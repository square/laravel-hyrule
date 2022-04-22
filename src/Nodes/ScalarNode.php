<?php

declare(strict_types=1);

namespace Square\Hyrule\Nodes;

use Illuminate\Contracts\Validation\Rule;
use Square\Hyrule\Build\LazyRuleStringify;

/**
 * @method $this gt($arg)
 * @method $this gte($arg)
 * @method $this lt($arg)
 * @method $this lte($arg)
 * @method $this max($arg)
 * @method $this min($arg)
 * @method $this nullable()
 * @method $this sometimes()
 * @method $this present()
 * @method $this timezone()
 */
class ScalarNode extends AbstractNode
{
    /**
     * @param string $name
     * @param CompoundNode|null $parent
     */
    public function __construct(string $name, ?CompoundNode $parent = null)
    {
        $this->rules = $this->defaultRules();
        parent::__construct($name, $parent);
    }

    /**
     * @return array|string[]|Rule[]|LazyRuleStringify[]
     */
    protected function defaultRules(): array
    {
        return [];
    }
}
