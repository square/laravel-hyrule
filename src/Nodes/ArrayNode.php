<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

use BadMethodCallException;
use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;
use LogicException;

/**
 * @property bool $allowUnknownProperties
 */
class ArrayNode extends CompoundNode
{
    /**
     * @var AbstractNode|ObjectNode|ArrayNode|null
     */
    protected $each = null;

    /**
     * @var class-string<AbstractNode>[]
     */
    private array $typeMap = [
        'integer' => IntegerNode::class,
        'string' => StringNode::class,
        'numeric' => NumericNode::class,
        'float' => FloatNode::class,
        'boolean' => BooleanNode::class,
        'array' => ArrayNode::class,
        'object' => ObjectNode::class,
        'scalar' => ScalarNode::class,
    ];

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
     * @param string $type
     * @return AbstractNode|ArrayNode|ObjectNode
     */
    public function each(string $type): AbstractNode
    {
        $expectedType = $this->typeMap[$type] ?? null;
        if ($expectedType === null) {
            $expectedType = $type;
            if (!is_a($expectedType, AbstractNode::class, true)) {
                throw new InvalidArgumentException(sprintf(
                    'Expected type to be one of [%s], or a class implementing %s. Got "%s"',
                    implode(', ', array_keys($this->typeMap)),
                    AbstractNode::class,
                    $type,
                ));
            }
        }

        if (isset($this->each)) {
            if ($expectedType !== get_class($this->each)) {
                throw new LogicException(sprintf(
                    'Cannot re-define of the underlying type for %s to "%s". Already defined as %s.',
                    self::class,
                    $type,
                    get_class($this->each),
                ));
            }
            return $this->each;
        }

        $each = new $expectedType('*', $this);
        $this->each = $each;
        return $each;
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
}
