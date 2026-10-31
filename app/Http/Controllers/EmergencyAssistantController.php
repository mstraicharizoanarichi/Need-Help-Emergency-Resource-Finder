<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chatbot\SendMessageRequest;
use App\Models\ChatConversation;
use App\Services\Chatbot\EmergencyChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmergencyAssistantController extends Controller
{
    public function __construct(
        private EmergencyChatbotService $chatbotService
    ) {}

    /**
     * Dedicated Emergency Assistant Web View
     */
    public function index(Request $request): View
    {
        return view('emergency.assistant', [
            'initialLanguage' => app()->getLocale() === 'bn' ? 'bn' : 'bn',
        ]);
    }

    /**
     * Start a new chat session/conversation
     */
    public function initConversation(Request $request): JsonResponse
    {
        $lang = $request->input('language', 'bn');
        if (!in_array($lang, ['en', 'bn'], true)) {
            $lang = 'bn';
        }

        $conversation = $this->chatbotService->startConversation(
            sessionId: $request->session()->getId(),
            userId: Auth::id(),
            language: $lang
        );

        return response()->json([
            'status' => 'success',
            'conversation' => [
                'id' => $conversation->id,
                'session_id' => $conversation->session_id,
                'language' => $conversation->language,
                'severity_level' => $conversation->severity_level,
                'is_emergency_active' => $conversation->is_emergency_active,
            ],
        ]);
    }

    /**
     * Get details and message history of a conversation
     */
    public function getConversation(ChatConversation $conversation): JsonResponse
    {
        $conversation->load(['messages', 'assessment', 'incident']);

        return response()->json([
            'status' => 'success',
            'conversation' => $conversation,
        ]);
    }

    /**
     * Send a user message and receive conversational triage response
     */
    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $conversationId = $request->input('conversation_id');
        $conversation = null;

        if ($conversationId) {
            $conversation = ChatConversation::find($conversationId);
        }

        if (!$conversation) {
            $conversation = $this->chatbotService->startConversation(
                sessionId: $request->session()->getId(),
                userId: Auth::id(),
                language: $request->input('language', 'bn')
            );
        }

        $result = $this->chatbotService->processMessage(
            conversation: $conversation,
            messageText: $request->input('message'),
            latitude: $request->input('latitude') ? (float) $request->input('latitude') : null,
            longitude: $request->input('longitude') ? (float) $request->input('longitude') : null,
            requestedLang: $request->input('language')
        );

        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    /**
     * Trigger 1-Tap SOS directly from the chat context
     */
    public function triggerSos(Request $request, ChatConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'reporter_name' => ['nullable', 'string', 'max:150'],
            'reporter_phone' => ['nullable', 'string', 'max:50'],
        ]);

        $result = $this->chatbotService->triggerSosFromChat($conversation, $validated);

        return response()->json($result);
    }

    /**
     * Switch language in active conversation
     */
    public function switchLanguage(Request $request, ChatConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'in:en,bn'],
        ]);

        $conversation->update(['language' => $validated['language']]);

        return response()->json([
            'status' => 'success',
            'language' => $conversation->language,
        ]);
    }

    /**
     * Fetch nearby emergency hospital resources
     */
    public function getNearbyResources(Request $request): JsonResponse
    {
        $lat = $request->query('lat') ? (float) $request->query('lat') : null;
        $lng = $request->query('lng') ? (float) $request->query('lng') : null;
        $limit = (int) $request->query('limit', 5);

        $hospitals = $this->chatbotService->findNearbyHospitals($lat, $lng, $limit);

        return response()->json([
            'status' => 'success',
            'hospitals' => $hospitals,
        ]);
    }
}
