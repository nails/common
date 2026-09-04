<?php

namespace Tests\Common\Service;

use Nails\Common\Service\Translation;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase
{
    public function test_the_nails_language_file_is_loaded_without_codeigniter(): void
    {
        $oTranslation = new Translation('english');

        self::assertSame('This field is required.', $oTranslation->line('fv_required'));
        self::assertSame('Please check highlighted fields.', $oTranslation->line('fv_there_were_errors'));
    }

    public function test_a_missing_line_is_false(): void
    {
        self::assertFalse((new Translation('english'))->line('definitely_not_a_line'));
    }

    public function test_scalar_parameters_are_sprintfd(): void
    {
        $oTranslation = (new Translation('english'))->set('greeting', 'Hello %s');
        self::assertSame('Hello Ada', $oTranslation->line('greeting', 'Ada'));
    }

    public function test_array_parameters_are_vsprintfd(): void
    {
        $oTranslation = (new Translation('english'))->set('greeting', 'Hello %s and %s');
        self::assertSame('Hello Ada and Grace', $oTranslation->line('greeting', ['Ada', 'Grace']));
    }

    public function test_empty_parameters_leave_the_line_alone(): void
    {
        $oTranslation = (new Translation('english'))->set('greeting', 'Hello %s');
        self::assertSame('Hello %s', $oTranslation->line('greeting'));
        self::assertSame('Hello %s', $oTranslation->line('greeting', []));
    }

    public function test_too_few_parameters_do_not_throw(): void
    {
        $oTranslation = (new Translation('english'))->set('greeting', 'Hello %s and %s');
        self::assertSame('Hello %s and %s', $oTranslation->line('greeting', ['Ada']));
    }

    public function test_an_unknown_idiom_falls_back_to_english(): void
    {
        $oTranslation = new Translation('klingon');
        self::assertSame('klingon', $oTranslation->getIdiom());
        self::assertSame('This field is required.', $oTranslation->line('fv_required'));
    }

    public function test_the_default_idiom_is_english(): void
    {
        self::assertSame('english', Translation::detectIdiom());
    }

    public function test_every_bundled_rule_has_both_language_lines(): void
    {
        $oTranslation = new Translation('english');
        $aMissing     = [];

        foreach (\Nails\Common\Validation\Registry::discover()->names() as $sName) {
            if (in_array($sName, ['trim', 'prep_for_form', 'prep_url', 'strip_image_tags', 'encode_php_tags'], true)) {
                continue; // mutators never fail
            }
            foreach (['fv_' . $sName, 'fv_' . $sName . '_field'] as $sKey) {
                if (!$oTranslation->has($sKey)) {
                    $aMissing[] = $sKey;
                }
            }
        }

        self::assertSame([], $aMissing);
    }
    public function test_load_tolerates_hmvc_and_suffixed_names(): void
    {
        $oTranslation = new Translation('english');
        self::assertSame($oTranslation, $oTranslation->load('admin/nails'));
        self::assertSame($oTranslation, $oTranslation->load('nails_lang'));
        self::assertSame('This field is required.', $oTranslation->line('fv_required'));
    }

    public function test_changing_the_idiom_reloads_lines(): void
    {
        $oTranslation = new Translation('english');
        $oTranslation->set('runtime_only', 'x');

        $oTranslation->setIdiom('klingon');

        self::assertSame('klingon', $oTranslation->getIdiom());
        self::assertSame('This field is required.', $oTranslation->line('fv_required'));
        self::assertFalse($oTranslation->line('runtime_only'));
    }

    public function test_the_lang_helper_uses_the_translation_service(): void
    {
        /** @var Translation $oTranslation */
        $oTranslation = \Nails\Factory::service('Translation');
        $oTranslation->set('helper_test', 'Hello %s');

        self::assertSame('Hello Ada', lang('helper_test', 'Ada'));
        self::assertSame('<label for="f">Hello Ada</label>', lang('helper_test', ['Ada'], 'f'));
        self::assertSame('This field is required.', lang('fv_required'));
        self::assertFalse(lang('no_such_line'));
    }
}
