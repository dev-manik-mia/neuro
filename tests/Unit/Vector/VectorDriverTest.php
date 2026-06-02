<?php

use Manik\Neuro\Contracts\VectorDriver;
use Manik\Neuro\Vector\Drivers\QdrantDriver;
use Manik\Neuro\Vector\Drivers\PineconeDriver;
use Manik\Neuro\Vector\Drivers\PgvectorDriver;
use Manik\Neuro\Vector\Drivers\WeaviateDriver;
use Manik\Neuro\Vector\Drivers\MilvusDriver;
use Manik\Neuro\Vector\Drivers\ChromaDriver;

$drivers = [
    'Qdrant' => QdrantDriver::class,
    'Pinecone' => PineconeDriver::class,
    'Pgvector' => PgvectorDriver::class,
    'Weaviate' => WeaviateDriver::class,
    'Milvus' => MilvusDriver::class,
    'Chroma' => ChromaDriver::class,
];

$config = [
    'driver' => 'test',
    'host' => 'http://localhost:8000',
    'api_key' => null,
    'timeout' => 30,
];

$pgvectorConfig = [
    'driver' => 'pgvector',
    'connection' => 'testing',
    'table' => 'vector_embeddings',
    'dimensions' => 1536,
    'timeout' => 30,
];

it('implements VectorDriver contract', function (string $name, string $class) use ($config, $pgvectorConfig) {
    $cfg = $name === 'Pgvector' ? $pgvectorConfig : $config;
    $driver = new $class($cfg);

    expect($driver)->toBeInstanceOf(VectorDriver::class);
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));
