<?php //phpcs:disable PSR1.Classes

use Oblak\ClassValidator\Decorator\Type\IsInt;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

final class IsIntTest extends TestCase
{
    #[Test]
    #[DataProvider('singleExamples')]
    #[TestDox('$_dataName $value validation returns $expected')]
    public function passesValidation(mixed $value, bool|string $expected): void
    {
        $int = new IsInt();

        $this->assertEquals($expected, $int->validate($value));
    }

    #[Test]
    #[DataProvider('arrayExamples')]
    #[TestDox('$_dataName is properly validated and returns $expected')]
    public function arrayValidation(array $value, array $options, bool|array $expected): void
    {
        $int = new IsInt($options);

        $this->assertEquals($expected, $int->validate($value));
    }

    public static function singleExamples(): array
    {
        return [
            'Integer' => [1, true],
            'Numeric' => ['33', '$value is not an integer.'],
            'Float'   => [1.1, '$value is not an integer.'],
        ];
    }

    public static function arrayExamples(): array
    {
        return [
            'Array<int>'   => [[1, 2, 3], ['each' => true], true],
            'Array<mixed>' => [[1, 2, '3'], ['each' => true], [2 => '$value is not an integer.']],
        ];
    }
}
