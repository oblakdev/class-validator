<?php

namespace Oblak\ClassValidator\Decorator\Type;

use Oblak\ClassValidator\Decorator\BaseValidator;
use Oblak\ClassValidator\Enum\Validators;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class IsInt extends BaseValidator
{
    public function __construct(array $validationOpts = [])
    {
        parent::__construct(type: Validators::isInt, validationOpts: $validationOpts);
    }
}
