<?php

namespace Square\Hyrule\Tests;

use Square\Hyrule\Nodes\StringNode;
use Generator;

class StringNodeTest extends NodeTestAbstract
{
    protected function getBuilderMethodName(): string
    {
        return 'string';
    }

    protected function getNodeClassName(): string
    {
        return StringNode::class;
    }

    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['string'];
    }

    /**
     * @return Generator
     */
    public function dataValid()
    {
        yield 'string' => [
            'foo',
        ];

        yield 'empty string' => [
            '',
        ];
    }

    /**
     * @return Generator
     */
    public function dataInvalid()
    {
        yield 'integer' => [
            1,
        ];

        yield 'boolean' => [
            true,
        ];

        yield 'float' => [
            1.0,
        ];

        yield 'array' => [
            [1, 2, 3],
        ];

        yield 'object' => [
            [
                'a' => 1,
            ],
        ];

        yield 'too short' => [
            'abc',
            static function (StringNode $node) {
                $node->min(5);
            },
        ];

        yield 'too long' => [
            'abc',
            static function (StringNode $node) {
                $node->max(1);
            },
        ];
    }
}

