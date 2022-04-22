<?php

declare(strict_types=1);

namespace Square\Hyrule;

use RuntimeException;
use Square\Hyrule\Nodes\AbstractNode;
use Square\Hyrule\Nodes\ArrayNode;
use Square\Hyrule\Nodes\ObjectNode;
use Square\Hyrule\Nodes\RootNode;

class Path
{
    private AbstractNode $node;

    /**
     * @param AbstractNode $node
     */
    public function __construct(AbstractNode $node)
    {
        $this->node = $node;
    }

    /**
     * @return Path
     */
    public function parent(): Path
    {
        if (!$this->node->hasParent()) {
            throw new RuntimeException('Cannot traverse further: there is no more parent.');
        }
        return new self($this->node->end());
    }

    /**
     * @param string $name
     * @return Path
     */
    public function get(string $name): Path
    {
        if (!$this->node instanceof ObjectNode && !$this->node instanceof ArrayNode) {
            throw new RuntimeException(sprintf('Cannot traverse down node of type %s.', get_class($this->node)));
        }
        return new self($this->node->get($name));
    }

    /**
     * Absolute path name of property in dot-notation.
     * @return string
     */
    public function pathName(): string
    {
        $path = [];
        $node = $this->node;
        do {
            array_unshift($path, $node->getName());
            if (!$node->hasParent()) {
                break;
            }
            $node = $node->getParent();
            if ($node instanceof RootNode) {
                // If we are bumped all the way up to the RootNode, we can abort. RootNodes do not have path-names.
                break;
            }
        } while ($node instanceof AbstractNode);
        return implode('.', $path);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->pathName();
    }
}
