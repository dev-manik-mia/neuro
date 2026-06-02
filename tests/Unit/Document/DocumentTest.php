<?php

use Manik\Neuro\RAG\Document;

it('creates a document with content and metadata', function () {
    $doc = new Document('Hello world', ['source' => 'test'], '/path/to/file.txt');

    expect($doc->content())->toBe('Hello world')
        ->and($doc->metadata())->toBe(['source' => 'test'])
        ->and($doc->path())->toBe('/path/to/file.txt');
});

it('creates a document without path', function () {
    $doc = new Document('Just content', ['key' => 'value']);

    expect($doc->content())->toBe('Just content')
        ->and($doc->metadata())->toBe(['key' => 'value'])
        ->and($doc->path())->toBeNull();
});

it('creates a document with empty metadata', function () {
    $doc = new Document('Content');

    expect($doc->content())->toBe('Content')
        ->and($doc->metadata())->toBe([])
        ->and($doc->path())->toBeNull();
});
