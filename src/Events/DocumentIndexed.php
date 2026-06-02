<?php

namespace Manik\Neuro\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentIndexed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $collection,
        public array $document,
        public int $chunkCount,
    ) {}
}
