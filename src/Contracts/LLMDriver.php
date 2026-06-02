<?php

namespace Manik\Neuro\Contracts;

interface LLMDriver
{
    public function chat(array $messages, array $options = []): array;

    public function stream(array $messages, array $options = []): iterable;

    public function tools(array $messages, array $tools, array $options = []): array;

    public function setModel(string $model): static;

    public function getModel(): string;

    public function setProvider(string $provider): static;

    public function getProvider(): string;
}
