<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;
use LogicException;
use Square\Hyrule\Build\LazyRuleStringify;
use Square\Hyrule\Path;
use Square\Hyrule\PathExp;
use Stringable;

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
     * @var array|string[]|LazyRuleStringify[]|Rule[]|Stringable[]
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
     * @return CompoundNode|ObjectNode|ArrayNode
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
        return $this->end();
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
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function requiredIf(string|PathExp|AbstractNode $path, string $value): self
    {
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
     * @param string $value
     * @return $this
     */
    public function requiredUnless(string|PathExp|AbstractNode $path, string $value): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('required_unless', [$path, $value]);
        } else {
            $rule = sprintf('required_unless:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function requiredWithout(string|PathExp|AbstractNode $path): self
    {
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
    public function requiredWith(string|PathExp|AbstractNode $path): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('required_with', [$path]);
        } else {
            $rule = sprintf('required_with:%s', $path);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function declinedIf(string|PathExp|AbstractNode $path, string $value): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('declined_if', [$path, $value]);
        } else {
            $rule = sprintf('declined_if:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function missingIf(string|PathExp|AbstractNode $path, string $value): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('missing_if', [$path, $value]);
        } else {
            $rule = sprintf('missing_if:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function acceptedIf(string|PathExp|AbstractNode $path, string $value): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('accepted_if', [$path, $value]);
        } else {
            $rule = sprintf('accepted_if:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function excludeIf(string|PathExp|AbstractNode $path, string $value): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('exclude_if', [$path, $value]);
        } else {
            $rule = sprintf('exclude_if:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function excludeUnless(string|PathExp|AbstractNode $path, string $value): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('exclude_unless', [$path, $value]);
        } else {
            $rule = sprintf('exclude_unless:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function excludeWith(string|PathExp|AbstractNode $path): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('exclude_with', [$path]);
        } else {
            $rule = sprintf('exclude_with:%s', $path);
        }
        $this->rules[] = $rule;
        return $this;
    }


    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function excludeWithout(string|PathExp|AbstractNode $path): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('exclude_without', [$path]);
        } else {
            $rule = sprintf('exclude_without:%s', $path);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function prohibitedIf(string|PathExp|AbstractNode $path, string $value): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('prohibited_if', [$path, $value]);
        } else {
            $rule = sprintf('prohibited_if:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function prohibitedUnless(string|PathExp|AbstractNode $path, string $value): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('prohibited_unless', [$path, $value]);
        } else {
            $rule = sprintf('prohibited_unless:%s,%s', $path, $value);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function prohibits(string|PathExp|AbstractNode $path): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('prohibits', [$path]);
        } else {
            $rule = sprintf('prohibits:%s', $path);
        }
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function same(string|PathExp|AbstractNode $path): self
    {
        if ($path instanceof PathExp || $path instanceof self) {
            $rule = new LazyRuleStringify('same', [$path]);
        } else {
            $rule = sprintf('same:%s', $path);
        }
        $this->rules[] = $rule;
        return $this;
    }


    /**
     * @param string|Rule|Stringable $rule
     * @return AbstractNode
     */
    public function rule(string|Rule|Stringable $rule): self
    {
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
                $rule = new LazyRuleStringify($ruleName, $arguments);
                break;
            }
        }

        if (!$rule instanceof LazyRuleStringify) {
            $rule = sprintf('%s:%s', $ruleName, implode(',', $arguments));
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
            } else if ($rule instanceof Stringable) {
                $rule = $rule->__toString();
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
    public function assignTo(mixed &$var): self
    {
        $var = $this;
        return $this;
    }
}
