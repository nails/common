<?php

namespace Nails\Common\Validation;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Service\Translation;
use Nails\Common\Validation\Exception\UnknownRuleException;

/**
 * Class Engine
 *
 * Runs a RuleSet against a data set. Semantics follow CodeIgniter's
 * Form_validation (empty-value skipping, per-element validation of array
 * values, value mutation, first failure per field) minus the superobject,
 * with one deliberate change: rules run in declared order, with
 * `required`/`isset` hoisted to the front.
 *
 * @package Nails\Common\Validation
 */
final class Engine
{
    /**
     * Rules which are always executed first
     */
    public const HOISTED_RULES = ['required', 'isset'];

    // --------------------------------------------------------------------------

    public function __construct(
        private readonly Registry $oRegistry,
        private readonly MessageResolver $oMessages,
        private readonly Translation $oTranslation,
    ) {
    }

    // --------------------------------------------------------------------------

    public function getRegistry(): Registry
    {
        return $this->oRegistry;
    }

    public function getMessageResolver(): MessageResolver
    {
        return $this->oMessages;
    }

    // --------------------------------------------------------------------------

    /**
     * Validates $aData against $oRuleSet
     *
     * @param RuleSet               $oRuleSet        The fields and their rules
     * @param array                 $aData           The data to validate
     * @param MessageStyle          $eStyle          Which default message flavour to prefer
     * @param array<string, string> $aGlobalMessages Per-rule message overrides (rule => message)
     * @param object|null           $oCallbackTarget The object `callback_*` rules are methods of
     *
     * @throws UnknownRuleException
     */
    public function run(
        RuleSet $oRuleSet,
        array $aData,
        MessageStyle $eStyle = MessageStyle::PLAIN,
        array $aGlobalMessages = [],
        ?object $oCallbackTarget = null,
    ): Result {

        $oState  = new State($oRuleSet, $aData, $eStyle, $this->oTranslation);
        $aErrors = [];

        foreach ($oRuleSet as $oField) {

            if (empty($oField->rules)) {
                continue;
            }

            $aResolved = $this->prepare($oField->rules, $oCallbackTarget);
            $sError    = $this->executeField($oField, $aResolved, $oState, $aGlobalMessages);

            if ($sError !== null) {
                $aErrors[$oField->name] = $sError;
            }
        }

        return new Result($oRuleSet, $aErrors, $oState->getValues(), $oState->getData());
    }

    // --------------------------------------------------------------------------

    /**
     * Resolves a field's rules and orders them for execution
     *
     * @return ResolvedRule[]
     */
    private function prepare(array $aRules, ?object $oCallbackTarget): array
    {
        $aHoisted = [];
        $aOthers  = [];

        foreach ($aRules as $mRule) {

            if ($mRule === null || $mRule === '') {
                continue;
            }

            $oResolved = $this->oRegistry->resolve($mRule, $oCallbackTarget);

            if (in_array($oResolved->rule->getName(), self::HOISTED_RULES, true)) {
                $aHoisted[] = $oResolved;
            } else {
                $aOthers[] = $oResolved;
            }
        }

        //  `required` always goes before `isset`
        usort($aHoisted, fn(ResolvedRule $a, ResolvedRule $b) => array_search($a->rule->getName(), self::HOISTED_RULES, true)
            <=> array_search($b->rule->getName(), self::HOISTED_RULES, true));

        return array_merge($aHoisted, $aOthers);
    }

    // --------------------------------------------------------------------------

    /**
     * Validates one field; array values are validated element by element unless
     * a rule accepts arrays. Returns the first error message, if any.
     *
     * @param ResolvedRule[] $aResolved
     */
    private function executeField(Field $oField, array $aResolved, State $oState, array $aGlobalMessages): ?string
    {
        $bAllowArrays = false;
        foreach ($aResolved as $oResolved) {
            if ($oResolved->rule->acceptsArrays()) {
                $bAllowArrays = true;
                break;
            }
        }

        $mValue = $oState->getValue($oField->name);

        if (!$bAllowArrays && is_array($mValue)) {

            if (empty($mValue)) {
                //  An unexpected (empty) array is treated as an empty field
                return $this->executeValue($oField, $aResolved, $bAllowArrays, null, null, $oState, $aGlobalMessages);
            }

            $sFirstError = null;
            foreach ($mValue as $mKey => $mElement) {
                $sError = $this->executeValue($oField, $aResolved, $bAllowArrays, $mElement, $mKey, $oState, $aGlobalMessages);
                if ($sError !== null && $sFirstError === null) {
                    $sFirstError = $sError;
                }
            }
            return $sFirstError;
        }

        return $this->executeValue($oField, $aResolved, $bAllowArrays, $mValue, null, $oState, $aGlobalMessages);
    }

    // --------------------------------------------------------------------------

    /**
     * Runs the rules against a single value, stopping at the first failure
     *
     * @param ResolvedRule[] $aResolved
     */
    private function executeValue(
        Field $oField,
        array $aResolved,
        bool $bAllowArrays,
        mixed $mValue,
        int|string|null $mIndex,
        State $oState,
        array $aGlobalMessages,
    ): ?string {

        foreach ($aResolved as $oResolved) {

            $oRule = $oResolved->rule;

            $bIsEmpty = $mValue === null || (!$bAllowArrays && $mValue === '');
            if ($bIsEmpty && !$oRule->runsOnEmpty()) {
                continue;
            }

            $oContext = new Context($oState, $oField, $oResolved->param, $mIndex);

            try {
                $bPassed = $oRule->apply($mValue, $oContext);
            } catch (ValidationException $e) {
                return $e->getMessage();
            }

            if ($oContext->hasMutation()) {
                $mValue = $oContext->getMutatedValue();
                $oState->setValue($oField, $mValue, $mIndex);
            }

            if (!$bPassed) {
                return $this->oMessages->resolve($oResolved, $oField, $oState, $aGlobalMessages);
            }
        }

        return null;
    }
}
