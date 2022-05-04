<?php

namespace Square\Hyrule;

use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use RuntimeException;
use Square\Hyrule\Validator\StrictValidator;

class HyruleServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (config('hyrule.use_strict_validator', false)) {
            $this->extendValidatorFactory();
        }
    }

    public function boot()
    {
        // No-op.
    }

    private function extendValidatorFactory()
    {
        $this->app->extend(FactoryContract::class, function (FactoryContract $factory) {
            if (!$factory instanceof Factory) {
                throw new RuntimeException(sprintf(
                    'Expected bound instance for %s to be of type %s. Got %s.',
                    FactoryContract::class,
                    Factory::class,
                    get_class($factory),
                ));
            }
            $factory->resolver(function (...$args) {
                return new StrictValidator(...$args);
            });
            return $factory;
        });
    }
}