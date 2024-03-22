<?php

namespace Oblak\ClassValidator\Decorator\StringType;

use Oblak\ClassValidator\Decorator\BaseValidator;
use Oblak\ClassValidator\Enum\Validators;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class IsBooleanString extends BaseValidator
{
    public function __construct(array $validationOpts = [])
    {
        parent::__construct(type: Validators::isBooleanString, validationOpts: $validationOpts);
    }
}
