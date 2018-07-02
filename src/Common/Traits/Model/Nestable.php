<?php

namespace Nails\Common\Traits\Model;

use Nails\Factory;

/**
 * Trait Nestable
 * @package Nails\Common\Traits\Model
 */
trait Nestable
{
    /**
     * Returns the column to save breadcrumbs
     * @return string
     */
    public function getBreadcrumbsColumn()
    {
        return $this->getColumn('breadcrumbs', 'breadcrumbs');
    }

    // --------------------------------------------------------------------------

    /**
     * Generates breadcrumbs after creating an object
     *
     * @param array $aData         Data to create the item with
     * @param bool  $bReturnObject Whether to return an object or not
     *
     * @return mixed
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        $mResult = parent::create($aData, $bReturnObject);
        if ($mResult) {
            $this->saveBreadcrumbs($bReturnObject ? $mResult->id : $mResult);
        }
        return $mResult;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates breadcrumbs after updating an object
     *
     * @param array|integer $mIds  The ID(s) to update
     * @param array         $aData Data to update the items with¬
     *
     * @return mixed
     */
    public function update($mIds, array $aData = [])
    {
        $mResult = parent::update($mIds, $aData);
        if ($mResult) {
            $aIds = (array) $mIds;
            foreach ($aIds as $iId) {
                $this->saveBreadcrumbs($iId);
            }
        }
        return $mResult;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates breadcrumbs for the item
     *
     * @param integer $iItemId The item's ID
     */
    protected function saveBreadcrumbs($iItemId)
    {
        //  @todo (Pablo - 2018-07-01) - protect against infinite loops

        $oItem = $this->getById($iItemId);

        if (!empty($oItem)) {

            $aBreadcrumbs = [];
            $iParentId    = (int) $oItem->parent_id;
            while ($iParentId) {
                $oParentItem = $this->getById($iParentId);
                if ($oParentItem) {
                    $iParentId = $oParentItem->parent_id;
                    array_unshift(
                        $aBreadcrumbs,
                        (object) [
                            'id'    => $oParentItem->id,
                            'label' => $oParentItem->label,
                            'slug'  => $oParentItem->slug,
                        ]
                    );
                } else {
                    $iParentId = null;
                }
            }

            //  Save breadcrumbs to the current item
            parent::update(
                $iItemId,
                [
                    $this->getBreadcrumbsColumn() => json_encode($aBreadcrumbs)
                ]
            );

            //  Save breadcrumbs of all children
            $oDb = Factory::service('Database');
            $oDb->where('parent_id', $iItemId);
            $aChildren = $oDb->get($this->getTableName())->result();
            foreach ($aChildren as $oItem) {
                $this->saveBreadcrumbs($oItem->id);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Generates the URL for nestable objects; optionally place under a URL namespace
     *
     * @param \stdClass $oObj The object to generate the URL for
     *
     * @return string
     */
    protected function generateUrl($oObj)
    {
        if (empty($oObj->breadcrumbs)) {
            return null;
        }

        $aBreadcrumbs = json_decode($oObj->breadcrumbs);
        if (is_null($aBreadcrumbs)) {
            return null;
        }

        $aUrl   = arrayExtractProperty($aBreadcrumbs, 'slug');
        $aUrl[] = $oObj->slug;

        $sUrl       = implode('/', $aUrl);
        $sNamespace = defined('static::NESTED_URL_NAMESPACE') ? addTrailingSlash(static::NESTED_URL_NAMESPACE) : '';

        return $sNamespace . $sUrl;
    }
}
