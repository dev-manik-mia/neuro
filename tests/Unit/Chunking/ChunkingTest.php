<?php

use Manik\Neuro\Contracts\ChunkingStrategy;
use Manik\Neuro\RAG\Chunking\FixedSizeChunking;
use Manik\Neuro\RAG\Chunking\RecursiveChunking;
use Manik\Neuro\RAG\Chunking\SemanticChunking;
use Manik\Neuro\RAG\Chunking\SlidingWindowChunking;

$sampleText = <<<'TEXT'
Laravel is a web application framework with expressive, elegant syntax.
We believe development must be an enjoyable and creative experience to be truly fulfilling.
Laravel attempts to take the pain out of development by easing common tasks used in most web projects.

The Laravel framework has a rich ecosystem. It includes features like Eloquent ORM, Blade templating,
and a powerful routing system. Laravel also provides tools for caching, queuing, and authentication.

Laravel Vapor is a serverless deployment platform for Laravel. It allows you to run your Laravel applications
at any scale with zero configuration required. Vapor integrates with AWS Lambda and other AWS services.
TEXT;

it('FixedSizeChunking implements ChunkingStrategy', function () {
    expect(new FixedSizeChunking)->toBeInstanceOf(ChunkingStrategy::class);
});

it('FixedSizeChunking splits text into chunks', function () use ($sampleText) {
    $chunker = new FixedSizeChunking;
    $chunks = $chunker->chunk($sampleText, ['chunk_size' => 100, 'chunk_overlap' => 20]);

    expect($chunks)->toBeArray()->not->toBeEmpty();
});

it('FixedSizeChunking respects chunk size', function () use ($sampleText) {
    $chunker = new FixedSizeChunking;
    $chunks = $chunker->chunk($sampleText, ['chunk_size' => 50, 'chunk_overlap' => 0]);

    foreach ($chunks as $chunk) {
        expect(strlen($chunk))->toBeLessThanOrEqual(55);
    }
});

it('RecursiveChunking implements ChunkingStrategy', function () {
    expect(new RecursiveChunking)->toBeInstanceOf(ChunkingStrategy::class);
});

it('RecursiveChunking splits text', function () use ($sampleText) {
    $chunker = new RecursiveChunking;
    $chunks = $chunker->chunk($sampleText, ['chunk_size' => 150, 'chunk_overlap' => 20]);

    expect($chunks)->toBeArray()->not->toBeEmpty();
});

it('SemanticChunking implements ChunkingStrategy', function () {
    expect(new SemanticChunking)->toBeInstanceOf(ChunkingStrategy::class);
});

it('SemanticChunking splits by paragraphs', function () use ($sampleText) {
    $chunker = new SemanticChunking;
    $chunks = $chunker->chunk($sampleText);

    expect($chunks)->toBeArray()->not->toBeEmpty();
});

it('SlidingWindowChunking implements ChunkingStrategy', function () {
    expect(new SlidingWindowChunking)->toBeInstanceOf(ChunkingStrategy::class);
});

it('SlidingWindowChunking creates overlapping windows', function () use ($sampleText) {
    $chunker = new SlidingWindowChunking;
    $chunks = $chunker->chunk($sampleText, ['window_size' => 100, 'stride' => 50]);

    expect($chunks)->toBeArray()->not->toBeEmpty();
});

it('all chunkers produce valid output', function (ChunkingStrategy $chunker) use ($sampleText) {
    $chunks = $chunker->chunk($sampleText);

    expect($chunks)->toBeArray()->not->toBeEmpty();
    foreach ($chunks as $chunk) {
        expect($chunk)->toBeString()->not->toBeEmpty();
    }
})->with([
    'fixed' => fn () => new FixedSizeChunking,
    'recursive' => fn () => new RecursiveChunking,
    'semantic' => fn () => new SemanticChunking,
    'sliding window' => fn () => new SlidingWindowChunking,
]);
