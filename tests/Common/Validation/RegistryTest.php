<?php

namespace Tests\Common\Validation;

use Nails\Common\Interfaces\Validation\Rule;
use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Adapter\ClosureRule;
use Nails\Common\Validation\Adapter\NativeFunctionRule;
use Nails\Common\Validation\Context;
use Nails\Common\Validation\Exception\UnknownRuleException;
use Nails\Common\Validation\Registry;
use Nails\Common\Validation\Rule\MaxWords;
use Nails\Common\Validation\Rule\Required;
use PHPUnit\Framework\TestCase;

class RegistryTest extends TestCase
{
    public function test_every_bundled_rule_is_discovered(): void
    {
        $aExpected = [
            'alpha', 'alpha_dash', 'alpha_dash_period', 'alpha_numeric', 'alpha_numeric_spaces',
            'date_after', 'date_before', 'date_future', 'date_past', 'date_today',
            'datetime_after', 'datetime_before', 'datetime_future', 'datetime_past',
            'decimal', 'differs', 'encode_php_tags', 'exact_length',
            'greater_than', 'greater_than_equal_to', 'isset', 'in_list', 'in_range', 'integer',
            'is', 'is_array', 'is_bool', 'is_id', 'is_natural', 'is_natural_no_zero', 'is_unique', 'item_count',
            'less_than', 'less_than_equal_to', 'matches', 'max_length', 'maxWords', 'max_words', 'min_length',
            'numeric', 'prep_for_form', 'prep_url', 'regex_match', 'required', 'strip_image_tags',
            'supportedLocale', 'time_after', 'time_before', 'time_future', 'time_past', 'trim', 'unique_if_diff',
            'valid_base64', 'valid_date', 'valid_datetime', 'valid_email', 'valid_emails', 'valid_ip', 'valid_mac',
            'valid_postcode', 'valid_time', 'validTimecode', 'valid_url',
        ];

        $aNames = Registry::discover()->names();
        sort($aExpected);
        sort($aNames);

        self::assertSame($aExpected, $aNames);
    }

    public function test_a_rule_can_be_resolved_by_name_with_a_parameter(): void
    {
        $oResolved = Registry::discover()->resolve('max_length[5]');

        self::assertSame('max_length', $oResolved->rule->getName());
        self::assertSame('5', $oResolved->param);
        self::assertSame('max_length', $oResolved->sourceName);
    }

    public function test_the_parameter_is_passed_verbatim(): void
    {
        $oResolved = Registry::discover()->resolve('regex_match[/^[a-z|]+$/]');
        self::assertSame('/^[a-z|]+$/', $oResolved->param);

        $oResolved = Registry::discover()->resolve('is_unique[table.column.1.id]');
        self::assertSame('table.column.1.id', $oResolved->param);
    }

    public function test_a_rule_can_be_resolved_by_alias(): void
    {
        $oResolved = Registry::discover()->resolve('max_words[3]');

        self::assertInstanceOf(MaxWords::class, $oResolved->rule);
        self::assertSame(['max_words', 'maxWords'], $oResolved->getNames());
    }

    public function test_a_rule_can_be_resolved_by_class_name(): void
    {
        $oResolved = Registry::discover()->resolve(Required::class);
        self::assertInstanceOf(Required::class, $oResolved->rule);

        $oResolved = Registry::discover()->resolve(MaxWords::class . '[2]');
        self::assertInstanceOf(MaxWords::class, $oResolved->rule);
        self::assertSame('2', $oResolved->param);
    }

    public function test_a_rule_instance_is_used_as_is(): void
    {
        $oRule     = new Required();
        $oResolved = Registry::discover()->resolve($oRule);
        self::assertSame($oRule, $oResolved->rule);
    }

    public function test_a_closure_is_wrapped(): void
    {
        $oResolved = Registry::discover()->resolve(fn() => true);
        self::assertInstanceOf(ClosureRule::class, $oResolved->rule);
        self::assertSame('closure', $oResolved->sourceName);
    }

    public function test_a_native_function_is_wrapped(): void
    {
        $oResolved = Registry::discover()->resolve('strtoupper');
        self::assertInstanceOf(NativeFunctionRule::class, $oResolved->rule);
        self::assertSame('strtoupper', $oResolved->rule->getName());
    }

    public function test_dangerous_native_functions_are_refused(): void
    {
        $this->expectException(UnknownRuleException::class);
        Registry::discover()->resolve('exec');
    }

    public function test_an_unknown_rule_throws(): void
    {
        $this->expectException(UnknownRuleException::class);
        $this->expectExceptionMessage('"not_a_rule" is not a recognised validation rule');
        Registry::discover()->resolve('not_a_rule');
    }

    public function test_a_callback_rule_needs_a_target_with_that_method(): void
    {
        $oTarget = new class {
            public function _check($mValue, $sParam)
            {
                return $mValue === 'ok';
            }
        };

        $oResolved = Registry::discover()->resolve('callback__check', $oTarget);
        self::assertSame('_check', $oResolved->rule->getName());

        $this->expectException(UnknownRuleException::class);
        Registry::discover()->resolve('callback__check');
    }

    public function test_later_registrations_win(): void
    {
        $oOverride = new class extends AbstractRule {
            public const NAME = 'required';

            public function apply(mixed $mValue, Context $oContext): bool
            {
                return true;
            }
        };

        $oRegistry = (new Registry())
            ->register(Required::class)
            ->register($oOverride);

        self::assertSame($oOverride, $oRegistry->get('required'));
        self::assertInstanceOf(Rule::class, $oRegistry->get('required'));
    }

    public function test_parse_splits_name_and_parameter(): void
    {
        self::assertSame(['required', null], Registry::parse('required'));
        self::assertSame(['in_list', 'a,b,c'], Registry::parse('in_list[a,b,c]'));
        self::assertSame(['max_length', ''], Registry::parse('max_length[]'));
    }
}
