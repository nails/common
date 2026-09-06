<?php

namespace Nails\Common\Validation;

use Closure;
use Nails\Common\Interfaces\Validation\Rule;
use Nails\Common\Validation\Adapter\CallbackRule;
use Nails\Common\Validation\Adapter\ClosureRule;
use Nails\Common\Validation\Adapter\NativeFunctionRule;
use Nails\Common\Validation\Exception\UnknownRuleException;
use Nails\Components;

/**
 * Class Registry
 *
 * Knows every available rule by name. Rules are discovered from each
 * component's `Validation\Rule` namespace; on a name collision the app wins
 * over drivers and skins, which win over modules, which win over common.
 *
 * @package Nails\Common\Validation
 */
final class Registry
{
    /**
     * Native PHP functions which may never be used as a rule
     */
    public const NATIVE_DENYLIST = [
        'assert', 'call_user_func', 'call_user_func_array', 'dl', 'eval', 'exec', 'extract',
        'file_put_contents', 'fopen', 'header', 'include', 'include_once', 'mail', 'passthru',
        'pcntl_exec', 'popen', 'proc_open', 'require', 'require_once', 'rmdir', 'setcookie',
        'shell_exec', 'system', 'unlink', 'unserialize',
    ];

    /**
     * The prefix CodeIgniter used for controller-method callbacks
     */
    public const CALLBACK_PREFIX = 'callback_';

    /**
     * The namespace (relative to the component's root namespace) rules are discovered from
     */
    public const DISCOVERY_NAMESPACE = 'Validation\\Rule';

    // --------------------------------------------------------------------------

    /** @var array<string, Rule> */
    private array $aRules = [];

    private static ?self $oDiscovered = null;

    // --------------------------------------------------------------------------

    /**
     * @param string[] $aDenylist Native functions which may not be used as rules
     */
    public function __construct(private readonly array $aDenylist = self::NATIVE_DENYLIST)
    {
    }

    // --------------------------------------------------------------------------

    /**
     * Builds (and caches) a registry populated from every component
     */
    public static function discover(bool $bUseCache = true): self
    {
        if ($bUseCache && self::$oDiscovered !== null) {
            return self::$oDiscovered;
        }

        $oRegistry = new self();

        //  Components are returned app-first; process the app last so that it wins
        $aComponents = Components::available();
        $oApp        = array_shift($aComponents);
        $aComponents[] = $oApp;

        foreach ($aComponents as $oComponent) {
            $aClasses = $oComponent
                ->findClasses(self::DISCOVERY_NAMESPACE)
                ->whichImplement(Rule::class)
                ->whichCanBeInstantiated();

            foreach ($aClasses as $sClass) {
                $oRegistry->register($sClass);
            }
        }

        return self::$oDiscovered = $oRegistry;
    }

    // --------------------------------------------------------------------------

    /**
     * Registers a rule under its name and aliases; later registrations win
     *
     * @param Rule|class-string<Rule> $mRule
     */
    public function register(Rule|string $mRule): self
    {
        $oRule = $mRule instanceof Rule ? $mRule : new $mRule();

        foreach (array_merge([$oRule->getName()], $oRule->getAliases()) as $sName) {
            if ($sName !== '') {
                $this->aRules[$sName] = $oRule;
            }
        }

        return $this;
    }

    public function has(string $sName): bool
    {
        return array_key_exists($sName, $this->aRules);
    }

    /**
     * @throws UnknownRuleException
     */
    public function get(string $sName): Rule
    {
        return $this->aRules[$sName] ?? throw new UnknownRuleException(
            sprintf('"%s" is not a registered validation rule', $sName)
        );
    }

    /**
     * @return string[]
     */
    public function names(): array
    {
        return array_keys($this->aRules);
    }

    // --------------------------------------------------------------------------

    /**
     * Splits `name[param]` into its parts; the parameter is returned verbatim
     *
     * @return array{0: string, 1: string|null}
     */
    public static function parse(string $sRule): array
    {
        $sRule = trim($sRule);
        if (preg_match('/^(.*?)\[(.*)\]$/s', $sRule, $aMatches)) {
            return [$aMatches[1], $aMatches[2]];
        }
        return [$sRule, null];
    }

    // --------------------------------------------------------------------------

    /**
     * Resolves a rule list entry to a Rule
     *
     * @param string|Rule|Closure $mRule           A rule name (optionally with `[param]`), a Rule class
     *                                             name, a Rule instance or a closure
     * @param object|null         $oCallbackTarget The object `callback_*` rules are methods of
     *
     * @throws UnknownRuleException
     */
    public function resolve(string|Rule|Closure $mRule, ?object $oCallbackTarget = null): ResolvedRule
    {
        if ($mRule instanceof Rule) {
            return new ResolvedRule($mRule, null, $mRule->getName());
        }

        if ($mRule instanceof Closure) {
            return new ResolvedRule(new ClosureRule($mRule), null, ClosureRule::NAME);
        }

        [$sName, $sParam] = self::parse($mRule);

        if ($sName === '') {
            throw new UnknownRuleException('Empty validation rule');
        }

        if (str_starts_with($sName, self::CALLBACK_PREFIX)) {
            $sMethod = substr($sName, strlen(self::CALLBACK_PREFIX));
            if ($oCallbackTarget !== null && method_exists($oCallbackTarget, $sMethod)) {
                return new ResolvedRule(new CallbackRule($oCallbackTarget, $sMethod), $sParam, $sMethod);
            }
            throw new UnknownRuleException(
                sprintf('Unable to find callback validation rule "%s"', $sMethod)
            );
        }

        if (str_contains($sName, '\\') && class_exists($sName) && is_subclass_of($sName, Rule::class)) {
            $oRule = new $sName();
            return new ResolvedRule($oRule, $sParam, $oRule->getName());
        }

        if ($this->has($sName)) {
            return new ResolvedRule($this->get($sName), $sParam, $sName);
        }

        if (function_exists($sName) && !in_array(strtolower($sName), $this->aDenylist, true)) {
            return new ResolvedRule(new NativeFunctionRule($sName), $sParam, $sName);
        }

        throw new UnknownRuleException(
            sprintf('"%s" is not a recognised validation rule', $sName)
        );
    }
}
