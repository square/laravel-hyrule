<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;
use Square\Hyrule\Build\LazyRuleStringify;
use Square\Hyrule\Path;
use Square\Hyrule\PathExp;

/**
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode required()
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode gt($arg)
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode gte($arg)
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode lt($arg)
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode lte($arg)
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode max($arg)
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode min($arg)
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode nullable()
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode sometimes()
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode present()
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode timezone()
 * @method $this|AbstractNode|ArrayNode|ScalarNode|ObjectNode in(...$allowedValues)
 */
abstract class AbstractNode
{
    /**
     * @var array|string[]|LazyRuleStringify[]|Rule[]
     */
    protected array $rules = [];


    /**
     * @var string
     */
    protected string $name;

    /**
     * @var CompoundNode|null
     */
    protected ?CompoundNode $parent = null;

    /**
     *
     * @param string $name
     * @param CompoundNode|null $parent
     */
    public function __construct(string $name, ?CompoundNode $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }

    /**
     * @return CompoundNode
     */
    public function end(): CompoundNode
    {
        if (!isset($this->parent)) {
            throw new LogicException('Cannot ->end(): no more parent.');
        }
        return $this->parent;
    }

    /**
     * @return CompoundNode
     */
    public function getParent(): CompoundNode
    {
        return $this->parent;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function hasParent(): bool
    {
        return $this->parent instanceof self;
    }

    /**
     * @param mixed $path
     * @param string $argName
     * @return void
     */
    private function assertPathArgument($path, string $argName = 'path'): void
    {
        if (!is_string($path) && !$path instanceof PathExp && !$path instanceof self) {
            throw new InvalidArgumentException(sprintf(
                'Expected %s to be a string, or an instance of %s or %s',
                $argName,
                PathExp::class,
                __CLASS__,
            ));
        }
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function requiredIf($path, string $value): self
    {
        $this->assertPathArgument($path, 'argument #0');

        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('required_if', [$path, $value]);
        } else {
            $rule = sprintf('required_if:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function requiredWithout($path): self
    {
        $this->assertPathArgument($path, 'argument #0');

        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('required_without', [$path]);
        } else {
            $rule = sprintf('required_without:%s', $path);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function requiredWith($path): self
    {
        $this->assertPathArgument($path, 'argument #0');

        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('required_with', [$path]);
        } else {
            $rule = sprintf('required_with:%s', $path);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|Rule $rule
     * @return $this|AbstractNode|ScalarNode|ObjectNode|ArrayNode
     */
    public function rule($rule): self
    {
        if (!is_string($rule) && $rule instanceof Rule) {
            throw new InvalidArgumentException(sprintf(
                'Expected argument to be a string, or instance of %s.', Rule::class,
            ));
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param callable $callable
     * @return $this|AbstractNode|ScalarNode|ObjectNode|ArrayNode
     */
    public function with(callable $callable): self
    {
        $callable($this);
        return $this;
    }

    /**
     * Allows adding rules w/o defining them in the library e.g. ->min() ->gte(1), ->nullable()
     *
     * @param string $methodName
     * @param array|mixed[] $arguments
     * @return $this
     */
    public function __call(string $methodName, array $arguments)
    {
        $ruleName = Str::snake($methodName);
        if (empty($arguments)) {
            $this->rules[] = $ruleName;
            return $this;
        }

        $rule = $methodName;

        /**
         * Lets look for an instance of PathExp. If it exists, we'll wrap the rule in LazyRuleStringify.
         */
        foreach ($arguments as $arg) {
            if ($arg instanceof PathExp || $arg instanceof self) {
                $rule = new LazyRuleStringify($methodName, $arguments);
                break;
            }
        }

        if (!$rule instanceof LazyRuleStringify) {
            $rule = sprintf('%s:%s', $methodName, implode(',', $arguments));
        }

        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @return array<array<string|Rule>>
     */
    public function build(): array
    {
        /**
         * Some rules may contain PathExp, and would be wrapped in LazyRuleStringify.
         * This ensures that all rules are converted to string.
         */
        $rules = array_map(function ($rule) {
            if ($rule instanceof LazyRuleStringify) {
                $rule = (string) $rule->setNode($this)->stringify();
            } else if ($rule instanceof self) {
                $rule = (string) (new Path($rule))->pathName();
            }
            return $rule;
        }, $this->rules);
        return [
            $this->name => $rules,
        ];
    }

    /**
     * @param mixed $var
     * @return static
     */
    public function assignTo(&$var): self
    {
        $var = $this;
        return $this;
    }
}
