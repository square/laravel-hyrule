<?php

namespace Square\Hyrule\PHPStan;

use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;

class RuleMethodClassMemberReflection implements ClassMemberReflection
{
    /**
     * @var ClassReflection
     */
    private $classReflection;

    public function __construct(ClassReflection $classReflection)
    {
        $this->classReflection = $classReflection;
    }

    public function getDeclaringClass(): ClassReflection
    {
        return $this->classReflection;
    }

    public function isStatic(): bool
    {
        return false;
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
