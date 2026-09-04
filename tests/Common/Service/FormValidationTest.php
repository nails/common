<?php

namespace Tests\Common\Service;

use BadMethodCallException;
use Nails\Common\Service\FormValidation;
use Nails\Common\Validation\Rule\Required;
use Nails\Factory;
use PHPUnit\Framework\TestCase;

/**
 * Covers the (deprecated) CodeIgniter-compatible surface of the service
 */
class FormValidationTest extends TestCase
{
    private FormValidation $oFormValidation;
    private array          $aPostBackup;
    private array          $aServerBackup;

    protected function setUp(): void
    {
        $this->aPostBackup   = $_POST;
        $this->aServerBackup = $_SERVER;

        /** @var FormValidation $oFormValidation */
        $oFormValidation       = Factory::service('FormValidation');
        $this->oFormValidation = $oFormValidation->reset_validation();
    }

    protected function tearDown(): void
    {
        $_POST   = $this->aPostBackup;
        $_SERVER = $this->aServerBackup;
        $this->oFormValidation->reset_validation();
    }

    // --------------------------------------------------------------------------

    public function test_the_service_constructs_without_codeigniter(): void
    {
        self::assertFalse(function_exists('get_instance'));
        self::assertInstanceOf(FormValidation::class, new FormValidation());
    }

    public function test_rule_constants_match_the_rule_names(): void
    {
        self::assertSame('required', FormValidation::RULE_REQUIRED);
        self::assertSame(Required::NAME, FormValidation::RULE_REQUIRED);
        self::assertSame('maxWords', FormValidation::RULE_MAX_WORDS);
        self::assertSame('supportedLocale', FormValidation::RULE_SUPPORTED_LOCALE);
    }

    public function test_rule_compiles_parameters(): void
    {
        self::assertSame('required', FormValidation::rule('required'));
        self::assertSame('is_unique[table.col.1]', FormValidation::rule('is_unique', 'table', 'col', 1));
    }

    public function test_split_rules_ignores_pipes_inside_parameters(): void
    {
        self::assertSame(['required', 'regex_match[/a|b/]', 'trim'], FormValidation::splitRules('required|regex_match[/a|b/]|trim'));
        self::assertSame(['required'], FormValidation::splitRules(['required', null, '']));
        self::assertSame([], FormValidation::splitRules(null));
    }

    // --------------------------------------------------------------------------

    public function test_legacy_run_validates_set_data(): void
    {
        $this->oFormValidation
            ->set_data(['email' => 'nope', 'name' => 'Ada'])
            ->set_rules('email', 'Email', 'required|valid_email')
            ->set_rules('name', '', 'required');

        self::assertFalse($this->oFormValidation->run());
        self::assertSame(['email' => 'The Email field must be a valid email.'], $this->oFormValidation->error_array());
        self::assertSame(['email' => 'The Email field must be a valid email.'], $this->oFormValidation->errors());
        self::assertSame('<p>The Email field must be a valid email.</p>', $this->oFormValidation->error('email'));
        self::assertSame('<b>The Email field must be a valid email.</b>', $this->oFormValidation->error('email', '<b>', '</b>'));
        self::assertSame('', $this->oFormValidation->error('name'));
        self::assertSame("<p>The Email field must be a valid email.</p>\n", $this->oFormValidation->error_string());
    }

    public function test_legacy_run_returns_false_without_rules(): void
    {
        self::assertFalse($this->oFormValidation->set_data(['a' => 'b'])->run());
    }

    public function test_empty_rules_are_a_no_op(): void
    {
        $this->oFormValidation->set_data(['title' => 'x'])->set_rules('title', '', '');

        self::assertFalse($this->oFormValidation->has_rule('title'));
        self::assertFalse($this->oFormValidation->run());
    }

    public function test_rules_are_ignored_without_post_or_data(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->oFormValidation->set_rules('title', '', 'required');

        self::assertFalse($this->oFormValidation->has_rule('title'));
    }

    public function test_legacy_run_writes_processed_values_back_to_post(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST                     = ['title' => '  hello  ', 'other' => ' untouched '];

        $this->oFormValidation->set_rules('title', '', 'trim|required');

        self::assertTrue($this->oFormValidation->run());
        self::assertSame(['title' => 'hello', 'other' => ' untouched '], $_POST);
    }

    public function test_legacy_run_fills_the_data_argument(): void
    {
        $this->oFormValidation
            ->set_data(['title' => '  hello  '])
            ->set_rules('title', '', 'trim|required');

        $aData = null;
        self::assertTrue($this->oFormValidation->run(null, $aData));
        self::assertSame(['title' => 'hello'], $aData);
    }

