<?php //phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

use Oblak\ClassValidator\Error\ValidationFailed;
use Oblak\ClassValidator\Tests\DecoratedClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

use function Oblak\ClassValidator\validateOrThrow;

class ValidateTest extends TestCase
{
    #[Group('functions')]
    #[Group('unit')]
    #[Test]
    #[TestDox('Validates standard class')]
    public function basicValidation()
    {
        $obj = new DecoratedClass(1, 'aaa');

        $this->expectException(ValidationFailed::class);

        validateOrThrow($obj);
    }
}
