<?php

namespace Manik\Neuro\Memory;

use Illuminate\Contracts\Container\Container;
use Manik\Neuro\Contracts\MemoryDriver;
use Manik\Neuro\Memory\Drivers\ConversationMemoryDriver;
use Manik\Neuro\Memory\Drivers\PersistentMemoryDriver;
use Manik\Neuro\Memory\Drivers\SessionMemoryDriver;

class MemoryManager
{
    protected array $drivers = [];

    public function __construct(
        protected ?Container $app = null,
    ) {}

    public function driver(?string $name = null): MemoryDriver
    {
        $name ??= config('ai.memory.default', 'session');

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

    protected function resolve(string $name): MemoryDriver
    {
        $config = config("ai.memory.drivers.{$name}");

        return match ($config['driver']) {
            'session' => new SessionMemoryDriver($config),
            'conversation' => new ConversationMemoryDriver($config),
            'persistent' => new PersistentMemoryDriver($config),
            default => throw new \InvalidArgumentException("Unknown memory driver: {$name}"),
        };
    }
}