    public function test_set_rules_accepts_an_array_of_rows(): void
    {
        $this->oFormValidation
            ->set_data(['a' => '', 'b' => 'x'])
            ->set_rules([
                ['field' => 'a', 'label' => 'Alpha', 'rules' => 'required'],
                ['field' => 'b', 'rules' => 'required', 'errors' => ['required' => 'never shown']],
                ['field' => 'c'],
            ]);

        self::assertFalse($this->oFormValidation->run());
        self::assertSame(['a' => 'The Alpha field is required.'], $this->oFormValidation->error_array());
        self::assertSame(['a' => ['required'], 'b' => ['required']], $this->oFormValidation->getRules());
    }

    public function test_set_message_overrides_globally(): void
    {
        $this->oFormValidation
            ->set_data(['a' => ''])
            ->set_rules('a', '', 'required')
            ->set_message('required', 'Custom {field}');

        self::assertFalse($this->oFormValidation->run());
        self::assertSame('Custom a', $this->oFormValidation->error_array()['a']);
    }

    public function test_callback_rules_target_the_object_passed_to_run(): void
    {
        $oController = new class {
            public function _callbackValid($sValue)
            {
                return $sValue === 'GBP';
            }
        };

        $this->oFormValidation
            ->set_data(['currency' => 'USD'])
            ->set_rules('currency', '', 'required|callback__callbackValid')
            ->set_message('_callbackValid', 'Invalid currency.');

        self::assertFalse($this->oFormValidation->run($oController));
        self::assertSame('Invalid currency.', $this->oFormValidation->error_array()['currency']);
    }

    public function test_set_value_reads_the_last_run(): void
    {
        $this->oFormValidation
            ->set_data(['title' => '  hello  ', 'tags' => ['a', 'b'], 'none' => null])
            ->set_rules('title', '', 'trim')
            ->set_rules('tags[]', '', 'trim')
            ->set_rules('none', '', 'trim');

        self::assertTrue($this->oFormValidation->run());
        self::assertSame('hello', $this->oFormValidation->set_value('title'));
        self::assertSame('a', $this->oFormValidation->set_value('tags[]'));
        self::assertSame('b', $this->oFormValidation->set_value('tags[]'));
        self::assertNull($this->oFormValidation->set_value('tags[]'));
        self::assertSame('dflt', $this->oFormValidation->set_value('none', 'dflt'));
        self::assertSame('dflt', $this->oFormValidation->set_value('undeclared', 'dflt'));
        self::assertTrue($this->oFormValidation->has_rule('title'));
        self::assertFalse($this->oFormValidation->has_rule('undeclared'));
    }

    public function test_set_select_radio_and_checkbox(): void
    {
        $this->oFormValidation
            ->set_data(['colour' => 'red', 'sizes' => ['s', 'l']])
            ->set_rules('colour', '', 'required')
            ->set_rules('sizes[]', '', 'required');

        self::assertTrue($this->oFormValidation->run());
        self::assertSame(' selected="selected"', $this->oFormValidation->set_select('colour', 'red'));
        self::assertSame('', $this->oFormValidation->set_select('colour', 'blue'));
        self::assertSame(' checked="checked"', $this->oFormValidation->set_radio('sizes[]', 'l'));
        self::assertSame('', $this->oFormValidation->set_checkbox('sizes[]', 'm'));
        self::assertSame('', $this->oFormValidation->set_checkbox('undeclared', 'm', true));
    }

    public function test_reset_validation_clears_everything(): void
    {
        $this->oFormValidation
            ->set_data(['a' => ''])
            ->set_rules('a', '', 'required')
            ->set_message('required', 'x');

        self::assertFalse($this->oFormValidation->run());
        $this->oFormValidation->reset_validation();

        self::assertSame([], $this->oFormValidation->error_array());
        self::assertSame([], $this->oFormValidation->getRules());
        self::assertSame([], $this->oFormValidation->getMessages());
        self::assertFalse($this->oFormValidation->has_rule('a'));
        self::assertNull($this->oFormValidation->getLastResult());
    }

    public function test_validation_data_is_readable_and_writable(): void
    {
        $this->oFormValidation->set_data(['a' => 'b']);
        self::assertSame(['a' => 'b'], $this->oFormValidation->validation_data);

        $this->oFormValidation->validation_data = ['c' => 'd'];
        self::assertSame(['c' => 'd'], $this->oFormValidation->validation_data);
    }

    public function test_rules_can_be_invoked_directly(): void
    {
        self::assertTrue($this->oFormValidation->valid_email('a@b.com'));
        self::assertFalse($this->oFormValidation->valid_email('nope'));
        self::assertTrue($this->oFormValidation->max_length('abc', 3));
        self::assertFalse($this->oFormValidation->max_length('abcd', 3));
        self::assertTrue($this->oFormValidation->is_bool('1'));

        $this->expectException(BadMethodCallException::class);
        $this->oFormValidation->not_a_rule('x');
    }
}
