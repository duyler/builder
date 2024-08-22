<?php

declare(strict_types=1);

namespace Duyler\Builder\Build;

class BuilderCollection
{
    /** @var BuilderInterface[] */
    private array $builders = [];

    public function addBuilder(BuilderInterface $builder): void
    {
        $this->builders[] = $builder;
    }

    public function getBuilders(): array
    {
        return $this->builders;
    }
}
