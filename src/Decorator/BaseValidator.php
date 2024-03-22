<?php

namespace Oblak\ClassValidator\Decorator;

use Closure;
use Oblak\ClassValidator\Interface\ValidationHandler;
use Oblak\ClassValidator\Interface\ValidationMeta;
use Oblak\ClassValidator\Interface\ValidatorCallback;

abstract class BaseValidator implements ValidationHandler, ValidationMeta
{
    public readonly bool $each;

    public readonly Closure $message;

    public readonly array $groups;

    public readonly bool $always;

    public readonly mixed $context;

    public function __construct(
        public readonly ValidatorCallback $type,
        protected readonly array $args = [],
        ?array $validationOpts = [],
    ) {
        [
            'each' => $this->each,
            'message' => $this->message,
            'groups' => $this->groups,
            'always' => $this->always,
            'context' => $this->context,
        ] = $this->parseValidationOpts(...$validationOpts);
    }

    protected function parseValidationOpts(
        ?bool $each = null,
        string|callable|null $message = null,
        ?array $groups = null,
        ?bool $always = null,
        mixed $context = null,
    ): array {
        $each    ??= false;
        $message ??= $this->type->getMessage(...);
        $groups  ??= ['**'];
        $always  ??= false;
        $context ??= null;

        $message = is_callable($message) ? $message : fn($v) => str_replace('$value', $v, $message);

        return compact('each', 'message', 'groups', 'always', 'context');
    }

    public function validate(mixed $value, ?array $args = null, ?bool $single = false): bool|string|array
    {
        $args = $args ?? $this->args;
        $cbfn = $this->type->getCallback();

        if (!$this->each || $single) {
            return $cbfn($value, $args, $single) ?: ($this->message)($value);
        }

        $value = array_filter(array_map(fn($v) => $this->validate($v, $args, true), $value), 'is_string');

        return !$value ?: $value;
    }
}
