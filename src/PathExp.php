<?php

declare(strict_types=1);

namespace Square\Hyrule;

use InvalidArgumentException;
use Square\Hyrule\Nodes\AbstractNode;
use Square\Hyrule\Nodes\ArrayNode;
use Square\Hyrule\Nodes\ObjectNode;

class PathExp
{
    /**
     * @var array|string[]
     */
    protected array $movements = [];

    /**
     *
     */
    private const MOVE_UP = '..';

    /**
     *
     */
    private const IN_PLACE = '.';

    /**
     * @return PathExp
     */
    public static function new(): PathExp
    {
        return new self();
    }

    /**
     * @return $this
     */
    public function parent()
    {
        $this->movements[] = self::MOVE_UP;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function get(string $name)
    {
        if ($name === self::MOVE_UP) {
            throw new InvalidArgumentException('Cannot select ".."');
        }
        $this->movements[] = $name;
        return $this;
    }

    /**
     * @param AbstractNode $node
     * @return AbstractNode
     */
    public function traverse(AbstractNode $node): AbstractNode
    {
        while (($movement = current($this->movements)) !== false) {
            switch ($movement) {
                case self::MOVE_UP:
                    $node = $node->end();
                    break;
                case self::IN_PLACE:
                    break;
                default:
                    if ($node instanceof ArrayNode) {
                        $node = $node->get('*');
                    } else {
                        $node = $node->get($movement);
                    }
                    break;
            }
            next($this->movements);
        }

        return $node;
    }

    /**
     * @param AbstractNode $node
     * @return ArrayNode|ObjectNode
     */
    public function findStartingPoint(AbstractNode $node): AbstractNode
    {
        while (!$node instanceof ObjectNode && !$node instanceof ArrayNode) {
            $node = $node->end();
        }
        return $node;
    }
}
