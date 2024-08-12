<?php

namespace Square\Hyrule\Tests;

use PHPUnit\Framework\TestCase;
use Square\Hyrule\Nodes\AbstractNode;
use Square\Hyrule\Nodes\NodeType;

class NodeTypeTest extends TestCase
{
    public function testAllCasesResolveAbstractNodeTypes()
    {
        foreach (NodeType::cases() as $nodeType) {
            $className = $nodeType->nodeClassName();
            $this->assertTrue(
                is_a($className, AbstractNode::class, true),
                sprintf('Expected (%s)#nodeClassName() to return a class of type %s. Got %s', get_class($nodeType), AbstractNode::class, $className),
            );
        }
    }
}