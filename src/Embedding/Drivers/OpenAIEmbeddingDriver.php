<?php

namespace Manik\Neuro\Embedding\Drivers;

use Illuminate\Support\Facades\Http;
use Manik\Neuro\Contracts\EmbeddingDriver;

class OpenAIEmbeddingDriver implements EmbeddingDriver
{
    protected array $config;

    protected string $model;

    protected string $provider;

    protected int $dimensionCount = 0;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->model = $config['model'] ?? 'text-embedding-3-small';
        $this->provider = $config['driver'] ?? 'openai';
    }

    public function embed(string $text): array
    {
        $response = Http::withToken($this->config['api_key'])
            ->timeout($this->config['timeout'] ?? 30)
            ->post($this->baseUrl().'/embeddings', [
                'input' => $text,
                'model' => $this->model,
            ]);

        $data = $response->throw()->json();

        $embedding = $data['data'][0]['embedding'];
        $this->dimensionCount = count($embedding);

        return [
            'embedding' => $embedding,
            'dimensions' => $this->dimensionCount,
            'model' => $this->model,
            'provider' => $this->provider,
        ];
    }

    public function embedBatch(array $texts): array
    {
        return array_map(fn (string $text) => $this->embed($text), $texts);
    }

    public function dimensions(): int
    {
        return $this->dimensionCount;
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

    protected function baseUrl(): string
    {
        return rtrim($this->config['base_url'] ?? 'https://api.openai.com/v1', '/');
    }
}
