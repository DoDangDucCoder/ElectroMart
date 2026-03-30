<?php

namespace App\Application\AI\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class OpenAiChatbotClient
{
    public function enabled(): bool
    {
        return (bool) config('chatbot.ai.enabled')
            && filled(config('services.openai.api_key'))
            && filled(config('services.openai.model'));
    }

    public function reply(string $message, array $history = []): ?string
    {
        if (! $this->enabled()) {
            return null;
        }

        $compiledHistory = collect($history)
            ->take(-6)
            ->map(function (array $item) {
                $sender = strtoupper((string) ($item['sender'] ?? 'BOT'));
                $text = trim((string) ($item['message'] ?? ''));

                return $text !== '' ? "{$sender}: {$text}" : null;
            })
            ->filter()
            ->implode("\n");

        $input = trim(implode("\n\n", array_filter([
            $compiledHistory !== '' ? "Lịch sử hội thoại gần đây:\n{$compiledHistory}" : null,
            "Tin nhắn mới của khách:\n{$message}",
        ])));

        try {
            $response = Http::acceptJson()
                ->withToken((string) config('services.openai.api_key'))
                ->timeout(20)
                ->post(rtrim((string) config('services.openai.base_url', 'https://api.openai.com/v1'), '/') . '/responses', [
                    'model' => (string) config('services.openai.model'),
                    'instructions' => (string) config('chatbot.ai.system_prompt'),
                    'input' => $input,
                ]);

            if (! $response->successful()) {
                return null;
            }

            $payload = $response->json();
            $outputText = trim((string) ($payload['output_text'] ?? ''));

            if ($outputText !== '') {
                return $outputText;
            }

            foreach ((array) ($payload['output'] ?? []) as $outputItem) {
                foreach ((array) ($outputItem['content'] ?? []) as $contentItem) {
                    if (($contentItem['type'] ?? null) === 'output_text' && ! empty($contentItem['text'])) {
                        return trim((string) $contentItem['text']);
                    }
                }
            }
        } catch (Throwable $exception) {
            report($exception);
        }

        return null;
    }
}
