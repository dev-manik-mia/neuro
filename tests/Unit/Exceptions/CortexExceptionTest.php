<?php

use Manik\Neuro\Exceptions\NeuroException;

it('creates invalid provider exception', function () {
    $e = NeuroException::invalidProvider('unknown', 'LLM');

    expect($e->getMessage())->toBe("Invalid LLM provider: 'unknown'.");
});

it('creates unsupported feature exception', function () {
    $e = NeuroException::unsupportedFeature('streaming', 'test');

    expect($e->getMessage())->toBe("Feature 'streaming' is not supported by provider 'test'.");
});

it('creates configuration error exception', function () {
    $e = NeuroException::configurationError('Missing API key');

    expect($e->getMessage())->toBe('Missing API key');
});

it('creates API error exception', function () {
    $e = NeuroException::apiError('OpenAI', 'Rate limit exceeded');

    expect($e->getMessage())->toBe('[OpenAI] API error: Rate limit exceeded');
});

it('creates embedding failed exception', function () {
    $e = NeuroException::embeddingFailed('Text too long');

    expect($e->getMessage())->toBe('Embedding failed: Text too long');
});

it('creates vector store error exception', function () {
    $e = NeuroException::vectorStoreError('Connection refused');

    expect($e->getMessage())->toBe('Vector store error: Connection refused');
});
