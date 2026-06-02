<?php

namespace Manik\Neuro\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Application;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Manik\Neuro\RAG\Document;
use Manik\Neuro\RAG\RAGManager;

class ChunkingJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $content,
        public string $collection,
        public array $options = [],
    ) {}

    public function handle(Application $app): void
    {
        $rag = $app->make(RAGManager::class);
        $document = new Document($this->content);
        $rag->chunk($document, $this->collection, $this->options);
    }
}
