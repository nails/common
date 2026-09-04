<?php

/**
 * A validation rule
 *
 * Rules are auto-discovered from every component's `Validation\Rule` namespace
 * (e.g. `Nails\Common\Validation\Rule\Required`, `App\Validation\Rule\Foo`) and
 * are referenced by their string name (`required`), by class name, or as an instance.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Interface
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Interfaces\Validation;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Validation\Context;

interface Rule
{
    /**
     * The rule's wire name, e.g. `required`; also the suffix of its language key (`fv_<name>`)
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Alternative wire names for this rule
     *
     * @return string[]
     */
    public function getAliases(): array;

    /**
     * Whether the rule should be applied to empty values (null or ''). Most rules
     * are skipped for empty values so that optional fields validate cleanly.
     *
     * @return bool
     */
    public function runsOnEmpty(): bool;

    /**
     * Whether the rule accepts array values as a whole. When false, an array value
     * is validated element by element.
     *
     * @return bool
     */
    public function acceptsArrays(): bool;

    /**
     * The default (English) error message; may contain the `{field}` and `{param}` placeholders
     *
     * @return string
     */
    public function getDefaultMessage(): string;

    /**
     * Applies the rule to a value.
     *
     * Return true to pass, false to fail with the resolved message, or throw a
     * ValidationException to fail with a specific message. Call
     * `$oContext->setValue()` to replace the value (e.g. trimming).
     *
     * @param mixed   $mValue   The value being validated
     * @param Context $oContext The validation context
     *
     * @return bool
     * @throws ValidationException
     */
    public function apply(mixed $mValue, Context $oContext): bool;
}
