<?php

namespace Oblak\ClassValidator\Interface;

interface ValidationHandler
{
    public function validate(mixed $value, ?array $validationOpts = null): bool|string|array;
}
