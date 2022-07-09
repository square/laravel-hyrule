<?php

declare(strict_types=1);

namespace Square\Hyrule\Tests;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use PHPUnit\Framework\TestCase;
use Square\Hyrule\Hyrule;
use Square\Hyrule\Nodes\ArrayNode;
use Square\Hyrule\Nodes\IntegerNode;
use Square\Hyrule\Nodes\ObjectNode;
use Square\Hyrule\Nodes\ScalarNode;
use Square\Hyrule\Nodes\StringNode;
use Square\Hyrule\PathExp;
use Square\Hyrule\Rules\KnownPropertiesOnly;

class BuilderTypedTest extends TestCase
{
    public function testScalar(): void
    {
        $node = new ScalarNode('foo');
        $this->assertEquals(
            [
                'foo' => [],
            ],
            $node->build(),
        );

        $node->required()
            ->rule('string');

        $this->assertEquals(
            [
                'foo' => ['required', 'string'],
            ],
            $node->build(),
        );
    }

    public function testStringNode(): void
    {
        $node = new StringNode('bar');
        $this->assertEquals(
            [
                'bar' => ['string'],
            ],
            $node->build(),
        );

        $node->required();

        $this->assertEquals(
            [
                'bar' => ['string', 'required'],
            ],
            $node->build(),
        );
    }

    public function testIntegerNode(): void
    {
        $node = new IntegerNode('bar');
        $this->assertEquals(
            [
                'bar' => ['integer'],
            ],
            $node->build(),
        );

        $node->required();

        $this->assertEquals(
            [
                'bar' => ['integer', 'required'],
            ],
            $node->build(),
        );
    }

    public function testArrayNode(): void
    {
        $node = new ArrayNode('bar');
        $this->assertEquals(
            [
                'bar' => ['array'],
            ],
            $node->build(),
        );

        $node
            ->required()
            ->min(2)
            ->max(20);

        $this->assertEquals(
            [
                'bar' => ['array', 'required', 'min:2', 'max:20'],
            ],
            $node->build(),
        );
    }

    public function testArrayEach(): void
    {
        $node = new ArrayNode('bar');
        $reference = $node
            ->each('string')
                ->min(2)
                ->max(20);

        $this->assertInstanceOf(StringNode::class, $reference);
        $this->assertEquals(
            [
                'bar' => ['array'],
                'bar.*' => ['string', 'min:2', 'max:20'],
            ],
            $node->build(),
        );
        $this->assertSame($node, $reference->end());
    }

    public function testArrayEachObject(): void
    {
        $node = new ArrayNode('bar');
        $reference = $node
            ->min(1)
            ->max(20)
            ->each('object')
                ->string('name')->required()->max(255)->end()
                ->integer('age')->nullable()->end()
                ->boolean('accept')->required()->end();

        $this->assertInstanceOf(ObjectNode::class, $reference);
        $this->assertSame($node, $reference->end());
        $this->assertEquals(
            [
                'bar' => ['array', 'min:1', 'max:20'],
                'bar.*' => [new KnownPropertiesOnly(['name', 'age', 'accept'])],
                'bar.*.name' => ['string', 'required', 'max:255'],
                'bar.*.age' => ['integer', 'nullable'],
                'bar.*.accept' => ['boolean', 'required'],
            ],
            $node->build(),
        );
    }

    public function testNestedArrays(): void
    {
        $node = new ArrayNode('bar');
        $reference = $node
            ->min(1)
            ->max(20)
            ->each('object')
                ->string('name')->required()->max(255)->end()
                ->array('hobbies')
                    ->max(5)
                    ->each('string')
                        ->uppercase()
                    ->end()
                ->end();

        $this->assertInstanceOf(ObjectNode::class, $reference);
        $this->assertSame($node, $reference->end());
        $this->assertEquals(
            [
                'bar' => ['array', 'min:1', 'max:20'],
                'bar.*' => [new KnownPropertiesOnly(['name', 'hobbies'])],
                'bar.*.name' => ['string', 'required', 'max:255'],
                'bar.*.hobbies' => ['array', 'max:5'],
                'bar.*.hobbies.*' => ['string', 'uppercase'],
            ],
            $node->build(),
        );
    }

    public function testNodeAsPathParam(): void
    {
        $builder = Hyrule::create();
        $foo = $builder
            ->object('bar')
                ->string('foo');

        $builder
            ->object('bar')
                ->object('baz')
                    ->integer('foo')
                    ->requiredWithout($foo);
        $this->assertEquals([
            '' => [new KnownPropertiesOnly(['bar'])],
            'bar' => [new KnownPropertiesOnly(['foo', 'baz'])],
            'bar.foo' => ['string'],
            'bar.baz' => [new KnownPropertiesOnly(['foo'])],
            'bar.baz.foo' => ['integer', 'required_without:bar.foo'],
        ], $builder->build());
    }

    public function testRequiredIfBooleans(): void
    {
        $builder = Hyrule::create()
            ->boolean('include_title')
                ->end()
            ->string('title')
                ->requiredIf('include_title', true)
                ->end()
            ->end();

        $rules = $builder->build();

        $factory = new Factory(new Translator(new ArrayLoader(), 'en'));
        $validator = $factory->make([
            'include_title' => true,
        ], $rules);

        $this->assertFalse($validator->passes());
    }
}
