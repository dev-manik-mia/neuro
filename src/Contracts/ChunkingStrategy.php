<?php

namespace Manik\Neuro\Contracts;

interface ChunkingStrategy
{
    public function chunk(string $text, array $options = []): array;
}
