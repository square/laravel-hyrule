## Static Analysis with PHPStan

If you use PHPStan to perform static analysis in your code, you can register the extension provided w/ this package so PHPStan understands
some method-call chains that it cannot naturally infer:

```yaml
# phpstan.neon
services:
    - class: Square\Hyrule\PHPStan\ArrayNodeEachDynamicReturnExtension
      tags:
          - phpstan.broker.dynamicMethodReturnTypeExtension
    - class: Square\Hyrule\PHPStan\RuleMagicMethodsReflectionExtension
      tags:
        - phpstan.broker.methodsClassReflectionExtension
```

