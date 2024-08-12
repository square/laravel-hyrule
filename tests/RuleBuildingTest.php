<?php

namespace Square\Hyrule\Tests;

use PHPUnit\Framework\TestCase;
use Square\Hyrule\Build\LazyRuleStringify;
use Square\Hyrule\Hyrule;
use Square\Hyrule\Nodes\AbstractNode;
use Stringable;

class RuleBuildingTest extends TestCase
{
    protected AbstractNode $node;

    public function setUp(): void
    {
        $this->node = new class ('test') extends AbstractNode {
            public static function testBuildRule() {
                return self::buildRule(...func_get_args());
            }
        };
    }

    /**
     * @param string $ruleName
     * @param array $arguments
     * @param string $expected
     * @param bool $lazy
     * @dataProvider dataBuildRule
     * @return void
     */
    public function testBuildRule(string $ruleName, array $arguments, string $expected)
    {
        $rule = $this->node::testBuildRule($ruleName, $arguments);
        $this->assertIsString($rule);
        $this->assertEquals($expected, (string) $rule);
    }

    /**
     * @param string $ruleName
     * @param array $arguments
     * @param string $expected
     * @dataProvider dataBuildLazyRule
     * @return void
     */
    public function testBuildLazyRule(string $ruleName, array $arguments, AbstractNode $node, string $expected)
    {
        $rule = $this->node::testBuildRule($ruleName, $arguments);
        $this->assertInstanceOf(LazyRuleStringify::class, $rule);
        $rule->setNode($node);
        $this->assertEquals($expected, $rule->stringify());
    }

    public function dataBuildRule()
    {
        yield 'string' => [
            'rule_name',
            ['foo'],
            'rule_name:foo',
            false,
        ];

        yield 'strings' => [
            'rule_name',
            ['foo', 'bar'],
            'rule_name:foo,bar',
            false,
        ];

        yield 'no args' => [
            'rule_name',
            [],
            'rule_name',
            false,
        ];

        yield 'stringable' => [
            'rule_name',
            [self::stringable('foo')],
            'rule_name:foo',
            false,
        ];

        yield 'many stringables' => [
            'rule_name',
            [self::stringable('foo'), self::stringable('bar'), self::stringable('baz')],
            'rule_name:foo,bar,baz',
            false,
        ];

        yield 'some stringables' => [
            'rule_name',
            [self::stringable('foo'), 'bar', self::stringable('baz')],
            'rule_name:foo,bar,baz',
            false,
        ];

        yield 'some stringables later in list' => [
            'rule_name',
            ['foo', 'bar', self::stringable('baz')],
            'rule_name:foo,bar,baz',
            false,
        ];
    }

    protected static function stringable(string $value): Stringable
    {
        return new class($value) implements Stringable {
            public function __construct(private readonly string $value)
            { }

            public function __toString()
            {
                return $this->value;
            }
        };
    }

    public function dataBuildLazyRule()
    {
        yield 'node' =>  [
            'rule_name',
            [Hyrule::create()->object('name')->string('first')],
            Hyrule::create(),
            'rule_name:name.first',
        ];

        yield 'many nodes' =>  [
            'rule_name',
            [Hyrule::create()->object('name')->string('first'), Hyrule::create()->object('name')->string('last')],
            Hyrule::create(),
            'rule_name:name.first,name.last',
        ];

        yield 'pathexp' =>  [
            'rule_name',
            [Hyrule::pathExp()->parent()],
            Hyrule::create()->object('name')->string('first'),
            'rule_name:name',
        ];

        yield 'many pathexp' =>  [
            'rule_name',
            [Hyrule::pathExp()->parent(), Hyrule::pathExp()->parent()->get('last')],
            Hyrule::create()->object('name')->string('last')->end()->string('first'),
            'rule_name:name,name.last',
        ];
    }
}