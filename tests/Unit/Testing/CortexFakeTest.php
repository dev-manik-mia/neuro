<?php

use Manik\Neuro\Testing\NeuroFake;
use Manik\Neuro\Contracts\LLMDriver;
use Manik\Neuro\Contracts\EmbeddingDriver;

it('implements LLMDriver contract', function () {
    $fake = new NeuroFake;

    expect($fake)->toBeInstanceOf(LLMDriver::class);
});

it('implements EmbeddingDriver contract', function () {
    $fake = new NeuroFake;

    expect($fake)->toBeInstanceOf(EmbeddingDriver::class);
});

it('returns fake chat response', function () {
    $fake = new NeuroFake;

    $response = $fake->chat([['role' => 'user', 'content' => 'Hi']]);

    expect($response)
        ->toHaveKey('content')
        ->toHaveKey('role')
        ->and($response['content'])->toBe('fake response')
        ->and($response['role'])->toBe('assistant');
});

it('returns fake stream response', function () {
    $fake = new NeuroFake;

    $stream = $fake->stream([['role' => 'user', 'content' => 'Hi']]);
    $output = '';
    foreach ($stream as $chunk) {
        $output .= $chunk['content'];
    }

    expect($output)->toBe('fake response');
});

it('returns fake tools response', function () {
    $fake = new NeuroFake;

    $response = $fake->tools([['role' => 'user', 'content' => 'Hi']], []);

    expect($response)
        ->toHaveKey('content')
        ->and($response['content'])->toBe('fake response');
});

it('returns fake embedding', function () {
    $fake = new NeuroFake;

    $result = $fake->embed('test text');

    expect($result)
        ->toHaveKey('embedding')
        ->toHaveKey('dimensions')
        ->toHaveKey('model')
        ->toHaveKey('provider')
        ->and($result['dimensions'])->toBe(1536)
        ->and(count($result['embedding']))->toBe(1536);
});

it('returns fake batch embeddings', function () {
    $fake = new NeuroFake;

    $results = $fake->embedBatch(['text one', 'text two']);

    expect($results)->toBeArray()->toHaveCount(2);
});

it('returns dimensions', function () {
    $fake = new NeuroFake;

    expect($fake->dimensions())->toBe(1536);
});

it('can set and get model', function () {
    $fake = new NeuroFake;

    $fake->setModel('gpt-4o');

    expect($fake->getModel())->toBe('gpt-4o');
});

it('can set and get provider', function () {
    $fake = new NeuroFake;

    $fake->setProvider('openai');

    expect($fake->getProvider())->toBe('openai');
});
