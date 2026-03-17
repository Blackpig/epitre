<?php

namespace BlackpigCreatif\Epitre\Support;

abstract class EpitreTemplate
{
    protected string $key;

    protected string $label;

    protected string $view;

    /** @var array<string, string> Maps '{token}' => 'Human-readable description' */
    protected array $tokens = [];

    /** @return array<string, string> Maps '{token}' => resolved value */
    abstract public function resolve(array $data): array;

    public function getKey(): string
    {
        return $this->key;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getView(): string
    {
        return $this->view;
    }

    /** @return array<string, string> */
    public function getTokens(): array
    {
        return $this->tokens;
    }
}
