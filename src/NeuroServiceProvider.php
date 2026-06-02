<?php

namespace Manik\Neuro;

use Illuminate\Support\ServiceProvider;

class NeuroServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/neuro.php', 'ai');

        $this->app->singleton(LLM\LLMManager::class, function ($app) {
            return new LLM\LLMManager($app);
        });

        $this->app->singleton(Embedding\EmbeddingManager::class, function ($app) {
            return new Embedding\EmbeddingManager($app);
        });

        $this->app->singleton(Vector\VectorManager::class, function ($app) {
            return new Vector\VectorManager($app);
        });

        $this->app->singleton(Image\ImageManager::class, function ($app) {
            return new Image\ImageManager($app);
        });

        $this->app->singleton(Speech\SpeechManager::class, function ($app) {
            return new Speech\SpeechManager($app);
        });

        $this->app->singleton(RAG\RAGManager::class, function ($app) {
            return new RAG\RAGManager(
                $app->make(LLM\LLMManager::class),
                $app->make(Embedding\EmbeddingManager::class),
                $app->make(Vector\VectorManager::class),
            );
        });

        $this->app->singleton(Memory\MemoryManager::class, function ($app) {
            return new Memory\MemoryManager($app);
        });

        $this->app->singleton('neuro', function ($app) {
            return new AIClient(
                new NeuroManager(
                    $app->make(LLM\LLMManager::class),
                    $app->make(Embedding\EmbeddingManager::class),
                    $app->make(Vector\VectorManager::class),
                    $app->make(Image\ImageManager::class),
                    $app->make(Speech\SpeechManager::class),
                    $app->make(RAG\RAGManager::class),
                    $app->make(Memory\MemoryManager::class),
                ),
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/neuro.php' => config_path('ai.php'),
            ], 'neuro-config');

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
