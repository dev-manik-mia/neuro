<?php

use Manik\Neuro\Contracts\EmbeddingDriver;
use Manik\Neuro\Embedding\Drivers\OpenAIEmbeddingDriver;
use Manik\Neuro\Embedding\Drivers\OllamaEmbeddingDriver;
use Manik\Neuro\Embedding\Drivers\GeminiEmbeddingDriver;
use Manik\Neuro\Embedding\Drivers\MistralEmbeddingDriver;
use Manik\Neuro\Embedding\Drivers\CohereEmbeddingDriver;

$drivers = [
    'OpenAI' => OpenAIEmbeddingDriver::class,
    'Ollama' => OllamaEmbeddingDriver::class,
    'Gemini' => GeminiEmbeddingDriver::class,
    'Mistral' => MistralEmbeddingDriver::class,
    'Cohere' => CohereEmbeddingDriver::class,
];

$config = [
    'driver' => 'test',
    'api_key' => 'test-key',
    'base_url' => 'https://api.test.com/v1',
    'model' => 'test-embed-model',
    'dimensions' => 768,
    'timeout' => 30,
];

it('implements EmbeddingDriver contract', function (string $name, string $class) use ($config) {
    $driver = new $class($config);

    expect($driver)->toBeInstanceOf(EmbeddingDriver::class);
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));

it('can set and get model', function (string $name, string $class) use ($config) {
    $driver = new $class($config);

    $result = $driver->setModel('custom-embed-model');

    expect($result)->toBeInstanceOf($class)
        ->and($driver->getModel())->toBe('custom-embed-model');
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));

it('returns dimensions', function (string $name, string $class) use ($config) {
    $driver = new $class($config);

    expect($driver->dimensions())->toBeInt();
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));

it('returns default model from config', function (string $name, string $class) use ($config) {
    $driver = new $class($config);

    expect($driver->getModel())->toBe('test-embed-model');
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));
