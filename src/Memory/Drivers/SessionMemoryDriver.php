<?php

namespace Manik\Neuro\Memory\Drivers;

use Illuminate\Support\Facades\Session;
use Manik\Neuro\Contracts\MemoryDriver;

class SessionMemoryDriver implements MemoryDriver
{
    public function __construct(
        protected array $config = [],
    ) {}

    public function add(string $sessionId, array $message): void
    {
        $key = "ai_memory_{$sessionId}";
        $messages = Session::get($key, []);
        $messages[] = $message;
        Session::put($key, $messages);
    }

    public function get(string $sessionId, int $limit = 10): array
    {
        $key = "ai_memory_{$sessionId}";
        $messages = Session::get($key, []);
        $limit = $this->config['limit'] ?? $limit;

        return array_slice($messages, -$limit);
    }

    public function clear(string $sessionId): void
    {
        Session::forget("ai_memory_{$sessionId}");
    }

    public function delete(string $sessionId): void
    {
        Session::forget("ai_memory_{$sessionId}");
    }
}
