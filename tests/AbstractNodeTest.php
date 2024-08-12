<?php

namespace Square\Hyrule\Tests;

use Generator;
use PHPUnit\Framework\TestCase;
use Square\Hyrule\Hyrule;

class AbstractNodeTest extends TestCase
{
    /**
     * @param array<mixed> $args
     * @param string $expected
     * @return void
     * @dataProvider dataRequiredUnless
     */
    public function testRequiredUnless(array $args, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
                ->string('bar')
                ->requiredUnless(...$args)
            ->end()
        ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }

    public static function dataRequiredUnless(): Generator
    {
        yield 'string path' => [
            ['foo.bar', 'boo'],
            'required_unless:foo.bar,boo',
        ];

        yield 'node path' => [
            [Hyrule::create()->string('boom'), 'baz'],
            'required_unless:boom,baz',
        ];

        yield 'nested node path' => [
            [Hyrule::create()->object('foo')->string('bar'), 'baz'],
            'required_unless:foo.bar,baz',
        ];

        yield 'path expression' => [
            [Hyrule::pathExp()->parent(), 'baz'],
            'required_unless:foo,baz',
        ];
    }

    /**
     * @param array<mixed> $args
     * @param string $expected
     * @return void
     * @dataProvider dataRequiredIf
     */
    public function testRequiredIf(array $args, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->requiredIf(...$args)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }


    public static function dataRequiredIf(): Generator
    {
        yield 'string path' => [
            ['foo.bar', 'boo'],
            'required_if:foo.bar,boo',
        ];

        yield 'node path' => [
            [Hyrule::create()->string('boom'), 'baz'],
            'required_if:boom,baz',
        ];

        yield 'nested node path' => [
            [Hyrule::create()->object('foo')->string('bar'), 'baz'],
            'required_if:foo.bar,baz',
        ];

        yield 'path expression' => [
            [Hyrule::pathExp()->parent(), 'baz'],
            'required_if:foo,baz',
        ];
    }

    /**
     * @param mixed $arg
     * @param string $expected
     * @return void
     * @dataProvider dataRequiredWith
     */
    public function testRequiredWith($arg, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->requiredWith($arg)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }


    public static function dataRequiredWith(): Generator
    {
        yield 'string path' => [
            'foo.bar',
            'required_with:foo.bar',
        ];

        yield 'node path' => [
            Hyrule::create()->string('boom'),
            'required_with:boom',
        ];

        yield 'nested node path' => [
            Hyrule::create()->object('foo')->string('bar'),
            'required_with:foo.bar',
        ];

        yield 'path expression' => [
            Hyrule::pathExp()->parent(),
            'required_with:foo',
        ];
    }

    /**
     * @param mixed $arg
     * @param string $expected
     * @return void
     * @dataProvider dataRequiredWithout
     */
    public function testRequiredWithout($arg, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->requiredWithout($arg)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }


    public static function dataRequiredWithout(): Generator
    {
        yield 'string path' => [
            'foo.bar',
            'required_without:foo.bar',
        ];

        yield 'node path' => [
            Hyrule::create()->string('boom'),
            'required_without:boom',
        ];

        yield 'nested node path' => [
            Hyrule::create()->object('foo')->string('bar'),
            'required_without:foo.bar',
        ];

        yield 'path expression' => [
            Hyrule::pathExp()->parent(),
            'required_without:foo',
        ];
    }

    /**
     * @param array<mixed> $args
     * @param string $expected
     * @return void
     * @dataProvider dataExcludeUnless
     */
    public function testExcludeUnless(array $args, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->excludeUnless(...$args)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }

    public static function dataExcludeUnless(): Generator
    {
        yield 'string path' => [
            ['foo.bar', 'boo'],
            'exclude_unless:foo.bar,boo',
        ];

        yield 'node path' => [
            [Hyrule::create()->string('boom'), 'baz'],
            'exclude_unless:boom,baz',
        ];

        yield 'nested node path' => [
            [Hyrule::create()->object('foo')->string('bar'), 'baz'],
            'exclude_unless:foo.bar,baz',
        ];

        yield 'path expression' => [
            [Hyrule::pathExp()->parent(), 'baz'],
            'exclude_unless:foo,baz',
        ];
    }

    /**
     * @param array<mixed> $args
     * @param string $expected
     * @return void
     * @dataProvider dataExcludeIf
     */
    public function testExcludeIf(array $args, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->excludeIf(...$args)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }

