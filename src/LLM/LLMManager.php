<?php

namespace Manik\Neuro\LLM;

use Illuminate\Contracts\Container\Container;
use Manik\Neuro\Contracts\LLMDriver;
use Manik\Neuro\Exceptions\NeuroException;

class LLMManager
{
    protected array $drivers = [];

    public function __construct(
        protected ?Container $app = null,
    ) {}

    public function driver(?string $name = null): LLMDriver
    {
        $name ??= config('ai.defaults.llm', 'openai');

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

    protected function resolve(string $name): LLMDriver
    {
        $config = config("ai.llm.{$name}");

        if ($config === null) {
            throw NeuroException::invalidProvider($name, 'LLM');
        }

        return match ($config['driver']) {
            'openai' => new Drivers\OpenAIDriver($config),
            'anthropic' => new Drivers\AnthropicDriver($config),
            'gemini' => new Drivers\GeminiDriver($config),
            'ollama' => new Drivers\OllamaDriver($config),
            'grok' => new Drivers\GrokDriver($config),
            'mistral' => new Drivers\MistralDriver($config),
            'cohere' => new Drivers\CohereDriver($config),
            default => throw NeuroException::invalidProvider($name, 'LLM'),
        };
    }
}
