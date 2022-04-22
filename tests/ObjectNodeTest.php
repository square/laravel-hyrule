<?php

namespace Square\Hyrule\Tests;

use Square\Hyrule\Nodes\ObjectNode;
use Generator;

class ObjectNodeTest extends NodeTestAbstract
{
    protected function getBuilderMethodName(): string
    {
        return 'object';
    }

    protected function getNodeClassName(): string
    {
        return ObjectNode::class;
    }

    /**
     * @return string[]
     */
    protected function defaultRules(): array
    {
        return ['array'];
    }

    /**
     * @return \Generator
     */
    public function dataValid()
    {
        yield '#0' => [
            ['a' => 1],
            static function (ObjectNode $node) {
                $node->integer('a');
            },
        ];

        yield '#1' => [
            ['a' => 1, 'b' => 'c'],
            static function (ObjectNode $node) {
                $node->integer('a');
                $node->string('b');
            },
        ];

        yield 'non-required field' => [
            ['a' => 1, 'b' => 'c'],
            static function (ObjectNode $node) {
                $node->integer('a');
                $node->string('b');
                $node->string('c');
            },
        ];

        yield 'non-strict shape' => [
            ['a' => 1, 'b' => 'c', 'c' => true],
            static function (ObjectNode $node) {
                $node->integer('a');
                $node->string('b');
                $node->boolean('c');
                $node->allowUnknownProperties();
            },
        ];
    }

    /**
     * @return \Generator
     */
    public function dataInvalid()
    {
        yield 'string' => [
            '{}',
        ];

        yield 'integer' => [
            3,
        ];

        yield 'numeric' => [
            '42',
        ];

        yield 'float' => [
            3.14,
        ];

        yield 'unknown field' => [
            ['b' => 1, 'xyz' => 1],
            static function (ObjectNode $node) {
                $node->integer('b');
            },
        ];

        yield 'missing required field' => [
            ['a' => 1],
            static function (ObjectNode $node) {
                $node->integer('a');
                $node->string('b')->required();
            },
        ];
    }
}
