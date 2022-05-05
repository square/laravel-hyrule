<?php

namespace Square\Hyrule;

use Illuminate\Contracts\Validation\Factory as FactoryContract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Factory;
use RuntimeException;
use Square\Hyrule\Validator\StrictValidator;

class HyruleServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function register()
    {
        $this->extendValidatorFactory(config('hyrule.strict_validator_class'));
    }

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/hyrule.php' => config_path('hyrule.php'),
        ]);
    }

    /**
     * @param string|bool|null $className
     * @return void
     */
    private function extendValidatorFactory($className): void
    {
        if (!is_string($className) || empty($className)) {
            return;
        }

        if (!is_a($className, Validator::class, true)) {
            throw new RuntimeException(sprintf(
                'hyrule.strict_validator_class string value must be class that implements %s. Got %s.',
                Validator::class,
                $className,
            ));
        }

        $this->app->extend(FactoryContract::class, function (FactoryContract $factory) use ($className) {
            if (!$factory instanceof Factory) {
                throw new RuntimeException(sprintf(
                    'Expected bound instance for %s to be of type %s. Got %s.',
                    FactoryContract::class,
                    Factory::class,
                    get_class($factory),
                ));
            }
            $factory->resolver(function (...$args) use ($className) {
                return new $className(...$args);
            });
            return $factory;
        });
    }
}