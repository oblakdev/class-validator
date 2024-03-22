<?php

namespace Oblak\ClassValidator;

use Oblak\ClassValidator\Decorator\Common\Whitelisted;
use Oblak\ClassValidator\Interface\ValidationHandler;
use Oblak\ClassValidator\Interface\ValidationMeta;

class Executor
{
    public function __construct(
        private readonly bool $skipMissing = false,
        private readonly bool $whitelist = false,
        private readonly array $groups = ['**'],
        private readonly bool $strictGroups = false,
        private readonly bool $noWhitelisted = false,
        private readonly bool $forbidUnknown = true,
        private readonly bool $stopAtFirstError = false,
        private readonly bool $always = false,
    ) {
    }

    protected function getValidationMetadata(\ReflectionClass $reflector): array
    {
        $metadata = [];

        foreach ($reflector->getProperties() as $property) {
            $propMeta = [
                'property'   => $property,
                'validators' => $this->checkGroups(
                    $this->getValidators($property),
                    $this->groups,
                    $this->strictGroups,
                    $this->always
                ),
            ];

            if (count($propMeta['validators']) === 0) {
                $propMeta['validators']['whitelisted'] = new Whitelisted($this->whitelist);
            }

            $metadata[] = $propMeta;
        }

        return $metadata;
    }

    protected function getValidators(\ReflectionProperty $property): array
    {
        $validators = $property->getAttributes(ValidationMeta::class, \ReflectionAttribute::IS_INSTANCEOF);
        $valNames   = array_map(fn($v) => $v->getName(), $validators);

        if (in_array('Oblak\ClassValidator\Decorator\Type\Whitelisted', $valNames, true)) {
            throw new \BadMethodCallException('Whitelist validator can only be used internally');
        }

        return $validators;
    }

    /**
     * @param array<\ReflectionAttribute> $vals
     * @return array<string, ValidationHandler>
     */
    protected function checkGroups(array $vals): array
    {
        $toKeep = [];

        foreach ($vals as $validator) {
            $opts = $this->getValidatorOpts($validator);

            if (!$this->groupsMatch($opts['groups'] ?? ['**'], $opts['always'] ?? true)) {
                continue;
            }

            $validator = $validator->newInstance();

            $toKeep[$validator->type->value] = $validator;
        }

        return array_filter($toKeep);
    }

    protected function groupsMatch($validatorGroups, $always): bool
    {
        if ($always && $this->always) {
            return true;
        }

        return $this->strictGroups
            ? $validatorGroups === $this->groups
            : (bool) array_intersect($validatorGroups, $this->groups);
    }

    protected function getValidatorOpts(\ReflectionAttribute $validator): array
    {
        $args = $validator->getArguments();
        $args = ['validationOpts'] ?? end($args);

        return is_array($args) ? $args : [];
    }

    public function validate(object $obj): bool|array
    {
        $result = [];
        $meta   = $this->getValidationMetadata(new \ReflectionClass($obj));

        foreach ($meta as ['property' => $property, 'validators' => $validators]) {
            if (!$this->validateProperty($property, $validators, $result, $obj) && $this->stopAtFirstError) {
                break;
            }
        }

        $result = array_filter($result, fn($r) => count($r['constraints']) > 0);

        return !$result ?: $result;
    }

    /**
     * Undocumented function
     *
     * @param  \ReflectionProperty $property
     * @param  array<ValidationHandler>               $validators
     * @param  array               $result
     * @param  object              $target
     * @return bool|array
     */
    protected function validateProperty(
        \ReflectionProperty $property,
        array $validators,
        array &$result,
        object &$target
    ) {
        $failed  = false;
        $current = [
            'target' => $target::class,
            'property' => $property->getName(),
            'value' => $property->isInitialized($target) ? $property->getValue($target) : null,
            'constraints' => [],
        ];

        if (null === $current['value']) {
            if ($this->skipMissing || in_array('optional', array_keys($validators), true)) {
                return !$failed;
            }

            $current['constraints']['required'] = 'Property is required.';
            $result[] = $current;

            return $failed;
        }

        foreach ($validators as $name => $validator) {
            $res = $validator->validate($current['value']);

            if (true === $res) {
                continue;
            }
            $failed = true;
            $current['constraints'][$name] = $res;
        }

        if ($failed) {
            $result[] = $current;

            return !$failed;
        }

        return !$failed;
    }
}
