<?php

namespace Nails\Common\Validation;

use Nails\Common\Service\Translation;
use Throwable;

/**
 * Class MessageResolver
 *
 * Works out the error message for a failed rule and interpolates the field
 * label and rule parameter into it.
 *
 * @package Nails\Common\Validation
 */
final class MessageResolver
{
    public const LANG_PREFIX  = 'fv_';
    public const LANG_SUFFIX  = '_field';
    public const LABEL_PREFIX = 'lang:';

    // --------------------------------------------------------------------------

    public function __construct(private readonly Translation $oTranslation)
    {
    }

    // --------------------------------------------------------------------------

    /**
     * Resolves the message for a failed rule. Precedence:
     *  1. the field's own per-rule message
     *  2. the global per-rule message (set_message / Validator messages)
     *  3. the language line (`fv_<rule>` / `fv_<rule>_field`, order depending on style)
     *  4. the rule's default message
     *
     * @param ResolvedRule          $oResolved       The rule which failed
     * @param Field                 $oField          The field it failed on
     * @param State                 $oState          The run state (for other fields' labels)
     * @param array<string, string> $aGlobalMessages rule => message
     */
    public function resolve(ResolvedRule $oResolved, Field $oField, State $oState, array $aGlobalMessages): string
    {
        $aNames = $oResolved->getNames();
        $sLine  = $this->firstNonEmpty($aNames, $oField->messages)
            ?? $this->firstNonEmpty($aNames, $aGlobalMessages)
            ?? $this->fromLanguage($aNames, $oState->getMessageStyle())
            ?? $oResolved->rule->getDefaultMessage();

        $sParam = $oResolved->param ?? '';
        if ($sParam !== '' && $oState->getRuleSet()->has($sParam)) {
            $sParam = $this->translateLabel($oState->getRuleSet()->get($sParam)->getLabel());
        }

        return $this->interpolate($sLine, $this->translateLabel($oField->getLabel()), $sParam);
    }

    // --------------------------------------------------------------------------

    /**
     * Translates a `lang:key` label; other labels are returned as-is
     */
    public function translateLabel(string $sLabel): string
    {
        if (str_starts_with($sLabel, self::LABEL_PREFIX)) {
            $sKey  = substr($sLabel, strlen(self::LABEL_PREFIX));
            $sLine = $this->oTranslation->line($sKey);
            return $sLine === false ? $sKey : $sLine;
        }

        return $sLabel;
    }

    // --------------------------------------------------------------------------

    /**
     * Interpolates the label and parameter into a message (ports CI's _build_error_msg):
     * a literal `%s` selects sprintf() for legacy strings, otherwise `{field}` and
     * `{param}` are replaced.
     */
    public function interpolate(string $sLine, string $sLabel, string $sParam): string
    {
        if (str_contains($sLine, '%s')) {
            try {
                return sprintf($sLine, $sLabel, $sParam);
            } catch (Throwable) {
                //  Fall through to placeholder replacement
            }
        }

        return str_replace(['{field}', '{param}'], [$sLabel, $sParam], $sLine);
    }

    // --------------------------------------------------------------------------

    /**
     * @param string[]             $aNames
     * @param array<string, mixed> $aMessages
     */
    private function firstNonEmpty(array $aNames, array $aMessages): ?string
    {
        foreach ($aNames as $sName) {
            $mMessage = $aMessages[$sName] ?? null;
            if (is_string($mMessage) && $mMessage !== '') {
                return $mMessage;
            }
        }
        return null;
    }

    // --------------------------------------------------------------------------

    /**
     * @param string[] $aNames
     */
    private function fromLanguage(array $aNames, MessageStyle $eStyle): ?string
    {
        foreach ($aNames as $sName) {
            $aKeys = $eStyle === MessageStyle::WITH_FIELD
                ? [self::LANG_PREFIX . $sName . self::LANG_SUFFIX, self::LANG_PREFIX . $sName]
                : [self::LANG_PREFIX . $sName, self::LANG_PREFIX . $sName . self::LANG_SUFFIX];

            foreach ($aKeys as $sKey) {
                $mLine = $this->oTranslation->line($sKey);
                if (is_string($mLine) && $mLine !== '') {
                    return $mLine;
                }
            }
        }
        return null;
    }
}
