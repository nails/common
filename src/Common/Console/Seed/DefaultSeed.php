<?php

namespace Nails\Common\Console\Seed;

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
     * @return void
     */
    public function execute()
    {
        $oModel  = Factory::model(static::CONFIG_MODEL_NAME, static::CONFIG_MODEL_PROVIDER);
        $sTable  = $oModel->getTableName();
        $oFields = $this->oDb->query('DESCRIBE `' . $sTable . '`;');
        $aFields = [];
        while ($oField = $oFields->fetchObject()) {

            if (!in_array($oField->Field, static::CONFIG_IGNORE_FIELDS)) {

                preg_match(
                    '/([a-zA-Z\s]*)(\((.*)\)(.*))?$/',
                    $oField->Type,
                    $aMatches
                );

                $sType  = trim(getFromArray(1, $aMatches));
                $iSize  = (int) getFromArray(3, $aMatches) ?: null;
                $sExtra = trim(getFromArray(4, $aMatches));

                $aFields[] = (object) [
                    'name'  => $oField->Field,
                    'type'  => $sType,
                    'size'  => $iSize,
                    'extra' => $sExtra,
                ];
            }
        }

        for ($i = 0; $i < self::CONFIG_NUM_PER_SEED; $i++) {
            try {
                //  Generate the item
                $aItem = $this->generate($aFields);

                //  Generate the SQL query
                $sSql = 'INSERT INTO `' . $sTable . '` (';
                foreach ($aFields as $oField) {
                    $sSql .= '`' . $oField->name . '`,';
                }
                $sSql .= '`created`,`modified`) VALUES (';
                foreach ($aFields as $oField) {
                    $sSql .= ':' . $oField->name . ',';
                }
                $sSql .= 'NOW(),NOW());';

                $oStatement = $this->oDb->prepare($sSql);
                $oStatement->execute($aItem);
            } catch (\Exception $e) {
                echo "\nSEED ERROR: " . $e->getMessage();
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Generate a new Article
     * @return array
     */
    protected function generate($aFields)
    {
        $aOut = [];
        foreach ($aFields as $oField) {
            switch ($oField->type) {
                case 'text' :
                    $mValue = $this->loremParagraph();
                    break;
                case 'int' :
                    $mValue = 123;
                    break;
                case 'tinyint' :
                    $mValue = 1;
                    break;
                case 'datetime':
                    $mValue = 'NOW()';
                    break;
                default:
                    $mValue = $this->loremWord(3);
                    break;
            }
            $aOut[$oField->name] = $mValue;
        }

        //  Special case, slugs
        if (array_key_exists('slug', $aOut)) {
            $aOut['slug'] = str_replace(' ', '-', strtolower($aOut['slug']));
        }

        return $aOut;
    }
}
