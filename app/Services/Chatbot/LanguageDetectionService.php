<?php

namespace App\Services\Chatbot;

class LanguageDetectionService
{
    /**
     * Common phonetic Banglish terms used in emergency and health contexts
     */
    private const BANGLISH_PATTERNS = [
        '/\b(amar|amr|amader|apnar|tar|tader)\b/i',
        '/\b(baba|ma|bhai|vai|bon|chele|meye|baccha|shishu|rogi)\b/i',
        '/\b(kotha|bolte|partese|parche|parchen|na|nai|nei)\b/i',
        '/\b(buke|bukey|betha|byatha|bedona|chap)\b/i',
        '/\b(shash|shas|saash|nisshash|koshto|nite)\b/i',
        '/\b(rokto|rocto|porche|khikhoron|katte)\b/i',
        '/\b(obosh|oshar|matha|ghurche|pore|geche|gache)\b/i',
        '/\b(behesh|ogyan|gyan|harieche|unresponsive)\b/i',
        '/\b(ki|korbo|koren|hobe|hoche|hochhe|dorkar|lagbe)\b/i',
        '/\b(druto|tara|taratari|khub|beshi|emergency|bachao)\b/i',
        '/\b(khichuni|jhor|pura|pure|agune|jole)\b/i',
        '/\b(hospital|ambulance|thana|police)\b/i',
    ];

    /**
     * Detect language: 'bn' (Standard Bengali or Banglish) or 'en' (English)
     */
    public function detect(string $text, ?string $preferredLanguage = null): string
    {
        if ($preferredLanguage && in_array($preferredLanguage, ['bn', 'en'], true)) {
            return $preferredLanguage;
        }

        // 1. Check for Standard Bengali script (Unicode range U+0980 to U+09FF)
        if (preg_match('/[\x{0980}-\x{09FF}]/u', $text)) {
            return 'bn';
        }

        // 2. Check for Banglish phonetic keywords (matches at least 2 distinct patterns or 1 strong)
        $banglishMatches = 0;
        foreach (self::BANGLISH_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                $banglishMatches++;
            }
        }

        if ($banglishMatches >= 2 || preg_match('/\b(partese|parchen|korbo|kotha|bukey|betha)\b/i', $text)) {
            return 'bn';
        }

        return 'en';
    }

    /**
     * Determine if the input is Banglish (phonetic Bengali written in Latin letters)
     */
    public function isBanglish(string $text): bool
    {
        if (preg_match('/[\x{0980}-\x{09FF}]/u', $text)) {
            return false;
        }

        foreach (self::BANGLISH_PATTERNS as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}
