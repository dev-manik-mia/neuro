<?php

namespace Manik\Neuro\LLM\Drivers;

use Illuminate\Support\Facades\Http;
use Manik\Neuro\Contracts\LLMDriver;

class AnthropicDriver implements LLMDriver
{
    protected array $config;

    protected string $model;

    protected string $provider;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->model = $config['model'] ?? 'claude-3-opus-20240229';
        $this->provider = $config['driver'] ?? 'anthropic';
    }

    public function chat(array $messages, array $options = []): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->config['api_key'],
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/messages', array_merge([
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $options['max_tokens'] ?? 1024,
            ], $options));

        $data = $response->throw()->json();

        return [
            'content' => $data['content'][0]['text'] ?? '',
            'role' => 'assistant',
            'raw' => $data,
        ];
    }

    public function stream(array $messages, array $options = []): iterable
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->config['api_key'],
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
            'Accept' => 'text/event-stream',
        ])
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/messages', array_merge([
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $options['max_tokens'] ?? 1024,
                'stream' => true,
            ], $options));

        $body = $response->throw()->body();

        foreach (explode("\n", $body) as $line) {
            $line = trim($line);

            if (str_starts_with($line, 'data: ')) {
                $data = substr($line, 6);

                $chunk = json_decode($data, true);

                if (isset($chunk['type']) && $chunk['type'] === 'content_block_delta' && isset($chunk['delta']['text'])) {
                    yield [
                        'content' => $chunk['delta']['text'],
                        'role' => 'assistant',
                    ];
                }
            }
        }
    }

    public function tools(array $messages, array $tools, array $options = []): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->config['api_key'],
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/messages', array_merge([
                'model' => $this->model,
                'messages' => $messages,
                'tools' => $tools,
                'max_tokens' => $options['max_tokens'] ?? 1024,
            ], $options));

        $data = $response->throw()->json();

        return [
            'content' => $data['content'][0]['text'] ?? '',
            'role' => 'assistant',
            'tool_calls' => $this->parseToolCalls($data),
            'raw' => $data,
        ];
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setProvider(string $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    protected function baseUrl(): string
    {
        return rtrim($this->config['base_url'] ?? 'https://api.anthropic.com/v1', '/');
    }

    protected function parseToolCalls(array $data): array
    {
        $toolCalls = [];

        foreach ($data['content'] ?? [] as $block) {
            if (isset($block['type']) && $block['type'] === 'tool_use') {
                $toolCalls[] = [
                    'id' => $block['id'],
                    'type' => 'function',
                    'function' => [
                        'name' => $block['name'],
                        'arguments' => json_encode($block['input']),
                    ],
                ];
            }
        }

        return $toolCalls;
    }
}
