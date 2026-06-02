<?php

namespace Manik\Neuro\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $provider,
        public string $model,
        public array $response,
        public float $latency,
    ) {}
}
