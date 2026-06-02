<?php

namespace Manik\Neuro\Speech;

use Illuminate\Contracts\Container\Container;
use Manik\Neuro\Contracts\SpeechDriver;
use Manik\Neuro\Exceptions\NeuroException;

class SpeechManager
{
    protected array $drivers = [];

    public function __construct(
        protected ?Container $app = null,
    ) {}

    public function driver(?string $name = null): SpeechDriver
    {
        $name ??= config('ai.defaults.speech', 'openai');

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

    protected function resolve(string $name): SpeechDriver
    {
        $config = config("ai.speech.{$name}");

        if ($config === null) {
            throw NeuroException::invalidProvider($name, 'Speech');
        }

        return match ($config['driver']) {
            'openai' => new Drivers\OpenAISpeechDriver($config),
            default => throw NeuroException::invalidProvider($name, 'Speech'),
        };
    }
}
