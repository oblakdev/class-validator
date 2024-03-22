<?php

namespace Oblak\ClassValidator\Tests;

use Oblak\ClassValidator\Decorator\Common\IsOptional;
use Oblak\ClassValidator\Decorator\StringType\IsBooleanString;
use Oblak\ClassValidator\Decorator\Type\IsInt;

class DecoratedClass
{
    #[IsInt(validationOpts: [
        'groups' => ['**', 'strict'],
    ])]
    #[IsInt(
        validationOpts: [
            'groups' => ['strict'],
        ]
    )]
    public mixed $int;

    #[IsInt]
    #[IsBooleanString]
    public mixed $something;

    public mixed $noWhitelist;

    #[IsInt]
    #[IsOptional()]
    public ?int $nullable = null;

    public function __construct(...$args)
    {
        $this->int = $args[0];
        $this->something = $args[1];
        $this->noWhitelist = $args[2] ?? 'karina';
    }
}
