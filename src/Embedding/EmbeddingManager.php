<?php

namespace Manik\Neuro\Embedding;

use Illuminate\Contracts\Container\Container;
use Manik\Neuro\Contracts\EmbeddingDriver;
use Manik\Neuro\Exceptions\NeuroException;

class EmbeddingManager
{
    protected array $drivers = [];

    public function __construct(
        protected ?Container $app = null,
    ) {}

    public function driver(?string $name = null): EmbeddingDriver
    {
        $name ??= config('ai.defaults.embedding', 'openai');

        if (! isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->resolve($name);
        }

        return $this->drivers[$name];
    }

    public function extend(string $name, callable $resolver): static
    {
        $this->drivers[$name] = $resolver($this->app);

        return $this;
    }

    protected function resolve(string $name): EmbeddingDriver
    {
        $config = config("ai.embedding.{$name}");

        if ($config === null) {
            throw NeuroException::invalidProvider($name, 'Embedding');
        }

        return match ($config['driver']) {
            'openai' => new Drivers\OpenAIEmbeddingDriver($config),
            'ollama' => new Drivers\OllamaEmbeddingDriver($config),
            'gemini' => new Drivers\GeminiEmbeddingDriver($config),
            'mistral' => new Drivers\MistralEmbeddingDriver($config),
            'cohere' => new Drivers\CohereEmbeddingDriver($config),
            default => throw NeuroException::invalidProvider($name, 'Embedding'),
        };
    }
}
