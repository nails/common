<?php

namespace Tests\Common\Factory\Service\FormValidation;

use Nails\Common\Exception\ValidationException;
use Nails\Common\Factory\Service\FormValidation\Validator;
use Nails\Common\Service\FormValidation;
use Nails\Common\Validation\Context;
use Nails\Common\Validation\Rule\Required;
use Nails\Factory;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private FormValidation $oFormValidation;

    protected function setUp(): void
    {
        /** @var FormValidation $oFormValidation */
        $oFormValidation       = Factory::service('FormValidation');
        $this->oFormValidation = $oFormValidation->reset_validation();
    }

    protected function tearDown(): void
    {
        $this->oFormValidation->reset_validation();
    }

    // --------------------------------------------------------------------------

    public function test_a_failing_run_throws_with_the_errors_as_data(): void
    {
        $oValidator = $this->oFormValidation->buildValidator(
            ['email' => 'required|valid_email', 'name' => [FormValidation::RULE_REQUIRED]],
            [],
            ['email' => 'nope', 'name' => '']
        );

        try {
            $oValidator->run();
            self::fail('Expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertSame('Please check highlighted fields.', $e->getMessage());
            self::assertSame(
                ['email' => 'This must be a valid email.', 'name' => 'This field is required.'],
                $e->getData()
            );
            self::assertSame($e->getData(), $oValidator->getErrors());
            self::assertTrue($oValidator->getResult()->failed());
        }
    }

    public function test_a_passing_run_returns_the_validator(): void
    {
        $oValidator = $this->oFormValidation->buildValidator(['email' => 'required|valid_email'], [], ['email' => 'a@b.com']);

        self::assertSame($oValidator, $oValidator->run());
        self::assertSame([], $oValidator->getErrors());
    }

    public function test_validated_data_carries_mutations(): void
    {
        $oValidator = $this->oFormValidation->buildValidator(['name' => 'trim|required'], [], ['name' => '  Ada  ', 'x' => ' y ']);
        $oValidator->run();

        self::assertSame(['name' => '  Ada  ', 'x' => ' y '], $oValidator->getData());
        self::assertSame(['name' => 'Ada', 'x' => ' y '], $oValidator->getValidatedData());
    }

    public function test_data_can_be_passed_to_run(): void
    {
        $oValidator = $this->oFormValidation->buildValidator(['name' => 'required']);

        $this->expectException(ValidationException::class);
        $oValidator->run(['name' => '']);
    }

    public function test_no_rules_means_nothing_happens(): void
    {
        $oValidator = $this->oFormValidation->buildValidator([], [], ['anything' => 'goes']);
        self::assertSame($oValidator, $oValidator->run());
        self::assertNull($oValidator->getResult());
    }

    public function test_messages_override_per_rule(): void
    {
        $oValidator = $this->oFormValidation->buildValidator(
            ['a' => 'required'],
            [FormValidation::RULE_REQUIRED => 'Custom'],
            ['a' => '']
        );

        try {
            $oValidator->run();
        } catch (ValidationException $e) {
            self::assertSame(['a' => 'Custom'], $e->getData());
        }
    }

    public function test_field_messages_and_labels(): void
    {
        $oValidator = $this->oFormValidation
            ->buildValidator(['a' => 'required', 'b' => 'required'], [], ['a' => '', 'b' => ''])
            ->setLabels(['b' => 'Bravo'])
            ->setFieldMessages(['a' => ['required' => 'Just a'], 'b' => ['required' => 'Field {field}']]);

        try {
            $oValidator->run();
        } catch (ValidationException $e) {
            self::assertSame(['a' => 'Just a', 'b' => 'Field Bravo'], $e->getData());
        }
    }

    public function test_rules_may_be_instances_class_names_and_closures(): void
    {
        $oValidator = $this->oFormValidation->buildValidator(
            [
                'a' => [new Required()],
                'b' => [Required::class],
                'c' => [function ($mValue, Context $oContext) {
                    throw new ValidationException('c failed with ' . $oContext->getValue('a'));
                }],
            ],
            [],
            ['a' => 'A', 'b' => '', 'c' => 'C']
        );

        try {
            $oValidator->run();
        } catch (ValidationException $e) {
            self::assertSame(['b' => 'This field is required.', 'c' => 'c failed with A'], $e->getData());
        }
    }

    public function test_the_last_run_is_published_to_the_view_helpers(): void
    {
        $oValidator = $this->oFormValidation->buildValidator(['name' => 'trim|required', 'email' => 'valid_email'], [], ['name' => ' Ada ', 'email' => 'nope']);

        try {
            $oValidator->run();
        } catch (ValidationException) {
        }

        self::assertSame('Ada', set_value('name'));
        self::assertSame('<p>This must be a valid email.</p>', form_error('email'));
        self::assertSame('', form_error('name'));
        self::assertSame("<p>This must be a valid email.</p>\n", validation_errors());
    }

    public function test_each_validator_keeps_its_own_errors(): void
    {
        $oFirst  = $this->oFormValidation->buildValidator(['a' => 'required'], [], ['a' => '']);
        $oSecond = $this->oFormValidation->buildValidator(['b' => 'required'], [], ['b' => '']);

        try {
            $oFirst->run();
        } catch (ValidationException) {
        }
        try {
            $oSecond->run();
        } catch (ValidationException) {
        }

        self::assertSame(['a'], array_keys($oFirst->getErrors()));
        self::assertSame(['b'], array_keys($oSecond->getErrors()));
    }

    public function test_the_factory_builds_a_validator(): void
    {
        self::assertInstanceOf(Validator::class, Factory::factory('FormValidationValidator'));
    }

    // --------------------------------------------------------------------------

    private function identityValidator(): Validator
    {
        return new class extends Validator {
            protected function rules(): array
            {
                return [
                    'email' => [FormValidation::RULE_REQUIRED, FormValidation::RULE_VALID_EMAIL],
                    'name'  => [FormValidation::RULE_REQUIRED],
                ];
            }

            protected function messages(): array
            {
                return [FormValidation::RULE_VALID_EMAIL => 'Not an email.'];
            }

            protected function labels(): array
            {
                return ['name' => 'Full name'];
            }

            protected function fieldMessages(): array
            {
                return ['name' => [FormValidation::RULE_REQUIRED => 'We need your {field}.']];
            }
        };
    }

    public function test_a_subclass_defines_its_own_rules_messages_and_labels(): void
    {
        $oValidator = $this->identityValidator();

        self::assertArrayHasKey('email', $oValidator->getRules());

        try {
            $oValidator->run(['email' => 'nope', 'name' => '']);
            self::fail('Expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertSame(
                ['email' => 'Not an email.', 'name' => 'We need your Full name.'],
                $e->getData()
            );
        }
    }

    public function test_runtime_values_merge_over_a_subclass(): void
    {
        $oValidator = $this->identityValidator()
            ->setRules(['email' => [FormValidation::RULE_REQUIRED]])   // replaces the email rules only
            ->addRules(['age' => [FormValidation::RULE_INTEGER]])       // adds a field
            ->setMessages([FormValidation::RULE_INTEGER => 'Whole numbers only.']);

        self::assertSame(['email', 'name', 'age'], array_keys($oValidator->getRules()));
        self::assertSame(
            [FormValidation::RULE_VALID_EMAIL => 'Not an email.', FormValidation::RULE_INTEGER => 'Whole numbers only.'],
            $oValidator->getMessages()
        );

        try {
            $oValidator->run(['email' => 'not-an-email', 'name' => 'Ada', 'age' => 'x']);
            self::fail('Expected a ValidationException');
        } catch (ValidationException $e) {
            //  valid_email no longer applies; integer does
            self::assertSame(['age' => 'Whole numbers only.'], $e->getData());
        }
    }

    public function test_a_rule_can_be_stubbed_with_a_closure_without_touching_the_shared_registry(): void
    {
        $aSeen      = [];
        $oValidator = $this->oFormValidation
            ->buildValidator(['email' => [FormValidation::RULE_VALID_EMAIL]], [], ['email' => 'a@b.com'])
            ->stubRule(FormValidation::RULE_VALID_EMAIL, function ($mValue, Context $oContext) use (&$aSeen) {
                $aSeen[] = [$mValue, $oContext->getField()];
                return false;
            });

        try {
            $oValidator->run();
            self::fail('Expected the stub to fail the field');
        } catch (ValidationException $e) {
            self::assertSame(['email' => 'This must be a valid email.'], $e->getData(), 'stub keeps the original message');
            self::assertSame([['a@b.com', 'email']], $aSeen);
        }

        //  A fresh validator still uses the real rule
        $this->oFormValidation
            ->buildValidator(['email' => [FormValidation::RULE_VALID_EMAIL]], [], ['email' => 'a@b.com'])
            ->run();

        //  ... and stubbing can be reverted
        $oValidator->setEngine(null)->run();
    }

    public function test_a_stubbed_closure_may_throw_its_own_message(): void
    {
        $oValidator = $this->oFormValidation
            ->buildValidator(['email' => 'is_unique[x.y]'], [], ['email' => 'a@b.com'])
            ->stubRule(FormValidation::RULE_IS_UNIQUE, fn() => throw new ValidationException('Taken.'));

        try {
            $oValidator->run();
            self::fail('Expected a ValidationException');
        } catch (ValidationException $e) {
            self::assertSame(['email' => 'Taken.'], $e->getData());
        }
    }

    public function test_a_rule_can_be_stubbed_with_an_instance(): void
    {
        $oValidator = $this->oFormValidation
            ->buildValidator(['name' => [FormValidation::RULE_REQUIRED]], [], ['name' => ''])
            ->stubRule(FormValidation::RULE_REQUIRED, new class extends Required {
                public function apply(mixed $mValue, Context $oContext): bool
                {
                    return true;
                }
            });

        self::assertSame($oValidator, $oValidator->run());
    }
}
