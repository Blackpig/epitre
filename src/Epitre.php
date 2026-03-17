<?php

namespace BlackpigCreatif\Epitre;

use BlackpigCreatif\Epitre\Support\EpitreTemplate;
use InvalidArgumentException;

class Epitre
{
    /** @var array<int, class-string<EpitreTemplate>> */
    protected array $templates = [];

    /**
     * Register a template class with the registry.
     *
     * @param  class-string<EpitreTemplate>  $class
     *
     * @throws InvalidArgumentException
     */
    public function register(string $class): void
    {
        if (! is_subclass_of($class, EpitreTemplate::class)) {
            throw new InvalidArgumentException("{$class} must extend EpitreTemplate.");
        }

        $this->templates[] = $class;
    }

    /** @return array<int, class-string<EpitreTemplate>> */
    public function all(): array
    {
        return $this->templates;
    }

    /** @return array<int, EpitreTemplate> */
    public function allInstances(): array
    {
        return array_map(fn (string $class) => new $class, $this->templates);
    }

    public function find(string $key): ?EpitreTemplate
    {
        foreach ($this->allInstances() as $template) {
            if ($template->getKey() === $key) {
                return $template;
            }
        }

        return null;
    }
}
