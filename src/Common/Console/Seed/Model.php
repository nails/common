<?php

namespace Nails\Common\Console\Seed;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
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
     * @param array $aFields The fields to generate
     *
     * @return array
     * @throws FactoryException
     */
    protected function generate(array $aFields): array
    {
        $aOut = [];
        foreach ($aFields as $oField) {
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

                case 'wysiwyg':
                    $mValue = $this->loremHtml();
                    break;

                default:
                    $mValue = $this->loremWord(3);
                    break;
            }

            //  Ensure the value isn't too long
            if (!empty($oField->max_length) && strlen($mValue) > $oField->max_length) {
                $mValue = substr($mValue, 0, $oField->max_length);
            }

            $sKey        = preg_replace('/\[\]$/', '', $oField->key);
            $aOut[$sKey] = $mValue;
        }

        //  Special Cases, model dependant
        $oModel = Factory::model(static::CONFIG_MODEL_NAME, static::CONFIG_MODEL_PROVIDER);

        //  Slugs
        //  If these are being automatically generated then let the model do the hard work
        if ($oModel->isAutoSetSlugs()) {
            $sColumn = $oModel->getColumn('slug');
            unset($aOut[$sColumn]);
        }

        //  Tokens
        //  If these are being automatically generated then let the model do the hard work
        if ($oModel->isAutoSetTokens()) {
            $sColumn = $oModel->getColumn('token');
            unset($aOut[$sColumn]);
        }

        return $aOut;
    }
}
