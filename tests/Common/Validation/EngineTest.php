<?php

namespace Tests\Common\Validation;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Service\FormValidation;
use Nails\Common\Validation\Context;
use Nails\Common\Validation\Engine;
use Nails\Common\Validation\Field;
use Nails\Common\Validation\MessageStyle;
use Nails\Common\Validation\Result;
use Nails\Common\Validation\RuleSet;
use Nails\Factory;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    private function engine(): Engine
    {
        /** @var FormValidation $oFormValidation */
        $oFormValidation = Factory::service('FormValidation');
        return $oFormValidation->getEngine();
    }

    /**
     * @param array<string, string|array> $aRules field => rules
     */
    private function validate(array $aRules, array $aData, MessageStyle $eStyle = MessageStyle::PLAIN, array $aMessages = []): Result
    {
        $oRuleSet = new RuleSet();
        foreach ($aRules as $sField => $mRules) {
            $oRuleSet->add(new Field($sField, '', FormValidation::splitRules($mRules)));
        }
        return $this->engine()->run($oRuleSet, $aData, $eStyle, $aMessages);
    }

    // --------------------------------------------------------------------------

    public function test_a_passing_data_set_has_no_errors(): void
    {
        $oResult = $this->validate(['name' => 'required|max_length[5]'], ['name' => 'Ada']);

        self::assertTrue($oResult->passed());
        self::assertSame([], $oResult->getErrors());
        self::assertSame('Ada', $oResult->getValue('name'));
    }

    public function test_required_is_hoisted_before_declared_rules(): void
    {
        $oResult = $this->validate(['email' => 'valid_email|required'], ['email' => '']);

        self::assertSame('This field is required.', $oResult->getError('email'));
    }

    public function test_other_rules_run_in_declared_order(): void
    {
        $oResult = $this->validate([
            'email' => [
                'required',
                'valid_email',
                function ($mValue) {
                    throw new ValidationException('closure ran');
                },
            ],
        ], ['email' => 'not-an-email']);

        self::assertSame('This must be a valid email.', $oResult->getError('email'));

        $oResult = $this->validate([
            'email' => [
                function ($mValue) {
                    throw new ValidationException('closure ran');
                },
                'valid_email',
            ],
        ], ['email' => 'not-an-email']);

        self::assertSame('closure ran', $oResult->getError('email'));
    }

    public function test_only_the_first_failure_per_field_is_reported(): void
    {
        $oResult = $this->validate(['name' => 'alpha|max_length[1]'], ['name' => '12']);

        self::assertSame('This field may only contain alphabetical characters.', $oResult->getError('name'));
    }

    public function test_empty_values_skip_non_required_rules(): void
    {
        $oResult = $this->validate(
            ['a' => 'valid_email', 'b' => 'valid_email', 'c' => 'valid_email', 'd' => 'valid_email'],
            ['a' => null, 'b' => '', 'c' => [], 'd' => '0']
        );

        self::assertSame(['d'], array_keys($oResult->getErrors()));
    }

    public function test_required_fails_on_empty_values(): void
    {
        $oResult = $this->validate(
            ['a' => 'required', 'b' => 'required', 'c' => 'required', 'd' => 'required', 'e' => 'required'],
            ['a' => null, 'b' => '', 'c' => [], 'd' => '  ', 'e' => '0']
        );

        self::assertSame(['a', 'b', 'c', 'd'], array_keys($oResult->getErrors()));
    }

    public function test_closures_run_on_empty_values(): void
    {
        $bRan = false;
        $this->validate(['a' => [function ($mValue) use (&$bRan) {
            $bRan = true;
        }]], ['a' => '']);

        self::assertTrue($bRan);
    }

    public function test_matches_runs_on_empty_values(): void
    {
        $oResult = $this->validate(['confirm' => 'matches[password]', 'password' => 'required'], ['password' => 'x', 'confirm' => '']);
        self::assertSame('This field does not match the password field.', $oResult->getError('confirm'));
    }

    public function test_array_values_are_validated_element_by_element(): void
    {
        $oResult = $this->validate(['n[]' => 'is_natural'], ['n' => ['1', 'x', '3']]);
        self::assertSame('This field must be a natural number (0, 1, 2, 3, etc).', $oResult->getError('n[]'));

        $oResult = $this->validate(['n[]' => 'is_natural'], ['n' => ['1', '2']]);
        self::assertTrue($oResult->passed());
    }

    public function test_is_array_marks_a_field_as_accepting_arrays(): void
    {
        $oResult = $this->validate(['opts[]' => 'is_array'], ['opts' => ['a' => 'b']]);
        self::assertTrue($oResult->passed());

        $oResult = $this->validate(['opts' => 'is_array'], ['opts' => 'not an array']);
        self::assertSame('This field must be an array.', $oResult->getError('opts'));
    }

    public function test_bracketed_paths_are_resolved(): void
    {
        $oResult = $this->validate(
            ['q[0][a]' => 'required', 'q[1][a]' => 'required', 'q[2][a]' => 'required'],
            ['q' => [['a' => 'yes'], ['a' => ''], []]]
        );

        self::assertSame(['q[1][a]', 'q[2][a]'], array_keys($oResult->getErrors()));
        self::assertSame('yes', $oResult->getValue('q[0][a]'));
    }

    public function test_mutating_rules_change_the_value_seen_by_later_rules_and_the_data(): void
    {
        $oResult = $this->validate(['name' => 'trim|max_length[3]'], ['name' => '  abcd ']);
        self::assertSame('This field is too long, maximum length is 3 characters.', $oResult->getError('name'));

        $oResult = $this->validate(['name' => 'trim|max_length[3]', 'other' => 'trim'], ['name' => ' ab ', 'other' => ' x ', 'untouched' => ' y ']);
        self::assertTrue($oResult->passed());
        self::assertSame(['name' => 'ab', 'other' => 'x', 'untouched' => ' y '], $oResult->getData());
    }

    public function test_mutations_apply_to_array_elements(): void
    {
        $oResult = $this->validate(['tags[]' => 'trim'], ['tags' => [' a ', ' b']]);
        self::assertSame(['tags' => ['a', 'b']], $oResult->getData());
    }

    public function test_closures_receive_the_context_and_can_read_other_fields(): void
    {
        $oResult = $this->validate([
            'group_id' => 'integer',
            'password' => [function ($mValue, Context $oContext) {
                if ($oContext->getValue('group_id') === '2' && $mValue === 'weak') {
                    throw new ValidationException('Too weak for group 2');
                }
            }],
        ], ['group_id' => '2', 'password' => 'weak']);

        self::assertSame('Too weak for group 2', $oResult->getError('password'));
    }

    public function test_closures_see_mutated_values_of_earlier_fields(): void
    {
        $oResult = $this->validate([
            'a' => 'trim',
            'b' => [function ($mValue, Context $oContext) {
                if ($oContext->getValue('a') !== 'x') {
                    throw new ValidationException('not trimmed');
                }
            }],
        ], ['a' => ' x ', 'b' => 'anything']);

        self::assertTrue($oResult->passed());
    }

    public function test_a_closure_returning_false_fails(): void
    {
        $oResult = $this->validate(['a' => [fn() => false]], ['a' => 'x']);
        self::assertSame('Field failed validation.', $oResult->getError('a'));
    }

    public function test_legacy_closures_with_a_single_parameter_still_work(): void
    {
        $oResult = $this->validate(['a' => [function ($mValue) {
            if ($mValue !== 'ok') {
                throw new ValidationException('nope');
            }
        }]], ['a' => 'ok']);

        self::assertTrue($oResult->passed());
    }

    public function test_callback_rules_use_the_target_object(): void
    {
        $oTarget = new class {
            public function _isOk($mValue, $sParam)
            {
                return $mValue === 'ok';
            }
        };

        $oRuleSet = (new RuleSet())->add(new Field('a', '', ['callback__isOk']));
        $oResult  = $this->engine()->run($oRuleSet, ['a' => 'no'], MessageStyle::PLAIN, [], $oTarget);

        self::assertSame('Field failed validation.', $oResult->getError('a'));
    }

    public function test_native_functions_can_mutate(): void
    {
        $oResult = $this->validate(['a' => 'strtoupper'], ['a' => 'abc']);
        self::assertSame('ABC', $oResult->getValue('a'));
    }

    public function test_with_field_style_uses_the_field_variant_and_label(): void
    {
        $oRuleSet = (new RuleSet())->add(new Field('email', 'Email address', ['required']));
        $oResult  = $this->engine()->run($oRuleSet, [], MessageStyle::WITH_FIELD);

        self::assertSame('The Email address field is required.', $oResult->getError('email'));
    }

    public function test_global_messages_override_the_language_line(): void
    {
        $oResult = $this->validate(['a' => 'max_length[2]'], ['a' => 'abc'], MessageStyle::PLAIN, ['max_length' => 'Max {param}!']);
        self::assertSame('Max 2!', $oResult->getError('a'));
    }

    public function test_fields_without_rules_are_ignored_but_declared(): void
    {
        $oResult = $this->validate(['a' => '', 'b' => 'required'], ['a' => 'x', 'b' => 'y']);
        self::assertTrue($oResult->passed());
        self::assertTrue($oResult->hasField('a'));
        self::assertSame('x', $oResult->getValue('a'));
    }
}
