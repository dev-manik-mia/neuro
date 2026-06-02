<?php

namespace Manik\Neuro\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSending
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $provider,
        public string $model,
        public array $messages,
        public array $options,
    ) {}
}