    public static function dataExcludeIf(): Generator
    {
        yield 'string path' => [
            ['foo.bar', 'boo'],
            'exclude_if:foo.bar,boo',
        ];

        yield 'node path' => [
            [Hyrule::create()->string('boom'), 'baz'],
            'exclude_if:boom,baz',
        ];

        yield 'nested node path' => [
            [Hyrule::create()->object('foo')->string('bar'), 'baz'],
            'exclude_if:foo.bar,baz',
        ];

        yield 'path expression' => [
            [Hyrule::pathExp()->parent(), 'baz'],
            'exclude_if:foo,baz',
        ];
    }

    /**
     * @param mixed $arg
     * @param string $expected
     * @return void
     * @dataProvider dataExcludeWith
     */
    public function testExcludeWith($arg, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->excludeWith($arg)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }


    public static function dataExcludeWith(): Generator
    {
        yield 'string path' => [
            'foo.bar',
            'exclude_with:foo.bar',
        ];

        yield 'node path' => [
            Hyrule::create()->string('boom'),
            'exclude_with:boom',
        ];

        yield 'nested node path' => [
            Hyrule::create()->object('foo')->string('bar'),
            'exclude_with:foo.bar',
        ];

        yield 'path expression' => [
            Hyrule::pathExp()->parent(),
            'exclude_with:foo',
        ];
    }

    /**
     * @param mixed $arg
     * @param string $expected
     * @return void
     * @dataProvider dataExcludeWithout
     */
    public function testExcludeWithout($arg, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->excludeWithout($arg)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }


    public static function dataExcludeWithout(): Generator
    {
        yield 'string path' => [
            'foo.bar',
            'exclude_without:foo.bar',
        ];

        yield 'node path' => [
            Hyrule::create()->string('boom'),
            'exclude_without:boom',
        ];

        yield 'nested node path' => [
            Hyrule::create()->object('foo')->string('bar'),
            'exclude_without:foo.bar',
        ];

        yield 'path expression' => [
            Hyrule::pathExp()->parent(),
            'exclude_without:foo',
        ];
    }

    /**
     * @param array<mixed> $args
     * @param string $expected
     * @return void
     * @dataProvider dataProhibitedUnless
     */
    public function testProhibitedUnless(array $args, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->prohibitedUnless(...$args)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }

    public static function dataProhibitedUnless(): Generator
    {
        yield 'string path' => [
            ['foo.bar', 'boo'],
            'prohibited_unless:foo.bar,boo',
        ];

        yield 'node path' => [
            [Hyrule::create()->string('boom'), 'baz'],
            'prohibited_unless:boom,baz',
        ];

        yield 'nested node path' => [
            [Hyrule::create()->object('foo')->string('bar'), 'baz'],
            'prohibited_unless:foo.bar,baz',
        ];

        yield 'path expression' => [
            [Hyrule::pathExp()->parent(), 'baz'],
            'prohibited_unless:foo,baz',
        ];
    }

    /**
     * @param array<mixed> $args
     * @param string $expected
     * @return void
     * @dataProvider dataProhibitedIf
     */
    public function testProhibitedIf(array $args, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->prohibitedIf(...$args)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }

    public static function dataProhibitedIf(): Generator
    {
        yield 'string path' => [
            ['foo.bar', 'boo'],
            'prohibited_if:foo.bar,boo',
        ];

        yield 'node path' => [
            [Hyrule::create()->string('boom'), 'baz'],
            'prohibited_if:boom,baz',
        ];

        yield 'nested node path' => [
            [Hyrule::create()->object('foo')->string('bar'), 'baz'],
            'prohibited_if:foo.bar,baz',
        ];

        yield 'path expression' => [
            [Hyrule::pathExp()->parent(), 'baz'],
            'prohibited_if:foo,baz',
        ];
    }

    /**
     * @param mixed $path
     * @param string $expected
     * @return void
     * @dataProvider dataRequiredIfDeclined
     */
    public function testRequiredIfDeclined(mixed $path, string $expected)
    {
        $node = Hyrule::create()
            ->object('foo')
            ->string('bar')
            ->requiredIfDeclined($path)
            ->end()
            ->end();
        $rules = $node->build();
        $this->assertEquals($expected, $rules['foo.bar'][1]);
    }

    public static function dataRequiredIfDeclined(): Generator
    {
        yield 'string path' => [
            'foo.bar',
            'required_if_declined:foo.bar',
        ];

        yield 'node path' => [
            Hyrule::create()->string('boom'),
            'required_if_declined:boom',
        ];

        yield 'nested node path' => [
            Hyrule::create()->object('foo')->string('bar'),
            'required_if_declined:foo.bar',
        ];

        yield 'path expression' => [
            Hyrule::pathExp()->parent(),
            'required_if_declined:foo',
        ];
    }
}