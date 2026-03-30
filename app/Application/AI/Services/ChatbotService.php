<?php

namespace App\Application\AI\Services;

class ChatbotService
{
    public function __construct(
        private readonly RuleBasedChatbotService $rules,
        private readonly OpenAiChatbotClient $openAi
    ) {
    }

    public function process(array $data): array
    {
        return $this->reply(
            (string) ($data['message'] ?? ''),
            $data['customer_id'] ?? null,
            $data['ip_address'] ?? null,
            (array) ($data['history'] ?? [])
        );
    }

    public function train(array $trainingData): bool
    {
        // The storefront assistant currently supports inference-only flows.
        unset($trainingData);

        return false;
    }

    public function predict(array $input): array
    {
        $reply = $this->reply(
            (string) ($input['message'] ?? ''),
            $input['customer_id'] ?? null,
            $input['ip_address'] ?? null,
            (array) ($input['history'] ?? [])
        );

        return [
            'prediction' => $reply['intent'] ?? 'fallback',
            'confidence' => ($reply['source'] ?? 'rule_based') === 'ai' ? 0.6 : 0.95,
            'alternatives' => [],
        ];
    }

    public function evaluate(array $testData): array
    {
        return [
            'accuracy' => 0.0,
            'precision' => 0.0,
            'recall' => 0.0,
            'f1_score' => 0.0,
            'sample_size' => count($testData),
            'mode' => 'inference_only',
        ];
    }

    public function reply(
        string $message,
        ?int $customerId = null,
        ?string $ipAddress = null,
        array $history = []
    ): array {
        $ruleReply = $this->rules->handle($message, $customerId, $ipAddress);

        if ($ruleReply !== null) {
            return $ruleReply;
        }

        $aiReply = $this->openAi->reply($message, $history);

        if ($aiReply !== null && $aiReply !== '') {
            return [
                'intent' => 'ai_assist',
                'response_type' => 'ai',
                'message' => $aiReply,
                'products' => [],
                'actions' => [
                    ['label' => 'Tìm sản phẩm', 'url' => url('/tim-kiem')],
                    ['label' => config('storefront.labels.contact_support', 'Liên hệ hỗ trợ'), 'url' => url('/lien-he')],
                ],
                'source' => 'ai',
            ];
        }

        return $this->rules->fallback($customerId, $ipAddress);
    }
}
