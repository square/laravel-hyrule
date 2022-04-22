<?php

namespace Square\Hyrule\Tests;

use Generator;
use Square\Hyrule\Nodes\ArrayNode;

class ArrayNodeTest extends NodeTestAbstract
{
    protected function getBuilderMethodName(): string
    {
        return 'array';
    }

    protected function getNodeClassName(): string
    {
        return ArrayNode::class;
    }

    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['array'];
    }

    /**
     * @return Generator
     */
    public function dataValid()
    {
        yield 'empty array' => [
            [],
        ];

        yield 'non-empty array' => [
            range('a', 'z'),
        ];

        // Bare ArrayNode don't validate element types.
        yield 'mixed array' => [
            [1, 2, 3, 'a', []],
        ];

        yield 'array of integers' => [
            range(1, 100),
            static function (ArrayNode $node) {
                $node->each('integer');
            },
        ];

        yield 'array of strings' => [
            range('a', 'z'),
            static function (ArrayNode $node) {
                $node->each('string');
            },
        ];

        yield 'array of integer arrays' => [
            [
                [1],
                [1, 2, 3],
                [],
            ],
            static function (ArrayNode $node) {
                $node->each('array')
                    ->each('integer');
            }
        ];

        yield 'array of non-empty arrays' => [
            [
                [1],
                [1, 2, 3],
            ],
            static function (ArrayNode $node) {
                $node->each('array')
                    ->min(1)
                        ->each('integer')->end();
            }
        ];
    }

    /**
     * @return Generator
     */
    public function dataInvalid()
    {
        yield 'string' => [
            '[]',
        ];

        yield 'integer' => [
            PHP_INT_MAX,
        ];

        yield 'mixed array' => [
            [1, 2, 3, 'a', []],
            static function (ArrayNode $node) {
                $node->each('integer');
            }
        ];

        yield 'array of integers' => [
            range('a', 'z'),
            static function (ArrayNode $node) {
                $node->each('integer');
            },
        ];

        yield 'array of strings' => [
            range(1, 100),
            static function (ArrayNode $node) {
                $node->each('string');
            },
        ];

        yield 'array of integer arrays' => [
            [
                ['a', 'b', 'c'],
            ],
            static function (ArrayNode $node) {
                $node
                    ->each('array')
                    ->each('integer');
            }
        ];

        yield 'array of non-empty arrays' => [
            [
                [1],
                [1, 2, 3],
                [],
            ],
            static function (ArrayNode $node) {
                $node->each('array')
                        ->each('integer')->end()
                        ->min(1);
            }
        ];
    }
}
