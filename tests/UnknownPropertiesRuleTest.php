<?php

namespace Square\Hyrule\Tests;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use PHPUnit\Framework\TestCase;
use Square\Hyrule\Hyrule;

class UnknownPropertiesRuleTest extends TestCase
{
    public function testNoUnknownProperties(): void
    {
        $builder = Hyrule::create()
            ->object('foo')
                ->string('first_name')->end()
                ->string('last_name')->end()
            ->end()
        ->end();

        $factory = new Factory($translator = new Translator(new ArrayLoader(), 'en'));
        $translator->addLines([
            'validation.unknown_property' => ':attribute is not a recognized field.',
        ], 'en');
        $validator = $factory->make([
            'foo' => [
                'first_name' => 'Mark',
            ],
        ], $builder->build());

        $this->assertTrue($validator->passes());
    }

    public function testOneUnknownProperty(): void
    {
        $builder = Hyrule::create()
             ->object('foo')
                ->string('first_name')->end()
                ->string('last_name')->end()
            ->end()
        ->end();

        $factory = new Factory($translator = new Translator(new ArrayLoader(), 'en'));
        $translator->addLines([
            'validation.unknown_property' => ':attribute is not a recognized field.',
        ], 'en');
        $validator = $factory->make([
            'foo' => [
                'first_name' => 'Mark',
                'company' => 'Lumon',
            ],
        ], $builder->build());

        $this->assertFalse($validator->passes());
        $this->assertEquals([
            'foo.company' => [
                'foo.company is not a recognized field.',
            ],
        ], $validator->messages()->toArray());
    }

    public function testMultipleUnknownProperties(): void
    {
        $builder = Hyrule::create()
            ->object('foo')
                ->string('first_name')->end()
                ->string('last_name')->end()
            ->end()
        ->end();

        $factory = new Factory($translator = new Translator(new ArrayLoader(), 'en'));
        $translator->addLines([
            'validation.unknown_property' => ':attribute is not a recognized field.',
        ], 'en');
        $validator = $factory->make([
            'foo' => [
                'first_name' => 'Mark',
                'company' => 'Lumon',
                'department' => 'Macrodata Refinement',
            ]
        ], $builder->build());

        $this->assertFalse($validator->passes());
        $this->assertEquals([
            'foo.company' => [
                'foo.company is not a recognized field.',
            ],
            'foo.department' => [
                'foo.department is not a recognized field.',
            ],
        ], $validator->messages()->toArray());
    }

    public function testUnknownPropertiesWithPropertyRuleDisabled(): void
    {
        $builder = Hyrule::create()
            ->object('foo')
                ->allowUnknownProperties()
                ->string('first_name')->end()
                ->string('last_name')->end()
            ->end()
        ->end();

        $factory = new Factory($translator = new Translator(new ArrayLoader(), 'en'));
        $translator->addLines([
            'validation.unknown_property' => ':attribute is not a recognized field.',
        ], 'en');
        $validator = $factory->make([
            'foo' => [
                'first_name' => 'Mark',
                'company' => 'Lumon',
                'department' => 'Macrodata Refinement',
            ]
        ], $builder->build());

        $this->assertTrue($validator->passes());
    }
}
