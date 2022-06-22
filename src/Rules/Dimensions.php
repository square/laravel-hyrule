<?php

namespace Square\Hyrule\Rules;

use Illuminate\Validation\Rules\Dimensions as DimensionsRule;
use Square\Hyrule\Nodes\FileNode;

/**
 * Extends the Laravel's built-in Dimensions helper and adds methods to support Hyrule's fluent API.
 */
class Dimensions extends DimensionsRule
{
    private FileNode $node;

    /**
     * @param FileNode $node
     * @param array $constraints
     */
    public function __construct(FileNode $node, array $constraints = [])
    {
        $this->node = $node;
        parent::__construct($constraints);
    }

    public function end(): FileNode
    {
        return $this->node;
    }
}