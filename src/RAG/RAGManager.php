<?php

namespace Manik\Neuro\RAG;

use Manik\Neuro\Embedding\EmbeddingManager;
use Manik\Neuro\LLM\LLMManager;
use Manik\Neuro\RAG\Ingestion\DocumentIngestion;
use Manik\Neuro\Vector\VectorManager;

class RAGManager
{
    protected string $collection;

    protected string $question;

    public function __construct(
        protected LLMManager $llm,
        protected EmbeddingManager $embedder,
        protected VectorManager $vectorStore,
    ) {}

    public function pipeline(?string $provider = null): RAGPipeline
    {
        return new RAGPipeline(
            $this->llm->driver($provider),
            $this->embedder->driver($provider),
            $this->vectorStore->driver($provider),
        );
    }

    public function ingestion(): DocumentIngestion
    {
        return new DocumentIngestion(
            $this->embedder->driver(),
            $this->vectorStore->driver(),
        );
    }

    public function collection(string $name): static
    {
        $this->collection = $name;

        return $this;
    }

    public function question(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function answer(): array
    {
        return $this->pipeline()
            ->collection($this->collection)
            ->question($this->question)
            ->answer();
    }
}
