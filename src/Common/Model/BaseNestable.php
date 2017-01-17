<?php

/**
 * This class allows models to support nestable data
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @todo Implement methods/mechanic for fetching siblings
 * @todo Implement methods/mechanic for fetching children
 * @todo Implement methods/mechanic for searching/saving paths
 */

namespace Nails\Common\Model;

use Nails\Common\Exception\NailsException;

/**
 * Class BaseNestable
 * @package Nails\Common\Model
 */
class BaseNestable extends Base
{
    protected $tableParentColumn;

    // --------------------------------------------------------------------------

    /**
     * BaseNestable constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->tableParentColumn = 'parent_id';
    }

    // --------------------------------------------------------------------------

    /**
     * Similar to getAll, but returns the items in a nested array
     * @param array $aData Customise the result set
     * @param bool $bIncludeDeleted Whether to include deleted results
     * @param string $sProperty The property to which to assign children
     * @return array
     */
    public function getAllNested($aData = [], $bIncludeDeleted = false, $sProperty = 'children')
    {
        $aAll = parent::getAll(null, null, $aData, $bIncludeDeleted);

        return $this->nestItems($aAll, $sProperty);
    }

    // --------------------------------------------------------------------------

    /**
     * Nests items
     * @param array $aAll The items to nest
     * @param string $sProperty The property to which to assign children
     * @param null $iParentId The parent ID to check against
     * @return array
     * @throws NailsException
     */
    protected function nestItems($aAll, $sProperty, $iParentId = null)
    {
        $aOut       = [];
        $sIdCol     = $this->tableIdColumn;
        $sParentCol = $this->tableParentColumn;

        foreach ($aAll as $oItem) {

            //  Check the parent ID column exists
            if (!property_exists($oItem, $sParentCol)) {
                throw new NailsException('Parent column "' . $sParentCol . '" is not defined on result set.');
            }

            $iThisParentId = $oItem->{$sParentCol};

            if ($iThisParentId == $iParentId) {
                $oItem->{$sProperty} = $this->nestItems($aAll, $sProperty, $oItem->{$sIdCol});
                $aOut[]              = $oItem;
            } else if (!property_exists($oItem, $sProperty)) {
                $oItem->{$sProperty} = [];
            }
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns nested items, but as a flat array. The item's parent's labels will be prepended to the string
     * @param array $aData Customise the result set
     * @param string $sSeparator The separator to use between labels
     * @param bool $bIncludeDeleted Whether to include deleted results
     * @return array
     */
    public function getAllNestedFlat($aData = [], $sSeparator = ' &rsaquo; ', $bIncludeDeleted = false)
    {
        $aAllNested = $this->getAllNested($aData, $bIncludeDeleted, 'children');
        $aFlattened = $this->flattenItems($aAllNested);

        foreach ($aFlattened as &$aLabels) {
            $aLabels = implode($sSeparator, $aLabels);
        }

        return $aFlattened;
    }

    // --------------------------------------------------------------------------

    /**
     * @param array $aAllNested The nested items
     * @param array $aParentLabels An array of the previous parent's labels
     * @return array
     * @throws NailsException
     */
    protected function flattenItems($aAllNested, $aParentLabels = [])
    {
        $aOut      = [];
        $sIdCol    = $this->tableIdColumn;
        $sLabelCol = $this->tableLabelColumn;

        foreach ($aAllNested as $oItem) {

            //  Check the label column exists
            if (!property_exists($oItem, $sLabelCol)) {
                throw new NailsException('Label column "' . $sLabelCol . '" is not defined on result set.');
            }

            $aItemLabels             = array_merge($aParentLabels, [$oItem->{$sLabelCol}]);
            $aOut[$oItem->{$sIdCol}] = $aItemLabels;

            if (!empty($oItem->children)) {
                $aChildren = $this->flattenItems($oItem->children, array_merge($aParentLabels, [$oItem->{$sLabelCol}]));
                $aOut      = $aOut + $aChildren;
            }
        }

        return $aOut;
    }
}
