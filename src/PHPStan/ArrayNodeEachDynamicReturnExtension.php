<?php

namespace Square\Hyrule\PHPStan;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeDumper;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Square\Hyrule\Nodes\ArrayNode;
use Square\Hyrule\Nodes\BooleanNode;
use Square\Hyrule\Nodes\FileNode;
use Square\Hyrule\Nodes\FloatNode;
use Square\Hyrule\Nodes\IntegerNode;
use Square\Hyrule\Nodes\NumericNode;
use Square\Hyrule\Nodes\ObjectNode;
use Square\Hyrule\Nodes\StringNode;

/**
 * PHPStan extension so ArrayNode#each($type) calls can be statically-analyzed properly. This method
 * returns a child type of AbstractNode, depending on the $type value e.g. StringNode for "string".
 */
class ArrayNodeEachDynamicReturnExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ArrayNode::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'each';
    }

    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope): ?Type
    {
        if (empty($methodCall->getArgs())) {
            return null;
        }

        $arg = $methodCall->getArgs()[0];
        $d = new NodeDumper();
        $t = $scope->getType($arg->value);

        if (!$t instanceof ConstantStringType) {
            // TODO: Handle other cases.
            return null;
        }

        switch ($t->getValue()) {
            case "array":
                return new ObjectType(ArrayNode::class);
            case "object":
                return new ObjectType(ObjectNode::class);
            case "string":
                return new ObjectType(StringNode::class);
            case "integer":
                return new ObjectType(IntegerNode::class);
            case "float":
                return new ObjectType(FloatNode::class);
            case "numeric":
                return new ObjectType(NumericNode::class);
            case "boolean":
                return new ObjectType(BooleanNode::class);
            case "file":
                return new ObjectType(FileNode::class);
            default:
                return null;
        }
    }
}
