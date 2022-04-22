<?php

declare(strict_types=1);

namespace Square\Hyrule;

use Closure;
use Square\Hyrule\Nodes\AbstractNode;
use Square\Hyrule\Nodes\RootNode;

class Hyrule
{
    /**
     * @return RootNode
     */
    public static function create(): RootNode
    {
        return new RootNode();
    }

    /**
     * @return PathExp
     */
    public static function pathExp(): PathExp
    {
        return new PathExp();
    }

    /**
     * @param bool $expression
     * @param callable $builder
     * @return callable|Closure
     */
    public static function if(bool $expression, callable $builder)
    {
        if ($expression) {
            return $builder;
        }

        return static fn() => null;
    }

    /**
     * @param bool $expression
     * @return callable|Closure
     */
    public static function requiredIf(bool $expression)
    {
        return self::if($expression, fn(AbstractNode $node) => $node->required());
    }
}
