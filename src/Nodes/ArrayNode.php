<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;
use BadMethodCallException;
use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;
use LogicException;

class ArrayNode extends CompoundNode
{
    /**
     * @var AbstractNode|ObjectNode|ArrayNode|null
     */
    protected $each = null;

    /**
     * @param string $name
     * @param CompoundNode|null $parent
     */
    public function __construct(string $name, ?CompoundNode $parent = null)
    {
        $this->rules[] = 'array';
        parent::__construct($name, $parent);
    }

    /**
     * @param string|NodeType $type
     * @return ScalarNode|ArrayNode|ObjectNode|FileNode
     */
    public function each(NodeType|string $type): AbstractNode
    {
        $className = $this->resolveNodeClassName($type);
        $this->failIfTypeChanged($className);

        if (isset($this->each)) {
            // Return existing sub-node if already defined.
            return $this->each;
        }

        // Otherwise, create the sub-node of the desired type.
        $each = new $className('*', $this);
        // Help static analysis tools
        assert($each instanceof AbstractNode);
        return $this->each = $each;
    }

    /**
     * @return array<array<string|Rule>>
     */
    public function build(): array
    {
        $rules = parent::build();

        if (isset($this->each)) {
            $eachRules = $this->each->build();
            foreach ($eachRules as $k => $v) {
                $rules[sprintf('%s.%s', $this->name, $k)] = $v;
            }
        }

        return $rules;
    }

    /**
     * @param string $name
     * @return AbstractNode
     */
    public function get(string $name = '*'): AbstractNode
    {
        if ($name !== '*') {
            throw new InvalidArgumentException(sprintf(
                '%s can only be called with "*"',
                __METHOD__,
            ));
        }
        if (!$this->each instanceof AbstractNode) {
            throw new BadMethodCallException(sprintf(
                '%s called when element type has not been declared. Did you call each(<TYPE>) yet?',
                __METHOD__,
            ));
        }
        return $this->each;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name = '*'): bool
    {
        if ($name !== '*') {
            throw new InvalidArgumentException(sprintf(
                '%s can only be called with "*"',
                __METHOD__,
            ));
        }
        return isset($this->each);
    }

    /**
     * Checks if existing sub-node matches the desired type exactly. Throws an exception if the check fails.
     * @param string $className
     * @return void
     */
    private function failIfTypeChanged(string $className): void
    {
        if (isset($this->each) && $className !== get_class($this->each)) {
            throw new LogicException(sprintf(
                'Cannot re-define of the underlying type for %s to "%s". Already defined as %s.',
                static::class,
                $className,
                get_class($this->each),
            ));
        }
    }

    /**
     * @param NodeType|string $type
     * @return string
     */
    public function resolveNodeClassName(NodeType|string $type): string
    {
        if ($type instanceof NodeType) {
            return $type->nodeClassName();
        }

        $nodeType = NodeType::tryFrom($type);
        if ($nodeType instanceof NodeType) {
            trigger_error(sprintf('The use of short-hand type names is deprecated. Use the %s enum.', NodeType::class), E_USER_DEPRECATED);
            return $nodeType->nodeClassName();
        }

        if (!is_a($type, AbstractNode::class, true)) {
            throw new InvalidArgumentException(sprintf(
                'Expected type to be a value of enum %s, or a name of a class extending %s. Got "%s"',
                NodeType::class,
                AbstractNode::class,
                $type,
            ));
        }

        return $type;
    }
}
