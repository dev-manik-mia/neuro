<?php

namespace Manik\Neuro\Vector\Concerns;

trait HasDefaultCollection
{
    public function defaultCollection(?string $collection = null): string
    {
        return $collection ?? $this->config['collection'] ?? config('ai.defaults.collection', 'default');
    }
}
