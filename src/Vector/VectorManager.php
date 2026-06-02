<?php

namespace Manik\Neuro\Vector;

use Illuminate\Contracts\Container\Container;
use Manik\Neuro\Contracts\VectorDriver;
use Manik\Neuro\Exceptions\NeuroException;

class VectorManager
{
    protected array $drivers = [];

    public function __construct(
        protected ?Container $app = null,
    ) {}

    public function driver(?string $name = null): VectorDriver
    {
        $name ??= config('ai.defaults.vector', 'qdrant');

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

    protected function resolve(string $name): VectorDriver
    {
        $config = config("ai.vector.{$name}");

        if ($config === null) {
            throw NeuroException::invalidProvider($name, 'Vector');
        }

        return match ($config['driver']) {
            'qdrant' => new Drivers\QdrantDriver($config),
            'pinecone' => new Drivers\PineconeDriver($config),
            'pgvector' => new Drivers\PgvectorDriver($config),
            'weaviate' => new Drivers\WeaviateDriver($config),
            'milvus' => new Drivers\MilvusDriver($config),
            'chroma' => new Drivers\ChromaDriver($config),
            default => throw NeuroException::invalidProvider($name, 'Vector'),
        };
    }
}
