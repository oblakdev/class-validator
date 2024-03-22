<?php

namespace Oblak\ClassValidator\Interface;

interface ValidatorCallback
{
    /**
     * Return the message for the current validator
     *
     * @param mixed $value
     * @return string
     */
    public function getMessage(mixed $value): string;

    /**
     * Return the callback for the current validator
     *
     * @return callable(mixed $value, ?array $args = null, bool $single = false): bool
     */
    public function getCallback(): callable;
}
