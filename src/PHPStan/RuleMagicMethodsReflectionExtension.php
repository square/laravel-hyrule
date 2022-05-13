<?php

namespace Square\Hyrule\PHPStan;

use Illuminate\Validation\Validator;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use ReflectionClass;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use RuntimeException;
use Square\Hyrule\Nodes\AbstractNode;

/**
 * Provide PHPStan knowledge about known Laravel validation rules.
 */
class RuleMagicMethodsReflectionExtension implements MethodsClassReflectionExtension
{
    /**
     * @var array<string,bool|MethodReflection>
     */
    protected $knownRuleMethods = [];

    public function __construct()
    {
        $reflectionClass = new ReflectionClass(Validator::class);
        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $methodName = $reflectionMethod->getName();
            if (preg_match('/^validate[A-Z]+.+$/', $methodName)) {
                $ruleName = lcfirst(substr($methodName, 8));
                $this->knownRuleMethods[$ruleName] = true;
            }
        }
    }

    /**
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return bool
     */
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        return is_a($classReflection->getName(), AbstractNode::class, true)
            && array_key_exists($methodName, $this->knownRuleMethods);
    }

    /**
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return MethodReflection
     */
    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return $this->returnOrCreateMethodReflection($classReflection, $methodName);
    }

    /**
     * @param ClassReflection $classReflection
     * @param string $methodName
     * @return RuleMethodReflection
     */
    private function returnOrCreateMethodReflection(ClassReflection $classReflection, string $methodName)
    {
        if (!array_key_exists($methodName, $this->knownRuleMethods))  {
            throw new RuntimeException(sprintf('%s is not a known rule method.', $methodName));
        }

        if (!$this->knownRuleMethods[$methodName] instanceof RuleMethodReflection) {
            $this->knownRuleMethods[$methodName] = new RuleMethodReflection(
                $methodName,
                $classReflection,
                new RuleMethodClassMemberReflection($classReflection),
            );
        }

        return $this->knownRuleMethods[$methodName];
    }
}
