<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Service\Database;
use Nails\Common\Resource;
use Nails\Factory;

/**
 * Trait Nestable
 *
 * @package Nails\Common\Traits\Model
 */
trait Nestable
{
    /**
     * Returns the column name for specific columns of interest
     *
     * @param string      $sColumn  The column to query
     * @param string|null $sDefault The default value if not defined
     *
     * @return string|null
     */
    abstract public function getColumn(string $sColumn, string $sDefault = null): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use as the ID
     *
     * @return string
     */
    abstract public function getColumnId(): string;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use as the label
     *
     * @return string|null
     */
    abstract public function getColumnLabel(): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use as the is_deleted
     *
     * @return string|null
     */
    abstract public function getColumnIsDeleted(): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $table
     *
     * @param bool $bIncludePrefix Whether to include the table's alias
     *
     * @return string
     */
    abstract public function getTableName(bool $bIncludePrefix = false): string;

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by it's ID
     *
     * @param int   $iId   The ID of the object to fetch
     * @param mixed $aData Any data to pass to getCountCommon()
     *
     * @return Resource|false
     */
    abstract public function getById($iId, array $aData = []);

    // --------------------------------------------------------------------------

    /**
     * Returns the column to save breadcrumbs
     *
     * @return string
     * @deprecated Use getColumnBreadcrumbs
     */
    public function getBreadcrumbsColumn()
    {
        return $this->getColumnBreadcrumbs();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to save the hierarchy
     *
     * @return string
     * @deprecated Use getColumnOrder
     */
    public function getOrderColumn()
    {
        return $this->getColumnOrder();
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to save the parent ID
     *
     * @return string
     * @deprecated Use getColumnParentId
     */
    public function getParentIdColumn()
    {
        return $this->getColumnParentId();
    }


    // --------------------------------------------------------------------------

    /**
     * Returns the column to save breadcrumbs
     *
     * @return string
     */
    public function getColumnBreadcrumbs()
    {
        return $this->getColumn('breadcrumbs', 'breadcrumbs');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to save the hierarchy
     *
     * @return string
     */
    public function getColumnOrder()
    {
        return $this->getColumn('order', 'order');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to save the parent ID
     *
     * @return string
     */
    public function getColumnParentId()
    {
        return $this->getColumn('parent_id', 'parent_id');
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
            $this->saveOrder();
            //  Refresh object to get updated breadcrumbs/URL
            if ($bReturnObject) {
                $mResult = $this->getById($mResult->id);
            }
        }
        return $mResult;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates breadcrumbs after updating an object
     *
     * @param array|int $mIds  The ID(s) to update
     * @param array     $aData Data to update the items with¬
     *
     * @return mixed
     */
    public function update($mIds, array $aData = []): bool
    {
        $mResult = parent::update($mIds, $aData);
        if ($mResult) {
            $aIds = (array) $mIds;
            foreach ($aIds as $iId) {
                $this->saveBreadcrumbs($iId);
            }
            $this->saveOrder();
        }
        return $mResult;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates breadcrumbs for the item
     *
     * @param int $iItemId The item's ID
     *
     * @throws FactoryException
     * @throws ModelException
     */
    protected function saveBreadcrumbs($iItemId)
    {
        if (!$this->shouldSaveBreadcrumbs($iItemId)) {
            return;
        }

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
                        (object) array_filter([
                            'id'    => $oParentItem->id,
                            'label' => !empty($oParentItem->label) ? $oParentItem->label : null,
                            'slug'  => !empty($oParentItem->slug) ? $oParentItem->slug : null,
                            'url'   => !empty($oParentItem->url) ? $oParentItem->url : null,
                        ])
                    );
                } else {
                    $iParentId = null;
                }
            }

            //  Save breadcrumbs to the current item
            parent::update(
                $iItemId,
                [
                    $this->getColumnBreadcrumbs() => json_encode($aBreadcrumbs),
                ]
            );

            //  Save breadcrumbs of all children
            /** @var Database $oDb */
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
     * Whether the Trait should attempt to save the breadcrumbs
     *
     * @return bool
     */
    public function shouldSaveBreadcrumbs($iItemId): bool
    {
        return !defined('static::SORTABLE_SAVE_BREADCRUMBS') || static::SORTABLE_SAVE_BREADCRUMBS;
    }

    // --------------------------------------------------------------------------

    /**
     * Save the item's orders
     *
     * @throws FactoryException
     */
    protected function saveOrder()
    {
        if (!$this->shouldSaveOrder()) {
            return;
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        $oDb->select([
            $this->getColumnId(),
            $this->getColumnParentId(),
        ]);
        if (!$this->isDestructiveDelete()) {
            $oDb->where($this->getColumnIsDeleted(), false);
        }
        $oDb->order_by($this->getColumnLabel());
        $aItems = $oDb->get($this->getTableName())->result();

        $iIndex = 0;
        $aItems = $this->flattenTree(
            $this->buildTree($aItems)
        );

        foreach ($aItems as $oItem) {
            $oDb->set($this->getColumnOrder(), ++$iIndex);
            $oDb->where($this->getColumnId(), $oItem->id);
            $oDb->update($this->getTableName());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the Trait should attempt to save the order
     *
     * @return bool
     */
    public function shouldSaveOrder(): bool
    {
        return !classUses($this, Sortable::class) && (!defined('static::SORTABLE_SAVE_ORDER') || static::SORTABLE_SAVE_ORDER);
    }

    // --------------------------------------------------------------------------

    /**
     * Builds a tree of objects
     *
     * This will only return complete trees beginning with the supplied $iParentId
     *
     * @param array $aItems    The items to sort
     * @param int   $iParentId The parent ID to sort on
     *
     * @return array
     * @todo (Pablo - 2019-05-10) - Support building partial trees
     */
    public function buildTree(array $aItems, int $iParentId = null): array
    {
        $aTemp         = [];
        $sIdColumn     = $this->getColumnId();
        $sParentColumn = $this->getColumnParentId();
        foreach ($aItems as $oItem) {
            if ($oItem->{$sParentColumn} == $iParentId) {
                $oItem->children = $this->buildTree($aItems, $oItem->{$sIdColumn});
                $aTemp[]         = $oItem;
            }
        }

        return $aTemp;
    }

    // --------------------------------------------------------------------------

    /**
     * Flattens a tree of objects
     *
     * @param array $aItems  The items to flatten
     * @param array $aOutput The array to write to
     *
     * @return array
     */
    public function flattenTree(array $aItems, &$aOutput = []): array
    {
        foreach ($aItems as $oItem) {
            $aOutput[] = $oItem;
            $this->flattenTree($oItem->children, $aOutput);
            unset($oItem->children);
        }

        return $aOutput;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates the URL for nestable objects; optionally place under a URL namespace
     *
     * @param Resource $oObj The object to generate the URL for
     *
     * @return string|null
     */
    public function generateUrl(Resource $oObj)
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

    // --------------------------------------------------------------------------

    /**
     * Retrieves the immediate children of an item
     *
     * @param int   $iId        The ID of the item
     * @param bool  $bRecursive Whetehr to recursively fetch children
     * @param array $aData      Any additional data to pass to the `getAll()` method
     *
     * @return mixed
     */
    public function getChildren($iId, $bRecursive = false, array $aData = [])
    {
        $aQueryData = $aData;
        if (!array_key_exists('where', $aQueryData)) {
            $aQueryData['where'] = [];
        }
        $aQueryData['where'][] = ['parent_id', $iId];

        $aChildren = $this->getAll($aQueryData);
        foreach ($aChildren as $oChild) {
            if ($bRecursive) {
                $oChild->children = $this->getChildren($oChild->id, $bRecursive, $aData);
            }
        }
        return $aChildren;
    }
}
