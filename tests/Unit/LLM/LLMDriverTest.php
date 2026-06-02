<?php

use Manik\Neuro\Contracts\LLMDriver;
use Manik\Neuro\LLM\Drivers\OpenAIDriver;
use Manik\Neuro\LLM\Drivers\AnthropicDriver;
use Manik\Neuro\LLM\Drivers\GeminiDriver;
use Manik\Neuro\LLM\Drivers\OllamaDriver;
use Manik\Neuro\LLM\Drivers\GrokDriver;
use Manik\Neuro\LLM\Drivers\MistralDriver;
use Manik\Neuro\LLM\Drivers\CohereDriver;

$drivers = [
    'OpenAI' => OpenAIDriver::class,
    'Anthropic' => AnthropicDriver::class,
    'Gemini' => GeminiDriver::class,
    'Ollama' => OllamaDriver::class,
    'Grok' => GrokDriver::class,
    'Mistral' => MistralDriver::class,
    'Cohere' => CohereDriver::class,
];

$config = [
    'driver' => 'test',
    'api_key' => 'test-key',
    'base_url' => 'https://api.test.com/v1',
    'model' => 'test-model',
    'temperature' => 0.7,
    'max_tokens' => 100,
    'timeout' => 30,
];

it('implements LLMDriver contract', function (string $name, string $class) use ($config) {
    $driver = new $class($config);

    expect($driver)->toBeInstanceOf(LLMDriver::class);
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));

it('can set and get model', function (string $name, string $class) use ($config) {
    $driver = new $class($config);

    $result = $driver->setModel('custom-model');

    expect($result)->toBeInstanceOf($class)
        ->and($driver->getModel())->toBe('custom-model');
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));

it('can set and get provider', function (string $name, string $class) use ($config) {
    $driver = new $class($config);

    $result = $driver->setProvider('custom-provider');

    expect($result)->toBeInstanceOf($class)
        ->and($driver->getProvider())->toBe('custom-provider');
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));

it('returns default model from config', function (string $name, string $class) use ($config) {
    $driver = new $class($config);

    expect($driver->getModel())->toBe('test-model');
})->with(array_map(fn ($name, $class) => [$name, $class], array_keys($drivers), array_values($drivers)));
