<?php

namespace Manik\Neuro\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmbeddingCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $provider,
        public string $model,
        public string $text,
        public array $embedding,
        public int $dimensions,
    ) {}
}
