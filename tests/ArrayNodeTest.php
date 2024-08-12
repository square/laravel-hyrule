<?php

namespace Square\Hyrule\Tests;

use Generator;
use Illuminate\Http\UploadedFile;
use LogicException;
use Square\Hyrule\Hyrule;
use Square\Hyrule\Nodes\ArrayNode;
use Square\Hyrule\Nodes\NodeType;

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
    public static function dataValid()
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
                $node->each(NodeType::Integer);
            },
        ];

        yield 'array of strings' => [
            range('a', 'z'),
            static function (ArrayNode $node) {
                $node->each(NodeType::String);
            },
        ];

        yield 'array of integer arrays' => [
            [
                [1],
                [1, 2, 3],
                [],
            ],
            static function (ArrayNode $node) {
                $node->each(NodeType::Array)
                    ->each(NodeType::Integer);
            }
        ];

        yield 'array of non-empty arrays' => [
            [
                [1],
                [1, 2, 3],
            ],
            static function (ArrayNode $node) {
                $node->each(NodeType::Array)
                    ->min(1)
                        ->each(NodeType::Integer)->end();
            }
        ];

        yield 'array of images' => [
            [
                UploadedFile::fake()->image('foo.jpeg'),
                UploadedFile::fake()->image('foo.jpg'),
                UploadedFile::fake()->image('foo.png'),
                UploadedFile::fake()->image('foo.gif'),
                UploadedFile::fake()->image('foo.svg'),
                UploadedFile::fake()->image('foo.bmp'),
            ],
            static function(ArrayNode $node) {
                $node->each(NodeType::File)
                    ->image();
            }
        ];
    }

    /**
     * @return Generator
     */
    public static function dataInvalid()
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
                $node->each(NodeType::Integer);
            }
        ];

        yield 'array of integers' => [
            range('a', 'z'),
            static function (ArrayNode $node) {
                $node->each(NodeType::Integer);
            },
        ];

        yield 'array of strings' => [
            range(1, 100),
            static function (ArrayNode $node) {
                $node->each(NodeType::String);
            },
        ];

        yield 'array of integer arrays' => [
            [
                ['a', 'b', 'c'],
            ],
            static function (ArrayNode $node) {
                $node
                    ->each(NodeType::Array)
                    ->each(NodeType::Integer);
            }
        ];

        yield 'array of non-empty arrays' => [
            [
                [1],
                [1, 2, 3],
                [],
            ],
            static function (ArrayNode $node) {
                $node->each(NodeType::Array)
                        ->each(NodeType::Integer)->end()
                        ->min(1);
            }
        ];

        yield 'array of files' => [
            [
                UploadedFile::fake()->create('foo.pdf', 0, 'application/pdf'),
                UploadedFile::fake()->image('foo.jpeg'),
                UploadedFile::fake()->create('foo.json', 0, 'application/json'),
            ],
            static function(ArrayNode $node) {
                $node->each(NodeType::File)
                    ->mimeType()
                        ->application('pdf')
                        ->image('jpeg');
            }
        ];
    }

    /**
     * @param mixed $type
     * @return void
     * @dataProvider dataRedefiningSameType
     */
    public function testRedefiningSameType(mixed $type)
    {
        $list = Hyrule::create()->array('list');
        $original = $list->each($type);
        $redefined = $list->each($type);
        $this->assertSame($original, $redefined);
    }

    public static function dataRedefiningSameType()
    {
        foreach (NodeType::cases() as $nodeType) {
            yield $nodeType->name => [$nodeType];
            yield $nodeType->nodeClassName() => [$nodeType->nodeClassName()];
        }
    }

    public function testRedefiningNotAllowedWithNonDifferentType()
    {
        $this->expectException(LogicException::class);
        $list = Hyrule::create()->array('list');
        $list->each(NodeType::Integer);
        $list->each(NodeType::String);
    }

    public function testRedefiningNotAllowedWithSubtype()
    {
        $this->expectException(LogicException::class);
        $list = Hyrule::create()->array('list');
        // Pre-condition: we expect IntegerNode to be sub-type of ScalarNode
        $this->assertTrue(is_a(NodeType::Integer->nodeClassName(), NodeType::Scalar->nodeClassName(), true), 'Pre-condition failed.');
        $list->each(NodeType::Scalar);
        $list->each(NodeType::Integer);
    }

    public function testRedefiningNotAllowedWithSuperType()
    {
        $this->expectException(LogicException::class);
        $list = Hyrule::create()->array('list');
        // Pre-condition: we expect IntegerNode to be sub-type of ScalarNode
        $this->assertTrue(is_a(NodeType::Integer->nodeClassName(), NodeType::Scalar->nodeClassName(), true), 'Pre-condition failed.');
        $list->each(NodeType::Integer);
        $list->each(NodeType::Scalar);
    }
}
