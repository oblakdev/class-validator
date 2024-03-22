<?php

namespace Oblak\ClassValidator;

use Oblak\ClassValidator\Error\ValidationFailed;

function validate(object $obj, array $validatorOpts = []): bool
{
    return (new Executor(...$validatorOpts))->validate($obj) === true;
}

function validateOrThrow(object $obj, array $validatorOpts = []): bool
{
    $result = (new Executor(...$validatorOpts))->validate($obj);

    if (true !== $result) {
        throw ValidationFailed::fromValidationResult($result);
    }

    return true;
}
