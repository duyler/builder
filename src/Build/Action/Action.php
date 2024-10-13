<?php

declare(strict_types=1);

namespace Duyler\Builder\Build\Action;

use Closure;
use Duyler\Builder\Build\AttributeInterface;
use UnitEnum;

final class Action
{
    private static ActionBuilder $builder;
    private string|UnitEnum $id;
    private string | Closure $handler;
    private array $require = [];
    private array $triggerFor = [];
    private array $config = [];
    private array $alternates = [];
    private ?string $argument = null;
    private null | string | Closure $argumentFactory = null;
    private ?string $contract = null;
    private null | string | Closure $rollback = null;
    private bool $externalAccess = true;
    private bool $repeatable = false;
    private bool $lock = true;
    private int $retries = 0;
    private array $listen = [];
    private bool $private = false;
    private array $sealed = [];
    private bool $silent = false;
    private bool $flush = false;

    /** @var array<string|int, mixed> */
    private array $labels = [];

    /** @var AttributeInterface[] */
    private array $attributes = [];

    public function __construct(ActionBuilder $builder)
    {
        static::$builder = $builder;
    }

    public static function create(null|string|UnitEnum $id = null): self
    {
        $action = new self(static::$builder);
        $action->id = $id ?? 'anonymous@' . spl_object_hash($action);
        $action->handler = function () {};

        self::$builder->addAction($action);

        return $action;
    }

    public function handler(string|Closure $handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    public function require(string|UnitEnum ...$actionId): self
    {
        $this->require = $actionId;
        return $this;
    }

    public function config(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function triggerFor(string|UnitEnum ...$actionId): self
    {
        $this->triggerFor = $actionId;
        return $this;
    }

    public function alternates(string|UnitEnum ...$alternates): self
    {
        $this->alternates = $alternates;
        return $this;
    }

    public function retries(int $retries): self
    {
        $this->retries = $retries;
        return $this;
    }

    public function argument(string $argument): self
    {
        $this->argument = $argument;
        return $this;
    }

    public function argumentFactory(string| Closure $argumentFactory): self
    {
        $this->argumentFactory = $argumentFactory;
        return $this;
    }

    public function contract(string $contract): self
    {
        $this->contract = $contract;
        return $this;
    }

    public function rollback(string|Closure $rollback): self
    {
        $this->rollback = $rollback;
        return $this;
    }

    public function externalAccess(bool $externalAccess = true): self
    {
        $this->externalAccess = $externalAccess;
        return $this;
    }

    public function repeatable(bool $repeatable = true): self
    {
        $this->repeatable = $repeatable;
        return $this;
    }

    public function lock(bool $lock = true): self
    {
        $this->lock = $lock;
        return $this;
    }

    public function listen(string|UnitEnum ...$listen): self
    {
        $this->listen = $listen;
        return $this;
    }

    public function private(bool $private = true): self
    {
        $this->private = $private;
        return $this;
    }

    public function sealed(string|UnitEnum ...$sealed): self
    {
        $this->sealed = $sealed;
        return $this;
    }

    public function silent(bool $silent = true): self
    {
        $this->silent = $silent;
        return $this;
    }

    public function labels(array $labels): self
    {
        $this->labels = $labels;
        return $this;
    }

    public function attributes(AttributeInterface ...$attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function flush(bool $flush = true): self
    {
        $this->flush = $flush;
        return $this;
    }

    public function get(string $property): mixed
    {
        return $this->{$property};
    }
}
