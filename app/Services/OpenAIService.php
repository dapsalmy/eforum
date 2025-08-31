<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenAIService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.openai.com/v1';
    private array $models = [
        'gpt-4' => 'gpt-4',
        'gpt-3.5-turbo' => 'gpt-3.5-turbo',
        'gpt-4-turbo' => 'gpt-4-turbo-preview'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Generate content analysis for admin dashboard
     */
    public function analyzeContent(string $content, string $type = 'general'): array
    {
        $prompt = match($type) {
            'post' => "Analyze this forum post and provide insights on:\n1. Sentiment (positive/negative/neutral)\n2. Topic classification\n3. Engagement potential\n4. Moderation flags\n5. Quality score (1-10)\n\nPost: {$content}",
            'comment' => "Analyze this comment and provide:\n1. Sentiment analysis\n2. Relevance to parent post\n3. Potential for spam/trolling\n4. Moderation recommendations\n\nComment: {$content}",
            'user' => "Analyze this user's activity and provide:\n1. Engagement level\n2. Content quality\n3. Community contribution\n4. Risk assessment\n\nUser Activity: {$content}",
            default => "Analyze this content and provide insights on quality, sentiment, and engagement potential.\n\nContent: {$content}"
        };

        try {
            $response = $this->makeRequest('chat/completions', [
                'model' => $this->models['gpt-4'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert content analyst for a Nigerian professional forum. Provide concise, actionable insights.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 500,
                'temperature' => 0.3
            ]);

            return [
                'success' => true,
                'analysis' => $response['choices'][0]['message']['content'] ?? '',
                'model' => $response['model'] ?? '',
                'usage' => $response['usage'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Analysis Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate post content for admin
     */
    public function generatePost(string $topic, string $category, array $requirements = []): array
    {
        $prompt = "Generate a professional forum post about '{$topic}' for the '{$category}' category. ";
        $prompt .= "Requirements: " . implode(', ', $requirements) . "\n\n";
        $prompt .= "Make it engaging, informative, and relevant to the Nigerian professional community. ";
        $prompt .= "Include practical advice, examples, and encourage discussion.";

        try {
            $response = $this->makeRequest('chat/completions', [
                'model' => $this->models['gpt-4'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert content creator for a Nigerian professional forum. Create engaging, informative posts that encourage discussion.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 1000,
                'temperature' => 0.7
            ]);

            return [
                'success' => true,
                'title' => $this->extractTitle($response['choices'][0]['message']['content']),
                'content' => $response['choices'][0]['message']['content'],
                'model' => $response['model'] ?? '',
                'usage' => $response['usage'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Post Generation Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate user profile suggestions
     */
    public function generateUserProfile(string $username, array $interests = []): array
    {
        $prompt = "Generate a professional user profile for username '{$username}' with interests: " . implode(', ', $interests);
        $prompt .= "\n\nInclude:\n1. Professional bio\n2. Areas of expertise\n3. Professional goals\n4. Community interests\n";
        $prompt .= "Make it authentic and engaging for a Nigerian professional community.";

        try {
            $response = $this->makeRequest('chat/completions', [
                'model' => $this->models['gpt-3.5-turbo'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert at creating professional user profiles for Nigerian professionals.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 500,
                'temperature' => 0.6
            ]);

            return [
                'success' => true,
                'bio' => $response['choices'][0]['message']['content'],
                'model' => $response['model'] ?? '',
                'usage' => $response['usage'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Profile Generation Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Moderate content using AI
     */
    public function moderateContent(string $content): array
    {
        $prompt = "Analyze this content for moderation issues:\n\n{$content}\n\n";
        $prompt .= "Check for:\n1. Hate speech or discrimination\n2. Spam or promotional content\n3. Inappropriate language\n4. Personal attacks\n5. Offensive content\n";
        $prompt .= "Provide a risk score (1-10) and specific recommendations.";

        try {
            $response = $this->makeRequest('chat/completions', [
                'model' => $this->models['gpt-4'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a content moderator for a professional Nigerian forum. Be thorough but fair.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 300,
                'temperature' => 0.2
            ]);

            return [
                'success' => true,
                'moderation' => $response['choices'][0]['message']['content'],
                'model' => $response['model'] ?? '',
                'usage' => $response['usage'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Moderation Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate forum category suggestions
     */
    public function suggestCategories(array $existingCategories = []): array
    {
        $prompt = "Suggest new forum categories for a Nigerian professional community. ";
        $prompt .= "Existing categories: " . implode(', ', $existingCategories) . "\n\n";
        $prompt .= "Focus on:\n1. Professional development\n2. Visa and immigration\n3. Job opportunities\n4. Business networking\n5. Technology trends\n";
        $prompt .= "Provide category names and brief descriptions.";

        try {
            $response = $this->makeRequest('chat/completions', [
                'model' => $this->models['gpt-3.5-turbo'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert at organizing professional communities and forums.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 600,
                'temperature' => 0.5
            ]);

            return [
                'success' => true,
                'suggestions' => $response['choices'][0]['message']['content'],
                'model' => $response['model'] ?? '',
                'usage' => $response['usage'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Category Suggestions Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate automated responses for common questions
     */
    public function generateResponse(string $question, string $context = ''): array
    {
        $prompt = "Generate a helpful, professional response to this question: '{$question}'";
        if ($context) {
            $prompt .= "\n\nContext: {$context}";
        }
        $prompt .= "\n\nMake it informative, friendly, and encourage further discussion.";

        try {
            $response = $this->makeRequest('chat/completions', [
                'model' => $this->models['gpt-3.5-turbo'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful moderator for a Nigerian professional forum. Provide informative, friendly responses.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 400,
                'temperature' => 0.6
            ]);

            return [
                'success' => true,
                'response' => $response['choices'][0]['message']['content'],
                'model' => $response['model'] ?? '',
                'usage' => $response['usage'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Response Generation Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Analyze user engagement patterns
     */
    public function analyzeUserEngagement(array $userData): array
    {
        $prompt = "Analyze this user's engagement data and provide insights:\n\n";
        $prompt .= json_encode($userData, JSON_PRETTY_PRINT);
        $prompt .= "\n\nProvide insights on:\n1. Engagement level\n2. Content quality\n3. Community contribution\n4. Recommendations for improvement";

        try {
            $response = $this->makeRequest('chat/completions', [
                'model' => $this->models['gpt-4'],
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert at analyzing user engagement in online communities.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 500,
                'temperature' => 0.3
            ]);

            return [
                'success' => true,
                'analysis' => $response['choices'][0]['message']['content'],
                'model' => $response['model'] ?? '',
                'usage' => $response['usage'] ?? []
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI Engagement Analysis Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Make API request to OpenAI
     */
    private function makeRequest(string $endpoint, array $data): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/' . $endpoint, $data);

        if (!$response->successful()) {
            throw new \Exception('OpenAI API Error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Extract title from generated content
     */
    private function extractTitle(string $content): string
    {
        // Simple title extraction - first line or first sentence
        $lines = explode("\n", trim($content));
        $firstLine = trim($lines[0]);
        
        if (strlen($firstLine) > 100) {
            return substr($firstLine, 0, 100) . '...';
        }
        
        return $firstLine;
    }

    /**
     * Get API usage statistics
     */
    public function getUsageStats(): array
    {
        $cacheKey = 'openai_usage_stats';
        
        return Cache::remember($cacheKey, 3600, function () {
            try {
                $response = $this->makeRequest('usage', []);
                return [
                    'success' => true,
                    'usage' => $response
                ];
            } catch (\Exception $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        });
    }
}
