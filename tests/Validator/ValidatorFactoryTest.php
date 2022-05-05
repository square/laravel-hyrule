<?php

namespace Square\Hyrule\Tests\Validator;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Validation\Validator;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Square\Hyrule\Validator\StrictValidator;

class ValidatorFactoryTest extends TestCase
{
    /**
     * @var Application
     */
    protected Application $app;

    /**
     * @var Kernel
     */
    private Kernel $kernel;

    public function setUp(): void
    {
        $this->app = require __DIR__ . '/../app/bootstrap/app.php';
        $this->kernel = $this->app->make(Kernel::class);
    }

    public function testCreatedValidatorWithEmptyValue(): void
    {
        putenv('STRICT_VALIDATOR_CLASS=') ;
        $this->kernel->bootstrap();
        $factory = $this->app->make(Factory::class);
        $this->assertInstanceOf(Factory::class, $factory);
        $validator = $factory->make([], [], []);
        $this->assertEquals(Validator::class, get_class($validator));
    }

    public function testCreatedValidatorWithDefaultConfig(): void
    {
        $defaultConfig = require __DIR__ . '/../../src/config/hyrule.php';
        $this->assertEquals(StrictValidator::class, $defaultConfig['use_strict_validator_class']);
        putenv(sprintf('STRICT_VALIDATOR_CLASS=%s', $defaultConfig['use_strict_validator_class']));
        $this->kernel->bootstrap();
        $factory = $this->app->make(Factory::class);
        $this->assertInstanceOf(Factory::class, $factory);
        $validator = $factory->make([], [], []);
        $this->assertEquals(StrictValidator::class, get_class($validator));
    }

    public function testCreatedValidatorWithCustomValidator(): void
    {
        putenv(sprintf('STRICT_VALIDATOR_CLASS=%s', CustomValidator::class));
        $this->kernel->bootstrap();
        $factory = $this->app->make(Factory::class);
        $this->assertInstanceOf(Factory::class, $factory);
        $validator = $factory->make([], [], []);
        $this->assertEquals(CustomValidator::class, get_class($validator));
    }

    public function testIncompatibleClassInConfig(): void
    {
        putenv(sprintf('STRICT_VALIDATOR_CLASS=%s', Kernel::class));
        $this->expectException(RuntimeException::class);
        $this->kernel->bootstrap();
    }
}