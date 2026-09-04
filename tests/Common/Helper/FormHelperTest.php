<?php

namespace Tests\Common\Helper;

use Nails\Common\Service\FormValidation;
use Nails\Factory;
use PHPUnit\Framework\TestCase;

/**
 * Covers the shadowed CodeIgniter form-validation helpers when no validation has run
 */
class FormHelperTest extends TestCase
{
    private array $aPostBackup;

    protected function setUp(): void
    {
        $this->aPostBackup = $_POST;
        /** @var FormValidation $oFormValidation */
        $oFormValidation = Factory::service('FormValidation');
        $oFormValidation->reset_validation();
    }

    protected function tearDown(): void
    {
        $_POST = $this->aPostBackup;
    }

    public function test_set_value_falls_back_to_post(): void
    {
        $_POST = ['name' => '<Ada>', 'q' => [['a' => 'deep']], 'blank' => ''];

        self::assertSame('&lt;Ada&gt;', set_value('name'));
        self::assertSame('<Ada>', set_value('name', '', false));
        self::assertSame('deep', set_value('q[0][a]'));
        self::assertSame('', set_value('blank', 'dflt'));
        self::assertSame('dflt', set_value('missing', 'dflt'));
    }

    public function test_set_select_radio_checkbox_fall_back_to_post(): void
    {
        $_POST = ['colour' => 'red', 'sizes' => ['s', 'l']];

        self::assertSame(' selected="selected"', set_select('colour', 'red'));
        self::assertSame('', set_select('colour', 'blue'));
        self::assertSame(' checked="checked"', set_radio('sizes', 'l'));
        self::assertSame('', set_checkbox('sizes', 'm'));
        self::assertSame(' checked="checked"', set_checkbox('missing', 'm', true));
        self::assertSame('', set_checkbox('missing', 'm'));
    }

    public function test_form_error_is_empty_without_a_run(): void
    {
        self::assertSame('', form_error('anything'));
        self::assertSame('', validation_errors());
    }
}
