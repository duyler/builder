<?php

declare(strict_types=1);

namespace Duyler\Framework\Build\Action;

use Closure;
use Duyler\Framework\Build\AttributeInterface;

class Action
{
    private static ActionBuilder $builder;
    private string $id;
    private string | Closure $handler;
    private array $require = [];
    private array $bind = [];
    private array $providers = [];
    private array $alternates = [];
    private ?string $argument = null;
    private null | string | Closure $argumentFactory = null;
    private ?string $contract = null;
    private null | string | Closure $rollback = null;
    private bool $externalAccess = false;
    private bool $repeatable = false;
    private bool $lock = true;
    private ?string $triggeredOn = null;
    private bool $private = false;
    private array $sealed = [];
    private bool $silent = false;

    /** @var AttributeInterface[] */
    private array $attributes = [];

    public function __construct(ActionBuilder $builder)
    {
        static::$builder = $builder;
    }

    public static function build(string $id, string|Closure $handler): self
    {
        $action = new self(static::$builder);
        $action->id = $id;
        $action->handler = $handler;

        self::$builder->addAction($action);

        return $action;
    }

    public function require(string ...$require): self
    {
        $this->require = $require;
        return $this;
    }

    public function bind(array $bind): self
    {
        $this->bind = $bind;
        return $this;
    }

    public function providers(array $providers): self
    {
        $this->providers = $providers;
        return $this;
    }

    public function alternates(string ...$alternates): self
    {
        $this->alternates = $alternates;
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

    public function externalAccess(bool $externalAccess): self
    {
        $this->externalAccess = $externalAccess;
        return $this;
    }

    public function repeatable(bool $repeatable): self
    {
        $this->repeatable = $repeatable;
        return $this;
    }

    public function lock(bool $lock): self
    {
        $this->lock = $lock;
        return $this;
    }

    public function triggeredOn(string $triggeredOn): self
    {
        $this->triggeredOn = $triggeredOn;
        return $this;
    }

    public function private(bool $private): self
    {
        $this->private = $private;
        return $this;
    }

    public function sealed(array $sealed): self
    {
        $this->sealed = $sealed;
        return $this;
    }

    public function silent(bool $silent): self
    {
        $this->silent = $silent;
        return $this;
    }

    public function attributes(AttributeInterface ...$attributes): self
    {
        $this->attributes = $attributes;
        return $this;
    }

    public function get(string $property): mixed
    {
        return $this->{$property};
    }
}
