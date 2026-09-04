<?php

namespace Tests\Common\Validation;

use Nails\Common\Service\Translation;
use Nails\Common\Validation\Field;
use Nails\Common\Validation\MessageResolver;
use Nails\Common\Validation\MessageStyle;
use Nails\Common\Validation\Registry;
use Nails\Common\Validation\RuleSet;
use Nails\Common\Validation\State;
use PHPUnit\Framework\TestCase;

class MessageResolverTest extends TestCase
{
    private Translation $oTranslation;

    protected function setUp(): void
    {
        $this->oTranslation = new Translation('english');
    }

    private function resolve(
        string $sRule,
        Field $oField,
        MessageStyle $eStyle = MessageStyle::PLAIN,
        array $aGlobal = [],
        array $aOtherFields = []
    ): string {
        $oRuleSet = (new RuleSet())->add($oField);
        foreach ($aOtherFields as $oOther) {
            $oRuleSet->add($oOther);
        }
        $oState    = new State($oRuleSet, [], $eStyle, $this->oTranslation);
        $oResolved = Registry::discover()->resolve($sRule);

        return (new MessageResolver($this->oTranslation))->resolve($oResolved, $oField, $oState, $aGlobal);
    }

    public function test_per_field_messages_win(): void
    {
        $oField = new Field('a', '', ['required'], ['required' => 'Field says {field}']);
        self::assertSame('Field says a', $this->resolve('required', $oField, MessageStyle::PLAIN, ['required' => 'Global']));
    }

    public function test_global_messages_beat_language_lines(): void
    {
        $oField = new Field('a', '', ['required']);
        self::assertSame('Global', $this->resolve('required', $oField, MessageStyle::PLAIN, ['required' => 'Global']));
    }

    public function test_empty_or_false_overrides_fall_through(): void
    {
        $oField = new Field('a', '', ['required']);
        self::assertSame('This field is required.', $this->resolve('required', $oField, MessageStyle::PLAIN, ['required' => false]));
        self::assertSame('This field is required.', $this->resolve('required', $oField, MessageStyle::PLAIN, ['required' => '']));
    }

    public function test_style_selects_the_language_variant(): void
    {
        $oField = new Field('a', 'Name', ['required']);
        self::assertSame('This field is required.', $this->resolve('required', $oField, MessageStyle::PLAIN));
        self::assertSame('The Name field is required.', $this->resolve('required', $oField, MessageStyle::WITH_FIELD));
    }

    public function test_the_rule_default_is_used_when_no_language_line_exists(): void
    {
        $this->oTranslation->set('fv_required', '');
        $oField = new Field('a', '', ['strtoupper']);
        self::assertSame('The a field is invalid.', $this->resolve('strtoupper', $oField));
    }

    public function test_aliases_share_language_lines(): void
    {
        $oField = new Field('a', '', ['max_words[2]']);
        self::assertSame('This field is too long, maximum 2 words.', $this->resolve('max_words[2]', $oField));
    }

    public function test_placeholders_are_interpolated(): void
    {
        $oField = new Field('a', 'Bio', ['max_length[5]']);
        self::assertSame(
            'Bio: 5',
            $this->resolve('max_length[5]', $oField, MessageStyle::PLAIN, ['max_length' => '{field}: {param}'])
        );
    }

    public function test_legacy_sprintf_messages_receive_label_and_param(): void
    {
        $oField = new Field('a', 'Bio', ['max_length[5]']);
        self::assertSame(
            'Bio must be <= 5',
            $this->resolve('max_length[5]', $oField, MessageStyle::PLAIN, ['max_length' => '%s must be <= %s'])
        );
    }

    public function test_positional_sprintf_specifiers_are_not_interpolated(): void
    {
        //  Parity with CodeIgniter: only a literal "%s" triggers sprintf()
        $oField = new Field('a', 'Bio', ['max_length[5]']);
        self::assertSame(
            'Exceeds (%2$s)',
            $this->resolve('max_length[5]', $oField, MessageStyle::PLAIN, ['max_length' => 'Exceeds (%2$s)'])
        );
    }

    public function test_a_param_naming_another_field_is_replaced_by_its_label(): void
    {
        $oField = new Field('confirm', 'Confirmation', ['matches[password]']);
        $oOther = new Field('password', 'Password', ['required']);
        self::assertSame(
            'The Confirmation field does not match the Password field.',
            $this->resolve('matches[password]', $oField, MessageStyle::WITH_FIELD, [], [$oOther])
        );
    }

    public function test_lang_prefixed_labels_are_translated(): void
    {
        $this->oTranslation->set('my_label', 'Translated');
        $oField = new Field('a', 'lang:my_label', ['required']);
        self::assertSame('The Translated field is required.', $this->resolve('required', $oField, MessageStyle::WITH_FIELD));

        $oField = new Field('a', 'lang:missing_label', ['required']);
        self::assertSame('The missing_label field is required.', $this->resolve('required', $oField, MessageStyle::WITH_FIELD));
    }
}
