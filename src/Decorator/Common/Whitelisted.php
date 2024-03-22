<?php

namespace Oblak\ClassValidator\Decorator\Common;

use Oblak\ClassValidator\Decorator\BaseValidator;
use Oblak\ClassValidator\Enum\Validators;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Whitelisted extends BaseValidator
{
    public function __construct(private bool $whitelisted = true)
    {
        parent::__construct(type: Validators::whitelist, validationOpts: []);
    }

    public function validate(mixed $value, ?array $args = null, ?bool $single = false): bool|string
    {
        return parent::validate($this->whitelisted, $args, true);
    }
}
