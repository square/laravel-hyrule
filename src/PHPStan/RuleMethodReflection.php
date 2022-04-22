<?php

namespace Square\Hyrule\PHPStan;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptor;
use PHPStan\Reflection\TrivialParametersAcceptor;
use PHPStan\TrinaryLogic;
use PHPStan\Type\Type;

class RuleMethodReflection implements MethodReflection
{
    /**
     * @var string
     */
    private string $name;

    /**
     * @var ClassReflection
     */
    private $declaringClass;

    /**
     * @var ClassMemberReflection
     */
    private $classMember;

    /**
     * @param string $name
     * @param ClassReflection $declaringClass
     * @param ClassMemberReflection $classMember
     */
    public function __construct(string $name, ClassReflection $declaringClass, ClassMemberReflection $classMember)
    {
        $this->name = $name;
        $this->declaringClass = $declaringClass;
        $this->classMember = $classMember;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrototype(): ClassMemberReflection
    {
        return $this->classMember;
    }

    public function getVariants(): array
    {
        return [
            new TrivialParametersAcceptor(),
        ];
    }

    public function isDeprecated(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function getDeprecatedDescription(): ?string
    {
        return null;
    }

    public function isFinal(): TrinaryLogic
    {
        return TrinaryLogic::createYes();
    }

    public function isInternal(): TrinaryLogic
    {
        return TrinaryLogic::createNo();
    }

    public function getThrowType(): ?Type
    {
        return null;
    }

    public function hasSideEffects(): TrinaryLogic
    {
        return TrinaryLogic::createYes();
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->declaringClass;
    }

    public function isStatic(): bool
    {
        return false;
        // TODO: Implement isStatic() method.
    }

    public function isPrivate(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function getDocComment(): ?string
    {
        return null;
    }
}
