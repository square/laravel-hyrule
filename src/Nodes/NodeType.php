<?php

namespace Square\Hyrule\Nodes;

enum NodeType: string
{
    case Integer = 'integer';
    case String = 'string';
    case Numeric = 'numeric';
    case Float = 'float';
    case Boolean = 'boolean';
    case Array = 'array';
    case Object = 'object';
    case Scalar = 'scalar';
    case File = 'file';

    /**
     * @return string
     */
    public function nodeClassName(): string
    {
        return match($this) {
            self::Integer => IntegerNode::class,
            self::String => StringNode::class,
            self::Numeric => NumericNode::class,
            self::Float => FloatNode::class,
            self::Boolean => BooleanNode::class,
            self::Array => ArrayNode::class,
            self::Object => ObjectNode::class,
            self::Scalar => ScalarNode::class,
            self::File => FileNode::class,
        };
    }
}