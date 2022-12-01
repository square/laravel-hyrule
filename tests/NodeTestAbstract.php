<?php

namespace Square\Hyrule\Tests;

use Generator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use PHPUnit\Framework\TestCase;
use Square\Hyrule\Hyrule;
use Square\Hyrule\Nodes\RootNode;
use Square\Hyrule\Nodes\StringNode;

abstract class NodeTestAbstract extends TestCase
{
    /**
     * @return string
     */
    abstract protected function getBuilderMethodName(): string;

    /**
     * @return class-string
     */
    abstract protected function getNodeClassName(): string;

    /**
     * @return string[]
     */
    abstract protected function defaultRules(): array;

    /**
     * @return void
     */
    public function testBuilder()
    {
        $methodName = $this->getBuilderMethodName();
        $className = $this->getNodeClassName();
        $node = Hyrule::create()->$methodName('foo');
        $this->assertInstanceOf($className, $node);
    }

    /**
     * @return void
     */
    public function testName()
    {
        $methodName = $this->getBuilderMethodName();
        $node = Hyrule::create()->$methodName('foo');
        $this->assertSame('foo', $node->getName());
    }

    /**
     * @return void
     */
    public function testDefaultRules()
    {
        $methodName = $this->getBuilderMethodName();
        $node = Hyrule::create()->$methodName('foo');
        $this->assertEquals([
            'foo' => $this->defaultRules(),
        ], $node->build());
    }

    /**
     * @return void
     */
    public function testFieldWith()
    {
        $methodName = $this->getBuilderMethodName();
        $className = $this->getNodeClassName();
        $builder = Hyrule::create();
        $withMethod = sprintf('%sWith', $methodName);
        $returned = $builder->$withMethod('foo', static function () {});
        $this->assertSame($builder, $returned);
        $node = $builder->$methodName('foo');
        $this->assertInstanceOf($className, $node);
        $this->assertSame($node, $builder->$methodName('foo'));
    }

    /**
     * @param mixed $value
     * @param callable|null $builderFn
     * @return void
     * @dataProvider dataValid
     */
    public function testValidData($value, callable $builderFn = null)
    {
        $methodName = $this->getBuilderMethodName();
        /** @var RootNode $builder */
        $builder = Hyrule::create()
            ->$methodName('foo')
            ->with($builderFn ?? static function () {}) ->end();
        $rules = $builder->build();
        /** @var Factory $factory */
        $factory = new Factory(new Translator(new ArrayLoader(), 'en'));
        $validator = $factory->make([
            'foo' => $value,
        ], $rules);
        $this->assertEmpty($validator->errors()->all());
    }

    /**
     * @param mixed $value
     * @param callable|null $builderFn
     * @return void
     * @dataProvider dataInvalid
     */
    public function testInvalidData($value, callable $builderFn = null)
    {
        $methodName = $this->getBuilderMethodName();
        /** @var RootNode $builder */
        $builder = Hyrule::create()
            ->$methodName('foo')
            ->with($builderFn ?? static function () {}) ->end();
        $rules = $builder->build();
        /** @var Factory $factory */
        $factory = new Factory(new Translator(new ArrayLoader(), 'en'));
        $validator = $factory->make([
            'foo' => $value,
        ], $rules);
        if ($validator->errors()->isEmpty()) {
            var_dump($rules);
            var_dump(['foo' => $value]);
            var_dump($validator->errors()->all());
        }
        $this->assertNotEmpty($validator->errors()->all());
    }

    /**
     * @return array<array<mixed>>|Generator
     */
    public function dataValid()
    {
        return [];
    }

    /**
     * @return array<array<mixed>>|Generator
     */
    public function dataInvalid()
    {
        return [];
    }
}

