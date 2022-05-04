<?php

namespace Square\Hyrule\Tests\Validator;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Monolog\Test\TestCase;
use Square\Hyrule\Hyrule;
use Square\Hyrule\Validator\StrictValidator;

class StrictValidatorTest extends TestCase
{
    public function testUnknownTopLevelField()
    {
        $builder = Hyrule::create()
            ->string('foo')->end()
            ->string('bar')->end()
        ->end();

        $validator = $this->makeValidator(
            [
                'foo' => 'FOO',
                'bar' => 'BAR',
                'baz' => 'BAZ',
            ],
            $builder->build(),
            [],
        );

        $this->assertFalse($validator->passes());
        $messages = $validator->messages();
        $this->assertEquals([
            'validation.unknown_property',
        ], $messages->get('baz'));
    }

    protected function makeValidator(array $data, array $rules, array $messages): StrictValidator
    {
        return new StrictValidator(
            new Translator(new ArrayLoader(), 'en'),
            $data,
            $rules,
            $messages,
        );
    }
}