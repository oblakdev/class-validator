<?php

namespace Oblak\ClassValidator\Decorator;

use Oblak\ClassValidator\Decorator\BaseValidator;
use Oblak\ClassValidator\Interface\ValidatorCallback;

abstract class CustomValidator extends BaseValidator implements ValidatorCallback
{
    public function __construct(
        array $args = [],
        ?array $validationOpts = [],
    ) {
        parent::__construct(type: $this, args:$args, validationOpts: $validationOpts);
    }
}
