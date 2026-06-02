<?php

namespace Manik\Neuro\LLM\Drivers;

use Illuminate\Support\Facades\Http;
use Manik\Neuro\Contracts\LLMDriver;

class GeminiDriver implements LLMDriver
{
    protected array $config;

    protected string $model;

    protected string $provider;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->model = $config['model'] ?? 'gemini-pro';
        $this->provider = $config['driver'] ?? 'gemini';
    }

    public function chat(array $messages, array $options = []): array
    {
        $response = Http::timeout($this->config['timeout'] ?? 30)
            ->post($this->generateUrl(), array_merge([
                'contents' => $this->formatMessages($messages),
            ], $options));

        $data = $response->throw()->json();

        return [
            'content' => $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'role' => 'model',
            'raw' => $data,
        ];
    }

    public function stream(array $messages, array $options = []): iterable
    {
        $response = Http::withHeaders(['Accept' => 'text/event-stream'])
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->streamUrl(), array_merge([
                'contents' => $this->formatMessages($messages),
            ], $options));

        $body = $response->throw()->body();

        foreach (explode("\n", $body) as $line) {
            $line = trim($line);

            if (str_starts_with($line, 'data: ')) {
                $data = substr($line, 6);

                $chunk = json_decode($data, true);

                if (isset($chunk['candidates'][0]['content']['parts'][0]['text'])) {
                    yield [
                        'content' => $chunk['candidates'][0]['content']['parts'][0]['text'],
                        'role' => 'model',
                    ];
                }
            }
        }
    }

    public function tools(array $messages, array $tools, array $options = []): array
    {
        $formatted = [];

        foreach ($tools as $tool) {
            $formatted[] = [
                'functionDeclarations' => [
                    [
                        'name' => $tool['function']['name'],
                        'description' => $tool['function']['description'] ?? '',
                        'parameters' => $tool['function']['parameters'] ?? [],
                    ],
                ],
            ];
        }

        $response = Http::timeout($this->config['timeout'] ?? 30)
            ->post($this->generateUrl(), array_merge([
                'contents' => $this->formatMessages($messages),
                'tools' => $formatted,
            ], $options));

        $data = $response->throw()->json();

        return [
            'content' => $data['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'role' => 'model',
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
        return rtrim($this->config['base_url'] ?? 'https://generativelanguage.googleapis.com/v1beta', '/');
    }

    protected function generateUrl(): string
    {
        return $this->baseUrl().'/models/'.$this->model.':generateContent?key='.$this->config['api_key'];
    }

    protected function streamUrl(): string
    {
        return $this->baseUrl().'/models/'.$this->model.':streamContent?key='.$this->config['api_key'];
    }

    protected function formatMessages(array $messages): array
    {
        return array_map(function (array $message): array {
            return [
                'role' => $message['role'] ?? 'user',
                'parts' => [
                    ['text' => $message['content'] ?? ''],
                ],
            ];
        }, $messages);
    }

    protected function parseToolCalls(array $data): array
    {
        $toolCalls = [];

        foreach ($data['candidates'][0]['content']['parts'] ?? [] as $part) {
            if (isset($part['functionCall'])) {
                $toolCalls[] = [
                    'id' => $part['functionCall']['name'],
                    'type' => 'function',
                    'function' => [
                        'name' => $part['functionCall']['name'],
                        'arguments' => json_encode($part['functionCall']['args'] ?? []),
                    ],
                ];
            }
        }

        return $toolCalls;
    }
}
