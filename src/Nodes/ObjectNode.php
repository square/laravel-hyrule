<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

use Illuminate\Contracts\Validation\Rule;
use Square\Hyrule\Build\LazyRuleStringify;
use Square\Hyrule\ObjectRule\DefaultKnownPropertiesRule;
use LogicException;

/**
 * @property bool $allowUnknownProperties
 */
class ObjectNode extends CompoundNode
{
    /**
     * @var array|AbstractNode[]
     */
    protected array $children = [];

    /**
     * @var bool
     */
    protected bool $allowUnknownProperties = false;

    /**
     * @var callable|null
     */
    protected static $defaultKnownPropertiesRule = null;

    /**
     * @var callable|null
     */
    protected $knownPropertiesRule = null;

    /**
     * @param string $name
     * @return StringNode
     */
    public function string(string $name): StringNode
    {
        return $this->registerNode(new StringNode($name, $this));
    }

    /**
     * @param string $name
     * @return IntegerNode
     */
    public function integer(string $name): IntegerNode
    {
        return $this->registerNode(new IntegerNode($name, $this));
    }

    /**
     * @param string $name
     * @return BooleanNode
     */
    public function boolean(string $name): BooleanNode
    {
        return $this->registerNode(new BooleanNode($name, $this));
    }

    /**
     * @param string $name
     * @return NumericNode
     */
    public function numeric(string $name): NumericNode
    {
        return $this->registerNode(new NumericNode($name, $this));
    }

    /**
     * @param string $name
     * @return FloatNode
     */
    public function float(string $name): FloatNode
    {
        return $this->registerNode(new FloatNode($name, $this));
    }

    /**
     * @param string $name
     * @return ArrayNode
     */
    public function array(string $name): ArrayNode
    {
        return $this->registerNode(new ArrayNode($name, $this));
    }

    /**
     * @param string $name
     * @return ObjectNode
     */
    public function object(string $name): ObjectNode
    {
        return $this->registerNode(new self($name, $this));
    }

    /**
     * @template Node of AbstractNode
     *
     * @param Node $node
     * @return Node
     */
    private function registerNode(AbstractNode $node)
    {
        $name = $node->name;
        if (array_key_exists($name, $this->children)) {
            $existing = $this->children[$name];
            if (get_class($existing) !== get_class($node)) {
                throw new LogicException(sprintf(
                    'Cannot address property "%s" as type %s. Already registered as type %s.',
                    $name,
                    get_class($node),
                    get_class($existing),
                ));
            }
            /** @var Node $existing */
            return $existing;
        }
        $this->children[$name] = $node;
        return $node;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this
     */
    public function stringWith(string $name, callable $callable): self
    {
        $this->string($name)
            ->with($callable);
        return $this;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this|self
     */
    public function integerWith(string $name, callable $callable): self
    {
        $this->integer($name)
            ->with($callable);
        return $this;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this|self
     */
    public function booleanWith(string $name, callable $callable): self
    {
        $this->boolean($name)
            ->with($callable);
        return $this;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this|self
     */
    public function numericWith(string $name, callable $callable): self
    {
        $this->numeric($name)
            ->with($callable);
        return $this;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this|self
     */
    public function floatWith(string $name, callable $callable): self
    {
        $this->float($name)
            ->with($callable);
        return $this;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this|self
     */
    public function arrayWith(string $name, callable $callable): self
    {
        $this->registerNode(new ArrayNode($name, $this))
            ->with($callable);
        return $this;
    }

    /**
     * @param string $name
     * @param callable $callable
     * @return $this|self
     */
    public function objectWith(string $name, callable $callable): self
    {
        $this->object($name)
            ->with($callable);
        return $this;
    }


    /**
     * @param bool $allow
     * @return $this
     */
    public function allowUnknownProperties(bool $allow = true): self
    {
        $this->allowUnknownProperties = $allow;
        return $this;
    }

    /**
     * Build rules array for use w/ Laravel's Validator factory.
     * @return array<array<string|Rule>>
     */
    public function build(): array
    {
        $rules = parent::build();
        if ($this->allowUnknownProperties || empty($this->children)) {
            array_unshift($rules[$this->name], 'array');
        } else {
            $knownPropertiesRule = $this->resolveKnownPropertiesRule();
            array_unshift($rules[$this->name], ...$knownPropertiesRule($this));
        }

        foreach ($this->children as $property => $node) {
            $childRules = $node->build();
            $propertyRules = $childRules[$property] ?? [];
            unset($childRules[$property]);
            $prefix = $this->name . '.';
            // If the name is empty (e.g. the root node), we don't want to join w/ the dot.
            if ($this->name === '') {
                $prefix = $this->name;
            }
            $rules[sprintf('%s%s', $prefix, $property)] = $propertyRules;
            foreach ($childRules as $k => $v) {
                $rules[sprintf('%s%s', $prefix, $k)] = $v;
            }
        }

        return $rules;
    }

    /**
     * @param string $name
     * @return AbstractNode|ScalarNode|ObjectNode|ArrayNode
     */
    public function get(string $name): AbstractNode
    {
        if (!isset($this->children[$name])) {
            throw new \RuntimeException(sprintf(
                'Object "%s" does not have a property with name "%s".',
                $this->name,
                $name,
            ));
        }
        return $this->children[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->children[$name]);
    }

    /**
     * @param string $name
     * @return static
     */
    public function remove(string $name): self
    {
        unset($this->children[$name]);
        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getDefinedPropertyNames(): array
    {
        return array_keys($this->children);
    }

    /**
     * @param callable $callable
     * @return ObjectNode
     */
    public function propertiesRule(callable $callable): self
    {
        $this->knownPropertiesRule = $callable;
        return $this;
    }

    /**
     * @return callable|DefaultKnownPropertiesRule
     */
    protected function resolveKnownPropertiesRule()
    {
        return $this->knownPropertiesRule ?? new DefaultKnownPropertiesRule();
    }
}
