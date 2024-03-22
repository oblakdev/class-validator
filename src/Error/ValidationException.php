<?php

namespace Oblak\ClassValidator\Error;

use JsonSerializable;

abstract class ValidationException extends \Exception implements JsonSerializable
{
    /**
     * Target of the validation.
     *
     * @var class-string
     */
    protected string $target;

    /**
     * Property of the target that failed validation.
     *
     * @var string
     */
    protected string $property;

    /**
     * Value of the property that failed validation.
     *
     * @var mixed
     */
    protected mixed $value;

    /**
     * Constraints that failed validation.
     *
     * @var array<string, string|array<string>>
     */
    protected array $constraints;

    /**
     * Create a new instance from a validation result.
     *
     * @param  array<int, array{
     *     target: string,
     *     property: string,
     *     value: mixed,
     *     constraints: array<string, string|array<string>>
     * }> $result The validation result.
     * @return static
     */
    public static function fromValidationResult(array $result, ?\Throwable $previous = null): ?static
    {
        $instance = null;

        foreach ($result as $error) {
            $childException = static::fromValidationResult($error['children'] ?? [], $previous);

            $instance = new static(
                $error['target'],
                $error['property'],
                $error['value'],
                $error['constraints'],
                $childException ?? $previous
            );

            $previous = $instance;
        }

        return $instance;
    }

    public function __construct(
        string $target,
        string $property,
        mixed $value,
        array $constraints,
        ?\Throwable $previous = null
    ) {
        $this->target      = $target;
        $this->property    = $property;
        $this->value       = $value;
        $this->constraints = $constraints;

        parent::__construct($this->getFormattedMessage($previous), 0, $previous);
    }

    public function getFormattedMessage(?ValidationException $e): string
    {
        $previousMsg = $e?->getFormattedMessage($e?->getPrevious()) ?? $this->getInitialMessage();

        return $previousMsg . $this->getCurrentMessage();
    }

    protected function getInitialMessage(): string
    {
        return "Validation failed for {$this->target}\n";
    }

    protected function getCurrentMessage(): string
    {
        $msg  = "Property {$this->property} failed validation with constraints: ";
        $msg .= implode(', ', array_keys($this->constraints)) . "\n";

        foreach ($this->constraints as $messages) {
            $msg .= is_array($messages)
                ? implode("\n", array_map($this->formatConstraintMessage(...), $messages, array_keys($messages)))
                : $this->formatConstraintMessage($messages);
            $msg .= "\n";
        }

        return $msg;
    }

    protected function formatConstraintMessage(string $message, ?int $index = null): string
    {
        $repl = $index ? "$this->value[$index]" : $this->value;

        return ' - ' . str_replace('$value', $repl, $message);
    }

    public function jsonSerialize(): mixed
    {
        $arr = [];

        $curr = $this;

        while ($curr) {
            $arr[] = [
                'target'      => $curr->target,
                'property'    => $curr->property,
                'value'       => $curr->value,
                'constraints' => $curr->constraints,
            ];
            $curr = $curr->getPrevious();
        }

        return $arr;
    }
}
