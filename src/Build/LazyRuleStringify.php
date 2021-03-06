<?php

declare(strict_types=1);

namespace Square\Hyrule\Build;

use RuntimeException;
use Square\Hyrule\Nodes\AbstractNode;
use Square\Hyrule\Path;
use Square\Hyrule\PathExp;

/**
 * Understands how to construct rule specs when a PathExp instance is present among the arg list.
 * Rule name + arg list => string conversion is done at build-time.
 */
class LazyRuleStringify
{
    /**
     * @var string
     */
    private string $ruleName;

    /**
     * @var AbstractNode
     */
    private AbstractNode $node;

    /**
     * @var array|mixed[]
     */
    private array $arguments;

    /**
     * @param string $ruleName
     * @param array|mixed[] $arguments
     */
    public function __construct(string $ruleName, array $arguments)
    {
        $this->ruleName = $ruleName;
        $this->arguments = $arguments;
    }

    /**
     * @param AbstractNode $node
     * @return self
     */
    public function setNode(AbstractNode $node): self
    {
        $this->node = $node;
        return $this;
    }

    /**
     * @return string
     */
    public function stringify(): string
    {
        $args = [];
        foreach ($this->arguments as $arg) {
            if ($arg instanceof PathExp) {
                if (!isset($this->node)) {
                    throw new RuntimeException('Cannot construct path. Node is not set.');
                }
                $arg = new Path($arg->traverse($this->node));
            }
            if ($arg instanceof AbstractNode) {
                $arg = new Path($arg);
            }
            $args[] = (string) $arg;
        }
        return sprintf('%s:%s', $this->ruleName, implode(',', $args));
    }
}
