<?php

namespace Tests\Feature\Chatbot;

use App\Models\ChatConversation;
use App\Models\EmergencyIncident;
use App\Services\Chatbot\EmergencyChatbotService;
use Tests\TestCase;

class ConversationalLogicAndFSMTest extends TestCase
{
    private EmergencyChatbotService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EmergencyChatbotService::class);
    }

    /**
     * TEST 1: User says severe bleeding, then cancels with "à¦¨à¦¾, à¦¶à§à¦§à§ à¦œà¦¾à¦¨à¦¾à¦° à¦œà¦¨à§à¦¯"
     * Expected: Assessment cancelled, no SOS, no EmergencyIncident, no acceptance message.
     */
    public function test_severe_bleeding_then_just_asking_cancels_assessment(): void
    {
        $conversation = $this->service->startConversation();

        // Turn 1: Emergency reported
        $res1 = $this->service->processMessage($conversation, 'প্রচণ্ড রক্তপাত হচ্ছে');
        $this->assertTrue($res1['is_emergency']);
        $this->assertEquals('P1', $res1['severity_level']);
        $this->assertStringContainsString('Severe arterial or heavy hemorrhage detected.', $res1['message']);

        // Turn 2: User clarifies they are just asking
        $res2 = $this->service->processMessage($conversation->fresh(), 'না, শুধু জানার জন্য');

        $this->assertFalse($res2['is_emergency']);
        $this->assertEquals('CANCELLATION', $res2['type']);
        $this->assertEquals(ChatConversation::STATE_EMERGENCY_CANCELLED, $res2['conversation_state']);
        $this->assertFalse($res2['requires_sos']);
        $this->assertStringNotContainsString('à¦œà¦°à§à¦°à¦¿ à¦ªà¦°à¦¿à¦¸à§à¦¥à¦¿à¦¤à¦¿ à¦—à§ƒà¦¹à§€à¦¤ à¦¹à§Ÿà§‡à¦›à§‡', $res2['message']);
        $this->assertStringContainsString('বাতিল', $res2['message']);

        // Assert no incident was created
        $this->assertEquals(0, EmergencyIncident::count());
    }

    /**
     * TEST 2: User says severe bleeding, bot asks question, user answers "nah"
     * Expected: Assessment cancelled.
     */
    public function test_severe_bleeding_then_nah_cancels_assessment(): void
    {
        $conversation = $this->service->startConversation();

        $res1 = $this->service->processMessage($conversation, 'প্রচণ্ড রক্তপাত হচ্ছে');
        $this->assertTrue($res1['is_emergency']);

        $res2 = $this->service->processMessage($conversation->fresh(), 'nah');

        $this->assertFalse($res2['is_emergency']);
        $this->assertEquals('CANCELLATION', $res2['type']);
        $this->assertEquals(ChatConversation::STATE_EMERGENCY_CANCELLED, $res2['conversation_state']);
        $this->assertFalse($res2['requires_sos']);
        $this->assertStringNotContainsString('à¦œà¦°à§à¦°à¦¿ à¦ªà¦°à¦¿à¦¸à§à¦¥à¦¿à¦¤à¦¿ à¦—à§ƒà¦¹à§€à¦¤ à¦¹à§Ÿà§‡à¦›à§‡', $res2['message']);
        $this->assertEquals(0, EmergencyIncident::count());
    }

    /**
     * TEST 3: User says severe bleeding, bot asks question, user confirms "à¦¹à§à¦¯à¦¾à¦, à¦à¦–à¦¨à§‹ à¦¹à¦šà§à¦›à§‡"
     * Expected: Continue assessment or confirm emergency.
     */
    public function test_severe_bleeding_then_affirmative_continues_emergency(): void
    {
        $conversation = $this->service->startConversation();

        $res1 = $this->service->processMessage($conversation, 'প্রচণ্ড রক্তপাত হচ্ছে');
        $this->assertTrue($res1['is_emergency']);

        $res2 = $this->service->processMessage($conversation->fresh(), 'হ্যাঁ, এখনো হচ্ছে');

        $this->assertTrue($res2['is_emergency']);
        $this->assertEquals('P1', $res2['severity_level']);
        $this->assertTrue($res2['requires_sos']);
    }

    /**
     * TEST 4: User says "hello"
     * Expected: GENERAL_GREETING, no emergency.
     */
    public function test_hello_produces_general_greeting_without_emergency(): void
    {
        $conversation = $this->service->startConversation();

        $res = $this->service->processMessage($conversation, 'hello');

        $this->assertEquals('GENERAL_GREETING', $res['type']);
        $this->assertFalse($res['is_emergency']);
        $this->assertFalse($res['requires_sos']);
        $this->assertEquals(ChatConversation::STATE_IDLE, $res['conversation_state']);
        $this->assertStringNotContainsString('à¦œà¦°à§à¦°à¦¿ à¦ªà¦°à¦¿à¦¸à§à¦¥à¦¿à¦¤à¦¿ à¦—à§ƒà¦¹à§€à¦¤ à¦¹à§Ÿà§‡à¦›à§‡', $res['message']);
        $this->assertNotEmpty($res['quick_replies']);
        $this->assertEquals(0, EmergencyIncident::count());
    }

    /**
     * TEST 5: User says "nah dokar nai"
     * Expected: NEGATIVE_RESPONSE / CANCELLATION, no emergency.
     */
    public function test_nah_dokar_nai_produces_cancellation_without_emergency(): void
    {
        $conversation = $this->service->startConversation();

        $res = $this->service->processMessage($conversation, 'nah dokar nai');

        $this->assertEquals('CANCELLATION', $res['type']);
        $this->assertFalse($res['is_emergency']);
        $this->assertFalse($res['requires_sos']);
        $this->assertStringNotContainsString('à¦œà¦°à§à¦°à¦¿ à¦ªà¦°à¦¿à¦¸à§à¦¥à¦¿à¦¤à¦¿ à¦—à§ƒà¦¹à§€à¦¤ à¦¹à§Ÿà§‡à¦›à§‡', $res['message']);
        $this->assertEquals(0, EmergencyIncident::count());
    }

    /**
     * TEST 6: User asks "stroke er symptoms ki?"
     * Expected: INFORMATION mode, no incident created.
     */
    public function test_stroke_symptoms_question_is_treated_as_information(): void
    {
        $conversation = $this->service->startConversation();

        $res = $this->service->processMessage($conversation, 'stroke er symptoms ki?');

        $this->assertEquals('INFORMATION', $res['type']);
        $this->assertFalse($res['is_emergency']);
        $this->assertFalse($res['requires_sos']);
        $this->assertEquals(ChatConversation::STATE_INFORMATION, $res['conversation_state']);
        $this->assertStringContainsString('FAST', $res['message']);
        $this->assertEquals(0, EmergencyIncident::count());
    }

    /**
     * TEST 7: User describes acute neurological signs: "à¦†à¦®à¦¾à¦° à¦¬à¦¾à¦¬à¦¾ à¦¹à¦ à¦¾à§Ž à¦•à¦¥à¦¾ à¦¬à¦²à¦¤à§‡ à¦ªà¦¾à¦°à¦›à§‡ à¦¨à¦¾ à¦à¦¬à¦‚ à¦¡à¦¾à¦¨ à¦¹à¦¾à¦¤ à¦¦à§à¦°à§à¦¬à¦²"
     * Expected: Possible neurological emergency, warning, follow-up questions, non-diagnostic.
     */
    public function test_acute_stroke_warning_signs_trigger_triage_assessment(): void
    {
        $conversation = $this->service->startConversation();

        $res = $this->service->processMessage($conversation, 'আমার বাবা হঠাৎ কথা বলতে পারছে না এবং ডান হাত দুর্বল');

        $this->assertTrue($res['is_emergency']);
        $this->assertEquals('P1', $res['severity_level']);
        $this->assertEquals('STROKE', $res['active_intent']);
        $this->assertTrue($res['requires_sos']);
        $this->assertNotEmpty($res['options']);
    }

    /**
     * TEST 8: User reports critical red flag: "à¦¸à§‡ à¦¶à§à¦¬à¦¾à¦¸ à¦¨à¦¿à¦šà§à¦›à§‡ à¦¨à¦¾"
     * Expected: CRITICAL CURRENT-MESSAGE RED FLAG, immediate guidance + action.
     */
    public function test_not_breathing_critical_override(): void
    {
        $conversation = $this->service->startConversation();

        $res = $this->service->processMessage($conversation, 'সে শ্বাস নিচ্ছে না');

        $this->assertTrue($res['is_emergency']);
        $this->assertEquals('P1', $res['severity_level']);
        $this->assertEquals('EMERGENCY_ACTION', $res['type']);
        $this->assertTrue($res['requires_sos']);
        $this->assertStringContainsString('Critical Respiratory Arrest / Airway Obstruction detected.', $res['message']);
        $this->assertStringContainsString('999', $res['message']);
    }

    /**
     * TEST 9: User says "à¦¨à¦¾ à¦¦à¦°à¦•à¦¾à¦° à¦¨à§‡à¦‡"
     * Expected: Cancellation, no SOS.
     */
    public function test_na_dorkar_nei_cancels_without_sos(): void
    {
        $conversation = $this->service->startConversation();

        $res = $this->service->processMessage($conversation, 'না দরকার নেই');

        $this->assertEquals('CANCELLATION', $res['type']);
        $this->assertFalse($res['is_emergency']);
        $this->assertFalse($res['requires_sos']);
        $this->assertEquals(0, EmergencyIncident::count());
    }

    /**
     * TEST 10: User says "just asking"
     * Expected: Information mode, no emergency.
     */
    public function test_just_asking_treated_as_information_or_cancellation(): void
    {
        $conversation = $this->service->startConversation();

        $res = $this->service->processMessage($conversation, 'just asking');

        $this->assertFalse($res['is_emergency']);
        $this->assertFalse($res['requires_sos']);
        $this->assertEquals(0, EmergencyIncident::count());
    }

    /**
     * Full Conversation Multi-Turn Sequence from User Prompt:
     * User: "à¦ªà§à¦°à¦šà¦£à§à¦¡ à¦°à¦•à§à¦¤à¦ªà¦¾à¦¤ à¦¹à¦šà§à¦›à§‡" -> assessment
     * User: "à¦¨à¦¾, à¦¶à§à¦§à§ à¦œà¦¾à¦¨à¦¾à¦° à¦œà¦¨à§à¦¯" -> cancelled
     * User: "nah dokar nai" -> no emergency acceptance
     * User: "hello" -> normal greeting, no emergency acceptance
     */
    public function test_full_conversation_lifecycle_resets_and_does_not_carry_emergency(): void
    {
        $conversation = $this->service->startConversation();

        // 1. User reports bleeding
        $r1 = $this->service->processMessage($conversation, 'প্রচণ্ড রক্তপাত হচ্ছে');
        $this->assertTrue($r1['is_emergency']);
        $this->assertEquals('P1', $r1['severity_level']);

        // 2. User says "à¦¨à¦¾, à¦¶à§à¦§à§ à¦œà¦¾à¦¨à¦¾à¦° à¦œà¦¨à§à¦¯"
        $r2 = $this->service->processMessage($conversation->fresh(), 'না, শুধু জানার জন্য');
        $this->assertFalse($r2['is_emergency']);
        $this->assertEquals('CANCELLATION', $r2['type']);
        $this->assertStringNotContainsString('à¦œà¦°à§à¦°à¦¿ à¦ªà¦°à¦¿à¦¸à§à¦¥à¦¿à¦¤à¦¿ à¦—à§ƒà¦¹à§€à¦¤ à¦¹à§Ÿà§‡à¦›à§‡', $r2['message']);

        // 3. User says "nah dokar nai"
        $r3 = $this->service->processMessage($conversation->fresh(), 'nah dokar nai');
        $this->assertFalse($r3['is_emergency']);
        $this->assertEquals('CANCELLATION', $r3['type']);
        $this->assertStringNotContainsString('à¦œà¦°à§à¦°à¦¿ à¦ªà¦°à¦¿à¦¸à§à¦¥à¦¿à¦¤à¦¿ à¦—à§ƒà¦¹à§€à¦¤ à¦¹à§Ÿà§‡à¦›à§‡', $r3['message']);

        // 4. User says "hello"
        $r4 = $this->service->processMessage($conversation->fresh(), 'hello');
        $this->assertFalse($r4['is_emergency']);
        $this->assertEquals('GENERAL_GREETING', $r4['type']);
        $this->assertStringNotContainsString('à¦œà¦°à§à¦°à¦¿ à¦ªà¦°à¦¿à¦¸à§à¦¥à¦¿à¦¤à¦¿ à¦—à§ƒà¦¹à§€à¦¤ à¦¹à§Ÿà§‡à¦›à§‡', $r4['message']);
        $this->assertStringContainsString('Need Help Emergency Health Assistant', $r4['message']);

        // 5. User asks educational question
        $r5 = $this->service->processMessage($conversation->fresh(), 'stroke er symptoms ki?');
        $this->assertFalse($r5['is_emergency']);
        $this->assertEquals('INFORMATION', $r5['type']);
        $this->assertStringContainsString('FAST', $r5['message']);

        // Ensure 0 incidents were accidentally spawned
        $this->assertEquals(0, EmergencyIncident::count());
    }
}

