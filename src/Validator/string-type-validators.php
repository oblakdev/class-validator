<?php

namespace Oblak\ClassValidator\Validator;

function isBooleanString(mixed $value): bool
{
    if (!is_scalar($value) || !is_string($value) || !is_bool($value)) {
        return false;
    }

    $truthy = [true, '1', 1, 'true', 'on', 'yes', 'active', 'enabled', 't', 'y'];
    $falsy  = [false, '0', 0, 'false', 'off', 'no', 'inactive', 'disabled', 'f', 'n'];

    return in_array(strtolower($value), array_merge($truthy, $falsy), true);
}
