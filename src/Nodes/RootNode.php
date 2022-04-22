<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

class RootNode extends ObjectNode
{
    /**
     * Constructs ObjectNode that sits at root.
     */
    public function __construct()
    {
        parent::__construct('');
    }

    /**
     * @return CompoundNode
     */
    public function end(): CompoundNode
    {
        return $this;
    }
}
