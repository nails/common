<?php

namespace Nails\Common\Validation;

use Nails\Common\Interfaces\Validation\Rule;

/**
 * A rule as resolved from a rule list entry, together with its parameter and
 * the name it was referenced by
 */
final class ResolvedRule
{
    public function __construct(
        public readonly Rule $rule,
        public readonly ?string $param,
        public readonly string $sourceName,
    ) {
    }

    /**
     * Every name this rule may be referred to by (as written, canonical, aliases)
     *
     * @return string[]
     */
    public function getNames(): array
    {
        return array_values(array_unique(array_filter(array_merge(
            [$this->sourceName, $this->rule->getName()],
            $this->rule->getAliases()
        ))));
    }
}
