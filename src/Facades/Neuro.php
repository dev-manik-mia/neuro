<?php

namespace Manik\Neuro\Facades;

use Illuminate\Support\Facades\Facade;
use Manik\Neuro\AIClient;

/**
 * @method static \Manik\Neuro\LLM\LLMManager llm(?string $provider = null)
 * @method static \Manik\Neuro\Embedding\EmbeddingManager embedding(?string $provider = null)
 * @method static \Manik\Neuro\Vector\VectorManager vector(?string $provider = null)
 * @method static \Manik\Neuro\Image\ImageManager image(?string $provider = null)
 * @method static \Manik\Neuro\Speech\SpeechManager speech(?string $provider = null)
 * @method static \Manik\Neuro\RAG\RAGManager rag()
 * @method static \Manik\Neuro\Memory\MemoryManager memory(?string $driver = null)
 * @method static \Manik\Neuro\Agent\Agent agent(?string $provider = null)
 * @method static \Manik\Neuro\Testing\NeuroFake fake()
 * @method static \Manik\Neuro\AIClient chat()
 * @method static mixed stream()
 * @method static array embed()
 * @method static array vectorSearch()
 *
 * @see AIClient
 */
class Neuro extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'neuro';
    }

    public static function chat(): AIClient
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }
}
