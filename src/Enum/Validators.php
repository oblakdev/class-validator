<?php

namespace Oblak\ClassValidator\Enum;

use Oblak\ClassValidator\Interface\ValidatorCallback;
use Oblak\ClassValidator\Validator as ValidatorFns;

enum Validators: string implements ValidatorCallback
{
    // Optional, allow, whitelist
    case isOptional = 'optional';
    case allow = 'allow';
    case whitelist = 'whitelist';

    // String-type validators
    case isBooleanString = 'booleanString';

    case isInt = 'integer';

    /**
     * Return the callback for the current enum value.
     *
     * @return callable(mixed $value, ?array $args = null): bool
     */
    public function getCallback(): callable
    {
        return match ($this) {
            Validators::allow,
            Validators::isOptional      => fn() => true,
            Validators::whitelist       => fn($value) => true === $value,

            // String-type validators
            Validators::isBooleanString => ValidatorFns\isBooleanString(...),

            Validators::isInt           => ValidatorFns\isInt(...),
        };
    }

    public function getMessage(mixed $value): string
    {
        $msg = match ($this) {
            Validators::isOptional      => 'optional',
            Validators::allow           => 'not allowed',
            Validators::whitelist       => 'not whitelisted',

            // String-type validators
            Validators::isBooleanString => 'not a boolean string.',

            Validators::isInt           => 'not an integer.',
        };

        return '$value is ' . $msg;
    }

    // public function getMessage(mixed $value): string
    // {
    //     $value = match (true) {
    //         is_bool($value)                     => $value ? 'true' : 'false',
    //         is_scalar($value)                   => (string) $value,
    //         is_array($value),
    //         is_object($value),
    //         (bool) json_encode($value)          => json_encode($value),
    //         method_exists($value, '__toString') => $value->__toString(),
    //         default                             => serialize($value),
    //     };

    //     return $value . ' ' . $this->value;
    // }
}
