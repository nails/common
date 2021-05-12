<?php

namespace Nails\Common\Console\Seed;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Factory\Model\Field;
use Nails\Common\Helper\Form;
use Nails\Common\Service\Locale;
use Nails\Common\Traits;
use Nails\Common\Interfaces;
use Nails\Factory;

/**
 * Class Model
 *
 * @package Nails\Common\Console\Seed
 */
abstract class Model implements Interfaces\Database\Seeder
{
    use Traits\Database\Seeder;

    // --------------------------------------------------------------------------

    /**
     * The model to bind this seeder to
     *
     * @var string
     */
    const CONFIG_MODEL_NAME     = '';
    const CONFIG_MODEL_PROVIDER = 'app';

    /**
     * The number of items to create
     *
     * @var int
     */
    const CONFIG_NUM_PER_SEED = 20;

    /**
     * Defines the priority of the seeder, useful for ordering
     *
     * @var int
     */
    const CONFIG_PRIORITY = 100;

    /**
     * Fields to explicitly ignore when generating
     *
     * @var array
     */
    const CONFIG_IGNORE_FIELDS = [
        'id',
        'is_deleted',
        'created',
        'created_by',
        'modified',
        'modified_by',
    ];

    // --------------------------------------------------------------------------

    /**
     * Returns the seeders priority
     *
     * @return int
     */
    public static function getPriority(): int
    {
        return static::CONFIG_PRIORITY;
    }

    // --------------------------------------------------------------------------

    /**
     * Execute the seed
     *
     * @return void
     * @throws FactoryException
     */
    public function execute(): self
    {
        /** @var Locale $oLocale */
        $oLocale = Factory::service('Locale');
        $oModel  = Factory::model(static::CONFIG_MODEL_NAME, static::CONFIG_MODEL_PROVIDER);

        $oDefaultLocale   = $oLocale->getDefautLocale();
        $aFieldsDescribed = $oModel->describeFields();
        $aFields          = [];

        foreach ($aFieldsDescribed as $oField) {
            if (!in_array($oField->key, static::CONFIG_IGNORE_FIELDS)) {
                $aFields[] = $oField;
            }
        }

        for ($i = 0; $i < static::CONFIG_NUM_PER_SEED; $i++) {

            $aData = $this->generate($aFields);

            if (classUses($oModel, Traits\Model\Localised::class)) {
                if (!$oModel->create($aData, false, $oDefaultLocale)) {
                    throw new NailsException('Failed to create item. ' . $oModel->lastError());
                }

            } elseif (!$oModel->create($aData)) {
                throw new NailsException('Failed to create item. ' . $oModel->lastError());
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Generate a new item
     *
     * @param Field[] $aFields The fields to generate
     *
     * @return mixed[]
     * @throws FactoryException
     */
    protected function generate(array $aFields): array
    {
        $aOut = [];
        foreach ($aFields as $oField) {
            $sKey        = preg_replace('/\[\]$/', '', $oField->key);
            $aOut[$sKey] = $this->generateValue($oField);
        }

        //  Special Cases, model dependant
        $oModel = Factory::model(static::CONFIG_MODEL_NAME, static::CONFIG_MODEL_PROVIDER);

        $this
            ->generateLabel($oModel, $aOut)
            ->generateSlugs($oModel, $aOut)
            ->generateTokens($oModel, $aOut)
            ->generatePublishable($oModel, $aOut);

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a value appropriate for the field
     *
     * @param Field $oField The field to generate a value for
     *
     * @return mixed
     */
    protected function generateValue(Field $oField)
    {
        switch ($oField->type) {
            case Form::FIELD_TEXTAREA:
                $mValue = $this->loremParagraph();
                break;

            case Form::FIELD_NUMBER:
                $mValue = $this->randomInteger();
                break;

            case Form::FIELD_BOOLEAN:
                $mValue = $this->randomBool();
                break;

            case Form::FIELD_DATETIME:
                $mValue = $this->randomDateTime();
                break;

            case Form::FIELD_DATE:
                $mValue = $this->randomDateTime(null, null, 'Y-m-d');
                break;

            case Form::FIELD_TIME:
                $mValue = $this->randomDateTime(null, null, 'H:i:s');
                break;

            case Form::FIELD_DROPDOWN:
                $mValue = $this->randomItem(array_keys($oField->options));
                break;

            case Form::FIELD_DROPDOWN_MULTIPLE:
                $mValue = $this->randomItems(array_keys($oField->options));
                break;

            case Form::FIELD_URL:
                $mValue = $this->url();
                break;

            case 'wysiwyg':
                $mValue = $this->loremHtml();
                break;

            default:
                $mValue = $this->loremWord(rand(3, 6));
                break;
        }

        //  Ensure the value isn't too long
        if (!empty($oField->max_length) && strlen($mValue) > $oField->max_length) {
            $mValue = substr($mValue, 0, $oField->max_length);
        }

        return $mValue;
    }

    // --------------------------------------------------------------------------

    /**
     * If the model ahs a label column, then title case the label
     *
     * @param \Nails\Common\Model\Base $oModel The model being seeded
     * @param mixed[]                  $aOut   The output array
     *
     * @return $this
     */
    protected function generateLabel(\Nails\Common\Model\Base $oModel, &$aOut): self
    {
        $sColumn = $oModel->getColumnLabel();
        if ($sColumn) {
            $aOut[$sColumn] = title_case($aOut[$sColumn]);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * If the model supports slugs, and slugs are automatic, unset and allow the
     * model to set the slug on creation.
     *
     * @param \Nails\Common\Model\Base $oModel The model being seeded
     * @param mixed[]                  $aOut   The output array
     *
     * @return $this
     */
    protected function generateSlugs(\Nails\Common\Model\Base $oModel, &$aOut): self
    {
        if (classImplements($oModel, Traits\Model\Slug::class) && $oModel->isAutoSetSlugs()) {
            $sColumn = $oModel->getColumnSlug();
            unset($aOut[$sColumn]);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * If the model supports tokens, and tokens are automatic, unset and allow the
     * model to set the token on creation.
     *
     * @param \Nails\Common\Model\Base $oModel The model being seeded
     * @param mixed[]                  $aOut   The output array
     *
     * @return $this
     */
    protected function generateTokens(\Nails\Common\Model\Base $oModel, &$aOut): self
    {
        if (classImplements($oModel, Traits\Model\Token::class) && $oModel->isAutoSetTokens()) {
            $sColumn = $oModel->getColumnToken();
            unset($aOut[$sColumn]);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * If the model supports expiration dates, then ensure the expiration date is
     * after the published date.
     *
     * @param \Nails\Common\Model\Base $oModel The model being seeded
     * @param mixed[]                  $aOut   The output array
     *
     * @return $this
     */
    protected function generatePublishable(\Nails\Common\Model\Base $oModel, &$aOut): self
    {
        if (classImplements($oModel, Traits\Model\Publishable::class)) {
            //  Ensure the expiration is after the publish date
            $sColumnPublished = $oModel->getColumnDateExpire();
            $sColumnExpire    = $oModel->getColumnDateExpire();
            if ($sColumnExpire && $sColumnPublished) {
                $aOut[$sColumnExpire] = $this->randomDateTime($aOut[$sColumnPublished]);
            }
        }
    }
}
