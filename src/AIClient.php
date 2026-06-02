<?php

namespace Manik\Neuro;

use Manik\Neuro\Contracts\EmbeddingDriver;
use Manik\Neuro\Contracts\ImageDriver;
use Manik\Neuro\Contracts\LLMDriver;
use Manik\Neuro\Contracts\SpeechDriver;
use Manik\Neuro\Contracts\VectorDriver;

class AIClient
{
    protected ?string $provider = null;

    protected ?string $model = null;

    protected array $messages = [];

    protected ?string $text = null;

    protected array $options = [];

    public function __construct(
        protected NeuroManager $manager,
    ) {}

    public function provider(string $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function message(string|array $message): static
    {
        if (is_string($message)) {
            $this->messages[] = ['role' => 'user', 'content' => $message];
        } else {
            $this->messages[] = $message;
        }

        return $this;
    }

    public function messages(array $messages): static
    {
        $this->messages = $messages;

        return $this;
    }

    public function text(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function options(array $options): static
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function chat(?string $provider = null): array
    {
        $driver = $this->resolveLLM($provider);
        $this->applyModel($driver);

        return $driver->chat($this->messages, $this->options);
    }

    public function stream(?string $provider = null): iterable
    {
        $driver = $this->resolveLLM($provider);
        $this->applyModel($driver);

        return $driver->stream($this->messages, $this->options);
    }

    public function embed(?string $provider = null): array
    {
        $driver = $this->resolveEmbedding($provider);
        $this->applyModel($driver);

        return $driver->embed($this->text ?? '');
    }

    public function embedBatch(array $texts, ?string $provider = null): array
    {
        $driver = $this->resolveEmbedding($provider);
        $this->applyModel($driver);

        return $driver->embedBatch($texts);
    }

    public function vectorSearch(?string $provider = null): array
    {
        $driver = $this->resolveVector($provider);

        return $driver->search(
            $this->options['collection'] ?? 'default',
            $this->options['vector'] ?? [],
            $this->options,
        );
    }

    public function image(?string $provider = null): array
    {
        $driver = $this->resolveImage($provider);
        $this->applyModel($driver);

        return $driver->generate($this->text ?? '', $this->options);
    }

    public function speech(?string $provider = null): string
    {
        $driver = $this->resolveSpeech($provider);
        $this->applyModel($driver);

        return $driver->synthesize($this->text ?? '', $this->options);
    }

    public function llm(?string $provider = null): LLMDriver
    {
        return $this->manager->llm($provider);
    }

    public function embedding(?string $provider = null): EmbeddingDriver
    {
        return $this->manager->embedding($provider);
    }

    public function vector(?string $provider = null): VectorDriver
    {
        return $this->manager->vector($provider);
    }

    public function memory(?string $driver = null): Memory\MemoryManager
    {
        return $this->manager->memory();
    }

    public function rag(): RAG\RAGManager
    {
        return $this->manager->rag();
    }

    public function agent(?string $provider = null): Agent\Agent
    {
        return $this->manager->agent($provider);
    }

    public function fake(): Testing\NeuroFake
    {
        $fake = new Testing\NeuroFake;

        $this->manager->llm->extend('openai', fn () => $fake);
        $this->manager->llm->extend('anthropic', fn () => $fake);
        $this->manager->llm->extend('gemini', fn () => $fake);
        $this->manager->llm->extend('ollama', fn () => $fake);
        $this->manager->embedding->extend('openai', fn () => $fake);
        $this->manager->embedding->extend('ollama', fn () => $fake);

        return $fake;
    }

    protected function resolveLLM(?string $provider = null): LLMDriver
    {
        return $this->manager->llm($provider ?? $this->provider);
    }

    protected function resolveEmbedding(?string $provider = null): EmbeddingDriver
    {
        return $this->manager->embedding($provider ?? $this->provider);
    }

    protected function resolveVector(?string $provider = null): VectorDriver
    {
        return $this->manager->vector($provider ?? $this->provider);
    }

    protected function resolveImage(?string $provider = null): ImageDriver
    {
        return $this->manager->image($provider ?? $this->provider);
    }

    protected function resolveSpeech(?string $provider = null): SpeechDriver
    {
        return $this->manager->speech($provider ?? $this->provider);
    }

    protected function applyModel($driver): void
    {
        if ($this->model !== null && method_exists($driver, 'setModel')) {
            $driver->setModel($this->model);
        }
    }
}
