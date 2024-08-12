<?php

declare(strict_types=1);

namespace Square\Hyrule\Tests;

use PHPUnit\Framework\TestCase;
use Square\Hyrule\Hyrule;
use Square\Hyrule\Nodes\ArrayNode;
use Square\Hyrule\Nodes\NodeType;
use Square\Hyrule\Nodes\ObjectNode;
use Square\Hyrule\PathExp;
use Square\Hyrule\Rules\KnownPropertiesOnly;

class PathExpressionTest extends TestCase
{
    public function testPathExp(): void
    {
        $exp = PathExp::new()->parent()->parent()->parent()->get('foo')->get('target');
        $start = Hyrule::create()
            ->object('root')
                ->object('foo')
                    ->string('target')
                    ->end()
                ->end()
                ->object('another')
                    ->object('tree')
                        ->integer('start');

        $target = $exp->traverse($start);
        $this->assertSame('target', $target->getName());
    }

    public function testNodeTraversal(): void
    {
        $node = new ArrayNode('bar');
        $reference = $node
            ->min(1)
            ->max(20)
            ->each(NodeType::Object)
                ->string('name')->required()->max(255)->end()
                ->boolean('cool_kid')->nullable()->end()
                ->array('hobbies')
                    ->requiredIf(PathExp::new()->parent()->get('cool_kid'), '1')
                    ->max(5)
                    ->each(NodeType::String)
                        ->uppercase()
                    ->end()
                ->end();

        $this->assertInstanceOf(ObjectNode::class, $reference);
        $this->assertSame($node, $reference->end());
        $this->assertEquals(
            [
                'bar' => ['array', 'min:1', 'max:20'],
                'bar.*' => [new KnownPropertiesOnly(['name', 'cool_kid', 'hobbies'])],
                'bar.*.name' => ['string', 'required', 'max:255'],
                'bar.*.cool_kid' => ['boolean', 'nullable'],
                'bar.*.hobbies' => ['array', 'required_if:bar.*.cool_kid,1', 'max:5'],
                'bar.*.hobbies.*' => ['string', 'uppercase'],
            ],
            $node->build(),
        );
    }

    public function testPathExpHigherUp(): void
    {
        $node = Hyrule::create()->object('foo');
        $node->boolean('bar_neighbor');
        $node->object('bar')
            ->object('baz')
            ->string('neighbor_name')
            ->requiredIf(
                PathExp::new()
                    ->parent()
                    ->parent()
                    ->parent()
                    ->get('bar_neighbor'),
                '1'
            )->end()
            ->end()
            ->end();


        $this->assertEquals(
            [
                'foo'  => [new KnownPropertiesOnly(['bar_neighbor', 'bar'])],
                'foo.bar_neighbor' => ['boolean'],
                'foo.bar' => [new KnownPropertiesOnly(['baz'])],
                'foo.bar.baz' => [new KnownPropertiesOnly(['neighbor_name'])],
                'foo.bar.baz.neighbor_name' => ['string', 'required_if:foo.bar_neighbor,1'],
            ],
            $node->build(),
        );
    }
}
