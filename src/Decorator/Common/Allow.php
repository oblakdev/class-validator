<?php

namespace Oblak\ClassValidator\Decorator\Common;

use Oblak\ClassValidator\Decorator\BaseValidator;
use Oblak\ClassValidator\Enum\Validators;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Allow extends BaseValidator
{
    public function __construct()
    {
        parent::__construct(type: Validators::allow, validationOpts: []);
    }

    public function validate(mixed $value, ?array $args = null, ?bool $single = false): bool
    {
        return true;
    }
}
