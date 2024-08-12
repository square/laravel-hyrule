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
        return $this->buildAndAddRule('required_if', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function requiredIfAccepted(string|PathExp|AbstractNode $path): self
    {
        return $this->buildAndAddRule('required_if_accepted', [$path]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function requiredIfDeclined(string|PathExp|AbstractNode $path): self
    {
        return $this->buildAndAddRule('required_if_declined', [$path]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function requiredUnless(string|PathExp|AbstractNode $path, string $value): self
    {
        return $this->buildAndAddRule('required_unless', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function requiredWithout(string|PathExp|AbstractNode $path): self
    {
        return $this->buildAndAddRule('required_without', [$path]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function requiredWith(string|PathExp|AbstractNode $path): self
    {
        return $this->buildAndAddRule('required_with', [$path]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function declinedIf(string|PathExp|AbstractNode $path, string $value): self
    {
        return $this->buildAndAddRule('declined_if', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function missingIf(string|PathExp|AbstractNode $path, string $value): self
    {
        return $this->buildAndAddRule('missing_if', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function acceptedIf(string|PathExp|AbstractNode $path, string $value): self
    {
        return $this->buildAndAddRule('accepted_if', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function excludeIf(string|PathExp|AbstractNode $path, string $value): self
    {
        return $this->buildAndAddRule('exclude_if', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function excludeUnless(string|PathExp|AbstractNode $path, string $value): self
    {
        return $this->buildAndAddRule('exclude_unless', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function excludeWith(string|PathExp|AbstractNode $path): self
    {
        return $this->buildAndAddRule('exclude_with', [$path]);
    }


    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function excludeWithout(string|PathExp|AbstractNode $path): self
    {
        return $this->buildAndAddRule('exclude_without', [$path]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function prohibitedIf(string|PathExp|AbstractNode $path, string $value): self
    {
        return $this->buildAndAddRule('prohibited_if', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @param string $value
     * @return $this
     */
    public function prohibitedUnless(string|PathExp|AbstractNode $path, string $value): self
    {
        return $this->buildAndAddRule('prohibited_unless', [$path, $value]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function prohibits(string|PathExp|AbstractNode $path): self
    {
        return $this->buildAndAddRule('prohibits', [$path]);
    }

    /**
     * @param string|PathExp|AbstractNode $path
     * @return $this
     */
    public function same(string|PathExp|AbstractNode $path): self
    {
        return $this->buildAndAddRule('same', [$path]);
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

        return $this->buildAndAddRule($ruleName, $arguments);
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

    /**
     * Creates a rule string if all arguments are string. If an argument in a PathExp or a node, wrap in a rule object
     * that lazily resolves the path strings on build.
     *
     * @param string $ruleName
     * @param array<int,string|AbstractNode|PathExp> $ruleArguments
     * @return LazyRuleStringify|string
     */
    protected static function buildRule(string $ruleName, array $ruleArguments): LazyRuleStringify|string
    {
        if (count($ruleArguments) === 0) {
            return $ruleName;
        }
        foreach ($ruleArguments as $argument) {
            if ($argument instanceof PathExp || $argument instanceof self) {
                /**
                 * We'll wrap the rule in LazyRuleStringify if we find a PathExp or a node
                 */
                return new LazyRuleStringify($ruleName, $ruleArguments);
            }
        }

        // We are here because we didn't find any argument that should be lazily resolved.
        // Let's treat them all as strings now.
        // @phpstan-ignore-next-line
        return sprintf('%s:%s', $ruleName, implode(',', $ruleArguments));
    }

    /**
     * Build a rule via static::buildRule and add it to the rules list.
     *
     * @param string $ruleName
     * @param array<int,string|AbstractNode|PathExp> $arguments
     * @return $this
     */
    protected function buildAndAddRule(string $ruleName, array $arguments): self
    {
        $this->rules[] = static::buildRule($ruleName, $arguments);
        return $this;
    }

}
