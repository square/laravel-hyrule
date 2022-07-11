<?php

namespace Square\Hyrule\Build;

use Illuminate\Contracts\Validation\Rule;
use InvalidArgumentException;
use Stringable;

final class RuleItem
{
    private Rule $rule;

    private string $ruleName;

    private array $ruleArguments;

    private string $ruleString;

    private Stringable $stringable;

    private function __construct()
    {
        // No-op.
    }

    public static function fromStringable(Stringable $stringable): self
    {
        $item = new self();
        $item->stringable = $stringable;
        return $item;
    }

    public static function fromRuleString(string $ruleString): self
    {
        $item = new self();
        $item->ruleString = $ruleString;
        return $item;
    }

    public static function fromRuleArguments(string $rule, array $arguments): self
    {
        $item = new self();
        $item->ruleName = $rule;
        $item->ruleArguments = $arguments;
        return $item;
    }

    public static function fromRule(Rule $rule): self
    {
        $item = new self();
        $item->rule = $rule;
        return $item;
    }

    /**
     * @return Rule|string|Stringable
     */
    public function render(): Rule|string|Stringable
    {
        if (isset($this->rule)) {
            return $this->rule;
        }

        if (isset($this->stringable)) {
            return $this->stringable;
        }

        if (isset($this->ruleString)) {
            return $this->ruleString;
        }

        return sprintf(
            '%s:%s',
            $this->ruleName,
            implode(
                ',',
                array_map(
                    '\Square\Hyrule\Build\RuleItem::normalizeRuleArgumentValue',
                    $this->ruleArguments,
                ),
            ),
        );
    }

    /**
     * @param mixed $value
     * @return string|Stringable
     */
    public static function normalizeRuleArgumentValue(mixed $value): Stringable|string
    {
        switch (gettype($value)) {
            case 'string':
                return $value;
            case 'double':
            case 'integer':
                return (string) $value;
            case 'boolean':
                return $value ? 'true' : 'false';
            case 'NULL':
                return 'NULL';
            case 'array':
                throw new InvalidArgumentException('Cannot use arrays as argument to string-based rules.');
            case 'object':
                if ($value instanceof Stringable) {
                    /*
                     *  If the value is Stringable, don't convert to string yet. Let's leave it to consuming
                     *  code to convert it to string at the right time e.g. last possible minute.
                     *  */
                    return $value;
                }
                throw new InvalidArgumentException(
                    'Objects must implement Stringable if used as arguments to string-based rules.',
                );
            default:
                throw new InvalidArgumentException(sprintf(
                    'Cannot use %s as argument to string-based rules.',
                    gettype($value),
                ));
        }
    }
}