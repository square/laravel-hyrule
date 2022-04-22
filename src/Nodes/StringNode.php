<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

class StringNode extends ScalarNode
{
    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['string'];
    }
}
