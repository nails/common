<?php

namespace Nails\Common\Console\Seed;

use Nails\Common\Exception\NailsException;
use Nails\Common\Service\Locale;
use Nails\Common\Traits\Model\Localised;
use Nails\Factory;

class DefaultSeed extends Base
{
    /**
     * The number of items to create
     */
    const CONFIG_NUM_PER_SEED = 20;

    /**
     * The model to bind this seeder to
     */
    const CONFIG_MODEL_NAME     = '';
    const CONFIG_MODEL_PROVIDER = '';

    /**
     * Fields to explicitly ignore when generating
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
     * Execute the seed
     *
     * @return void
     */
    public function execute()
    {
        $oModel = Factory::model(static::CONFIG_MODEL_NAME, static::CONFIG_MODEL_PROVIDER);

        /** @var Locale $oLocale */
        $oLocale        = Factory::service('Locale');
        $oDefaultLocale = $oLocale->getDefautLocale();

        $aFieldsDescribed = $oModel->describeFields();
        $aFields          = [];

        foreach ($aFieldsDescribed as $oField) {
            if (!in_array($oField->key, static::CONFIG_IGNORE_FIELDS)) {
                $aFields[] = $oField;
            }
        }

        for ($i = 0; $i < static::CONFIG_NUM_PER_SEED; $i++) {
            try {
                $aData = $this->generate($aFields);
                if (classUses($oModel, Localised::class)) {
                    if (!$oModel->create($aData, false, $oDefaultLocale)) {
                        throw new NailsException('Failed to create item. ' . $oModel->lastError());
                    }
                } elseif (!$oModel->create($aData)) {
                    throw new NailsException('Failed to create item. ' . $oModel->lastError());
                }
            } catch (\Exception $e) {
                echo "\nSEED ERROR: " . $e->getMessage();
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Generate a new item
     *
     * @param array $aFields The fields to generate
     *
     * @return array
     */
    protected function generate($aFields)
    {
        $aOut = [];
        foreach ($aFields as $oField) {
            switch ($oField->type) {
                case 'textarea':
                    $mValue = $this->loremParagraph();
                    break;
                case 'wysiwyg':
                    $mValue = $this->loremHtml();
                    break;
                case 'number':
                    $mValue = $this->randomInteger();
                    break;
                case 'boolean':
                    $mValue = $this->randomBool();
                    break;
                case 'datetime':
                    $mValue = $this->randomDateTime();
                    break;
                case 'date':
                    $mValue = $this->randomDateTime(null, null, 'Y-m-d');
                    break;
                case 'time':
                    $mValue = $this->randomDateTime(null, null, 'H:i:s');
                    break;
                case 'dropdown':
                    $mValue = $this->randomItem(array_keys($oField->options));
                    break;
                default:
                    $mValue = $this->loremWord(3);
                    break;
            }

            //  Ensure the value isn't too long
            if (!empty($oField->max_length) && strlen($mValue) > $oField->max_length) {
                $mValue = substr($mValue, 0, $oField->max_length);
            }

            $aOut[$oField->key] = $mValue;
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
