<?php

namespace Nails\Common\Traits\Database\Seeder;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Factory;

/**
 * Trait Model
 *
 * @package Nails\Common\Traits\Database\Seeder
 */
trait Model
{
    /**
     * Returns a random ID from a particular model
     *
     * @param string $sModel    The model to use
     * @param string $sProvider The model's provider
     * @param array  $aData     Any data to pass to the model
     *
     * @return int|null
     * @throws FactoryException
     * @throws ModelException
     */
    protected function randomId(string $sModel, string $sProvider, array $aData = []): ?int
    {
        $oModel   = Factory::model($sModel, $sProvider);
        $aResults = $oModel->getRandom($aData);
        $oRow     = reset($aResults);

        return $oRow ? $oRow->id : null;
    }
}
