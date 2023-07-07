<?php

namespace Square\Hyrule\Tests;

use Monolog\Test\TestCase;
use Square\Hyrule\Hyrule;
use Square\Hyrule\Nodes\StringNode;

class AutoSnakeCaseTest extends TestCase
{
    /**
     * @param string $camelCase
     * @param array<mixed> $args
     * @param string $expected
     * @return void
     * @dataProvider data
     */
    public function testAutoSnakeCase(string $camelCase, array $args, string $expected): void
    {
        $node = Hyrule::create()
            ->string('foo')
            ->$camelCase(...$args);
        assert($node instanceof StringNode);
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo'][1]);
    }

    /**
     * @return array<mixed>
     */
    public static function data(): array
    {
        return [
            [
                'hello',
                [],
                'hello',
            ],
            [
                'helloWorld',
                [],
                'hello_world',
            ],
            [
                'helloWorld',
                [1, 2, 3],
                'hello_world:1,2,3',
            ],
            [
                'helloBeautifulWorld',
                [],
                'hello_beautiful_world',
            ],
            [
                'helloBeautifulWorld',
                ['a', 'b', 'c'],
                'hello_beautiful_world:a,b,c',
            ],
        ];
    }
}