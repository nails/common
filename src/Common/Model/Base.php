<?php

/**
 * This class brings about uniformity to Nails models.
 *
 * @package     Nails
 * @subpackage  common
 * @category    model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Model;

use Behat\Transliterator\Transliterator;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Factory\Model\Field;
use Nails\Common\Helper;
use Nails\Common\Resource;
use Nails\Common\Service\Database;
use Nails\Common\Service\Event;
use Nails\Common\Service\FormValidation;
use Nails\Common\Service\Locale;
use Nails\Common\Traits\Caching;
use Nails\Common\Traits\ErrorHandling;
use Nails\Common\Traits\GetCountCommon;
use Nails\Common\Traits\Model\Localised;
use Nails\Common\Traits\Model\Sortable;
use Nails\Common\Traits\Model\Timestamps;
use Nails\Components;
use Nails\Factory;

/**
 * Class Base
 *
 * @package Nails\Common\Model
 */
abstract class Base
{
    use ErrorHandling;
    use Caching;
    use GetCountCommon;
    use Timestamps;

    // --------------------------------------------------------------------------

    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = null;

    /**
     * The alias to give the table
     *
     * @var string
     */
    const TABLE_ALIAS = null;

    /**
     * Whether this model uses destructive delete or not
     *
     * @var bool
     */
    const DESTRUCTIVE_DELETE = true;

    /**
     * Expandable objects of type EXPANDABLE_TYPE_MANY are a 1 to many relationship
     * where a property of the child object is the ID of the parent object.
     */
    const EXPANDABLE_TYPE_MANY = 0;

    /**
     * Expandable objects of type EXPANDABLE_TYPE_SINGLE are a 1 to 1 relationship
     * where a property of the parent object is the ID of the child object.
     */
    const EXPANDABLE_TYPE_SINGLE = 1;

    /**
     * Magic trigger for expanding all expandable objects
     */
    const EXPAND_ALL = 'ALL';

    /**
     * The event to fire before an item is created
     *
     * @var string
     */
    const EVENT_CREATING = 'CREATING';

    /**
     * The event to fire after an item is created
     *
     * @var string
     */
    const EVENT_CREATED = 'CREATED';

    /**
     * The event to fire before an item is updated
     *
     * @var string
     */
    const EVENT_UPDATING = 'UPDATING';

    /**
     * The event to fire after an item is updated
     *
     * @var string
     */
    const EVENT_UPDATED = 'UPDATED';

    /**
     * The event to fire before an item is deleted
     *
     * @var string
     */
    const EVENT_DELETING = 'DELETING';

    /**
     * The event to fire after an item is deleted
     *
     * @var string
     */
    const EVENT_DELETED = 'DELETED';

    /**
     * The event to fire before an item is destroyed
     *
     * @var string
     */
    const EVENT_DESTROYING = 'DESTROYING';

    /**
     * The event to fire after an item is destroyed
     *
     * @var string
     */
    const EVENT_DESTROYED = 'DESTROYED';

    /**
     * The event to fire before an item is restored
     *
     * @var string
     */
    const EVENT_RESTORING = 'RESTORING';

    /**
     * The event to fire after an item is restored
     *
     * @var string
     */
    const EVENT_RESTORED = 'RESTORED';

    /**
     * Whether to automatically set timestamps or not
     *
     * @var bool
     */
    const AUTO_SET_TIMESTAMP = true;

    /**
     * Whether to automatically set created/modified users or not
     *
     * @var bool
     */
    const AUTO_SET_USER = true;

    /**
     * Whether to automatically set tokens or not
     *
     * @var bool
     */
    const AUTO_SET_TOKEN = false;

    /**
     * Whether to automatically set slugs or not
     *
     * @var bool
     */
    const AUTO_SET_SLUG = false;

    /**
     * When true, the model will not attempt to automatically generate a slug when updating
     *
     * @var bool
     */
    const AUTO_SET_SLUG_IMMUTABLE = true;

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'Entity';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = null;

    /**
     * The property of the config array which contains the search keywords
     *
     * @var string
     */
    const KEYWORD_PROPERTY = 'keywords';

    /**
     * Any fields which should be considered sensitive
     *
     * @var string[]
     */
    const SENSITIVE_FIELDS = [];

    /**
     * The mask to mask when generating tokens
     *
     * @var string
     */
    const TOKEN_MASK = null;

    /**
     * The string for sorting in ascending order
     *
     * @var string
     */
    const SORT_ASC = 'asc';

    /**
     * The string for sorting in descending order
     *
     * @var string
     */
    const SORT_DESC = 'desc';

    /**
     * The default column to sort on
     *
     * @var string|null
     */
    const DEFAULT_SORT_COLUMN = null;

    /**
     * The default sort order
     *
     * @var string
     */
    const DEFAULT_SORT_ORDER = null;

    // --------------------------------------------------------------------------

    /**
     * The name of the "id" column
     *
     * @var string
     */
    protected $tableIdColumn = 'id';

    /**
     * The name of the "slug" column
     *
     * @var string
     */
    protected $tableSlugColumn = 'slug';

    /**
     * The name of the "token" column
     *
     * @var string
     */
    protected $tableTokenColumn = 'token';

    /**
     * The name of the "label" column
     *
     * @var string
     */
    protected $tableLabelColumn = 'label';

    /**
     * The name of the "created" column
     *
     * @var string
     */
    protected $tableCreatedColumn = 'created';

    /**
     * The name of the "created by" column
     *
     * @var string
     */
    protected $tableCreatedByColumn = 'created_by';

    /**
     * The name of the "modified" column
     *
     * @var string
     */
    protected $tableModifiedColumn = 'modified';

    /**
     * The name of the "modified by" column
     *
     * @var string
     */
    protected $tableModifiedByColumn = 'modified_by';

    /**
     * The name of the "deleted" column
     *
     * @var string
     */
    protected $tableIsDeletedColumn = 'is_deleted';

    /**
     * The columns which should be included when searching by keyword
     *
     * @var array
     */
    protected $searchableFields = [];

    /**
     * Keeps a track of the columns which have been used by getByColumn(); allows
     * for us to more easily invalidate caches
     *
     * @var array
     */
    protected $aCacheColumns = [];

    /**
     * The model's expandable field definitions
     *
     * @var array
     */
    protected $aExpandableFields = [];

    /**
     * The default number of items to render per page of paginated results
     *
     * @var int
     */
    protected $perPage = 50;

    /**
     * --------------------------------------------------------------------------
     * DEPRECATED PROPERTIES
     * --------------------------------------------------------------------------
     */

    /**
     * The name of the table this model binds to
     *
     * @var string|null
     * @deprecated Use constant TABLE instead
     */
    protected $table;

    /**
     * The alias to give this model's table
     *
     * @var string
     * @deprecated Leave null to auto-define
     */
    protected $tableAlias;

    /**
     * Whether to automatically set slugs
     *
     * @var bool|null
     * @deprecated Use constant AUTO_SET_SLUG instead
     */
    protected $tableAutoSetSlugs;

    /**
     * Whether to automatically set tokens
     *
     * @var bool|null
     * @deprecated Use constant AUTO_SET_TOKEN instead
     */
    protected $tableAutoSetTokens;

    /**
     * @var bool|null
     * @deprecated Use constant DESTRUCTIVE_DELETE instead
     */
    protected $destructiveDelete;

    /**
     * Override the default token mask when automatically generating tokens for items
     *
     * @var string
     * @deprecated Use constant TOKEN_MASK instead
     */
    protected $sTokenMask;

    /**
     * The default column to use for sorting items
     *
     * @var string|null
     * @deprecated Use constant DEFAULT_SORT_COLUMN instead
     */
    protected $defaultSortColumn;

    /**
     * The default sort order
     *
     * @var string
     * @deprecated Use constant DEFAULT_SORT_ORDER instead
     */
    protected $defaultSortOrder = self::SORT_ASC;

    // --------------------------------------------------------------------------

    /**
     * Base constructor.
     */
    public function __construct()
    {
        $this->clearErrors();

        $this->aCacheColumns = [
            $this->getColumn('id'),
            $this->getColumn('slug'),
            $this->getColumn('token'),
        ];

        /**
         * Set up default searchable fields. Each field is passed directly to the
         * `column` parameter in getCountCommon() so can be in any form accepted by that.
         *
         * @todo  allow some sort of cleansing callback so that models can prep the
         * search string if needed.
         */
        if (empty($this->searchableFields)) {
            $this->searchableFields[] = $this->getColumn('id');
            $this->searchableFields[] = $this->getColumn('label');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Destruct the model
     *
     * @return void
     */
    public function __destruct()
    {
        /**
         * @todo: decide whether this is necessary; should caches be persistent;
         * gut says yes.
         */

        $this->clearCache();
    }

    /**
     * --------------------------------------------------------------------------
     *
     * MUTATION METHODS
     * These methods provide a consistent interface for creating, and manipulating
     * objects that this model represents. These methods should be extended if any
     * custom functionality is required.
     *
     * See the docs for more info
     *
     * @TODO: link to docs
     *
     * --------------------------------------------------------------------------
     */

    /**
     * Creates a new object
     *
     * @param array $aData         The data to create the object with
     * @param bool  $bReturnObject Whether to return just the new ID or the full object
     *
     * @return mixed
     * @throws ModelException
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        $this->triggerEvent(static::EVENT_CREATING, [$aData, $this]);

        /** @var Database $oDb */
        $oDb    = Factory::service('Database');
        $sTable = $this->getTableName();

        // --------------------------------------------------------------------------

        //  Execute the data pre-write hooks
        //  Generic write hook, before specific create hook
        $this
            ->prepareWriteData($aData)
            ->prepareCreateData($aData);

        // --------------------------------------------------------------------------

        //  If there are any expandable fields which should automatically save,
        //  then separate them out now
        $aAutoSaveExpandableFields = $this->autoSaveExpandableFieldsExtract($aData);

        // --------------------------------------------------------------------------

        $this
            ->setDataTimestamps($aData)
            ->setDataUsers($aData)
            ->setDataSlug($aData)
            ->setDataToken($aData);

        // --------------------------------------------------------------------------

        if ($this->saveToDb($aData)) {

            if (classUses($this, Localised::class)) {
                $iId = $aData['id'];
            } else {
                $iId = $oDb->insert_id();
            }

            $this
                ->autoSaveExpandableFieldsSave($iId, $aAutoSaveExpandableFields)
                ->afterCreate($iId)
                ->afterWrite($iId)
                ->triggerEvent(static::EVENT_CREATED, [$iId, $this]);

            return $bReturnObject ? $this->skipCache()->getById($iId) : $iId;

        } else {
            return null;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Inserts a batch of data into the table
     *
     * @param array $aData The data to insert
     *
     * @return bool
     */
    public function createMany(array $aData)
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        try {

            $oDb->transaction()->start();
            foreach ($aData as $aDatum) {
                if (!$this->create($aDatum)) {
                    throw new ModelException('Failed to create item with data: ' . json_encode($aDatum));
                }
            }
            $oDb->transaction()->commit();
            return true;

        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Updates an existing object
     *
     * @param int   $iId   The ID of the object to update
     * @param array $aData The data to update the object with
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function update($iId, array $aData = []): bool
    {
        $this->triggerEvent(static::EVENT_UPDATING, [$aData, $this]);

        $sAlias = $this->getTableAlias(true);
        $sTable = $this->getTableName(true);
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        // --------------------------------------------------------------------------

        //  Execute the data pre-write hooks
        //  Generic write hook, before specific update hook
        $this
            ->prepareWriteData($aData)
            ->prepareUpdateData($aData);

        // --------------------------------------------------------------------------

        //  If there are any expandable fields which should automatically save,
        //  then separate them out now
        $aAutoSaveExpandableFields = $this->autoSaveExpandableFieldsExtract($aData);

        // --------------------------------------------------------------------------

        $this
            ->setDataTimestamps($aData, false)
            ->setDataUsers($aData, false)
            ->setDataSlug($aData, false, $iId)
            ->setDataToken($aData, false);

        // --------------------------------------------------------------------------

        if ($this->saveToDb($aData, $iId)) {

            //  Save any expandable objects
            $this
                ->autoSaveExpandableFieldsSave($iId, $aAutoSaveExpandableFields)
                ->afterUpdate($iId)
                ->afterWrite($iId);

            //  Clear the cache
            foreach ($this->aCacheColumns as $sColumn) {
                $sKey = strtoupper($sColumn) . ':' . json_encode($iId);
                $this->unsetCachePrefix($sKey);
            }

            $this->triggerEvent(static::EVENT_UPDATED, [$iId, $this]);

            return true;

        } else {
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update many items with the same data
     *
     * @param array $aIds  An array of IDs to update
     * @param array $aData The data to set
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function updateMany(array $aIds, array $aData = []): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        /**
         * Note the current timestamp behaiour, if we want to skip it then we'll
         * need to re-set it on each iteration of the loop as `update()` will
         * reset it.
         */
        $bSkipUpdateTimestamps = $this->bSkipUpdateTimestamp;

        try {
            $oDb->transaction()->start();
            foreach ($aIds as $iId) {

                if ($bSkipUpdateTimestamps) {
                    $this->skipUpdateTimestamp();
                }

                if (!$this->update($iId, $aData)) {
                    throw new ModelException('Failed to update item with ID ' . $iId);
                }
            }
            $oDb->transaction()->commit();
            return true;
        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Provides an opportunity for models to manipulate the data passed to create()
     * before it is processed
     *
     * @param array $aData The data being passed
     *
     * @return $this
     */
    protected function prepareCreateData(array &$aData): Base
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Provides an opportunity for models to manipulate the data passed to update()
     * before it is processed
     *
     * @param array $aData The data being passed
     *
     * @return $this
     */
    protected function prepareUpdateData(array &$aData): Base
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Provides an opportunity for models to manipulate the data passed to create()
     * or update() before it is processed
     *
     * @param array $aData The data being passed
     *
     * @return $this
     */
    protected function prepareWriteData(array &$aData): Base
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Provides an opportunity for models to perform an action after a create operation
     *
     * @param int $iId The ID of the item which was created
     *
     * @return $this
     */
    protected function afterCreate(int $iId): Base
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Provides an opportunity for models to perform an action after an update operation
     *
     * @param int $iId The ID of the item which was updated
     *
     * @return $this
     */
    protected function afterUpdate(int $iId): Base
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Provides an opportunity for models to perform an action after a create or
     * an update operation
     *
     * @param int $iId The ID of the item which was created or updated
     *
     * @return $this
     */
    protected function afterWrite(int $iId): Base
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the created/modified user IDs on the $aData array
     *
     * @param array $aData       The data being passed to the model
     * @param bool  $bSetCreated Whether to set the created user or not
     *
     * @return $this
     */
    protected function setDataUsers(array &$aData, bool $bSetCreated = true): Base
    {
        if ($this->isAutoSetUsers()) {
            if (isLoggedIn()) {
                if ($bSetCreated && empty($aData[$this->getColumn('created_by')])) {
                    $aData[$this->getColumn('created_by')] = activeUser('id');
                }
                if (empty($aData[$this->getColumn('modified_by')])) {
                    $aData[$this->getColumn('modified_by')] = activeUser('id');
                }
            } else {
                if ($bSetCreated && empty($aData[$this->getColumn('created_by')])) {
                    $aData[$this->getColumn('created_by')] = null;
                }
                if (empty($aData[$this->getColumn('modified_by')])) {
                    $aData[$this->getColumn('modified_by')] = null;
                }
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the slug on the $aData array
     *
     * @param array $aData     The data being passed to the model
     * @param bool  $bIsCreate Whether the method is being called in a create context or not
     * @param int   $iIgnoreId The ID of an item to ignore
     *
     * @return $this
     */
    protected function setDataSlug(array &$aData, bool $bIsCreate = true, int $iIgnoreId = null): Base
    {
        if ($this->isAutoSetSlugs() &&
            empty($aData[$this->getColumn('slug')]) &&
            ($bIsCreate || !static::AUTO_SET_SLUG_IMMUTABLE)
        ) {

            if (empty($this->getColumn('slug'))) {
                throw new ModelException(static::class . '::create() Slug column variable not set', 1);
            }

            if (empty($this->getColumn('label'))) {
                throw new ModelException(static::class . '::create() Label column variable not set', 1);
            }

            /**
             * We only want to set slugs if:
             * - It is a create operation
             * - it is an update operation and the label column is defined (else, lease it as it is)
             */
            if ($bIsCreate || array_key_exists($this->getColumn('label'), $aData)) {

                if (empty($aData[$this->getColumn('label')])) {
                    throw new ModelException(
                        static::class . '::create() "' . $this->getColumn('label') .
                        '" is required when automatically generating slugs.'
                    );
                }

                $aData[$this->getColumn('slug')] = $this->generateSlug(
                    $aData,
                    $iIgnoreId
                );
            }
        }

        return $this;
    }

    /**
     * Set the token on the $aData array
     *
     * @param array $aData The data being passed to the model
     *
     * @return $this
     * @throws ModelException
     */
    protected function setDataToken(array &$aData, $bIsCreate = true): Base
    {
        if ($bIsCreate && $this->isAutoSetTokens() && empty($aData[$this->getColumn('token')])) {
            if (empty($this->getColumn('token'))) {
                throw new ModelException(static::class . '::create() Token column variable not set', 1);
            }
            $aData[$this->getColumn('token')] = $this->generateToken();
        } elseif (!$bIsCreate) {
            //  Automatically set tokens are permanent and immutable
            if ($this->isAutoSetTokens() && !empty($aData[$this->getColumn('token')])) {
                unset($aData[$this->getColumn('token')]);
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Commits $aData to the database
     *
     * @param array $aData the data array
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    protected function saveToDb(array $aData, int $iId = null): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        if (!empty($aData)) {
            if (!classUses($this, Localised::class)) {
                unset($aData['id']);
            }
            foreach ($aData as $sColumn => $mValue) {
                if (is_array($mValue)) {
                    $mSetValue = isset($mValue[0]) ? $mValue[0] : null;
                    $bEscape   = isset($mValue[1]) ? (bool) $mValue[1] : true;
                    $oDb->set($sColumn, $mSetValue, $bEscape);
                } else {
                    $oDb->set($sColumn, $mValue);
                }
            }
        }

        if (!$iId) {
            $oDb->insert($this->getTableName());
            return (bool) $oDb->affected_rows();
        } else {
            $oDb->where('id', $iId);
            return $oDb->update($this->getTableName());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Marks an object as deleted
     *
     * If destructive deletion is enabled then this method will permanently
     * destroy the object. If Non-destructive deletion is enabled then the
     * $this->getColumn('is_deleted') field will be set to true.
     *
     * @param int $iId The ID of the object to mark as deleted
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function delete($iId): bool
    {
        $oItem = $this->getById($iId);
        if (empty($oItem)) {
            $this->setError('Item does not exist.');
            return false;
        }

        $this->triggerEvent(static::EVENT_DELETING, [$oItem, $this]);

        if ($this->isDestructiveDelete()) {
            $bResult = $this->destroy($iId);
        } else {
            $bResult = $this->update($iId, [$this->getColumn('is_deleted') => true]);
        }

        if ($bResult) {
            $this->triggerEvent(static::EVENT_DELETED, [$iId, $oItem, $this]);
        }

        return $bResult;
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes many items
     *
     * @param array $aIds An array of item IDs to delete
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function deleteMany(array $aIds): bool
    {
        /**
         * Ensure that passed IDs are unique; if they're not then we'll encounter
         * an error when the second iteration of the item is hit because it has
         * already been deleted.
         */
        $aIds = array_unique($aIds);
        $aIds = array_filter($aIds);
        $aIds = array_values($aIds);

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        try {

            $oDb->transaction()->start();
            foreach ($aIds as $iId) {
                if (!$this->delete($iId)) {
                    throw new ModelException('Failed to delete item with ID ' . $iId . '. ' . $this->lastError());
                }
            }
            $oDb->transaction()->commit();
            return true;

        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes items generated as a result of a lookup
     *
     * @param array $aData The `where` coponent of a config array
     *
     * @throws FactoryException
     * @throws ModelException
     */
    public function deleteWhere(array $aData)
    {
        $oResults = $this->getAllRawQuery(['where' => $aData]);

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        try {

            $oDb->transaction()->start();
            while ($oResult = $oResults->unbuffered_row()) {
                if (!$this->delete($oResult->id)) {
                    throw new ModelException('Failed to delete item with ID ' . $oResult->id . '. ' . $this->lastError());
                }
            }
            $oDb->transaction()->commit();
            return true;

        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unmarks an object as deleted
     *
     * If destructive deletion is enabled then this method will return null.
     * If Non-destructive deletion is enabled then the $this->getColumn('is_deleted')
     * field will be set to false.
     *
     * @param int $iId The ID of the object to restore
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function restore($iId): ?bool
    {
        $this->triggerEvent(static::EVENT_RESTORING, [$iId, $this]);

        if ($this->isDestructiveDelete()) {
            return null;
        } elseif ($this->update($iId, [$this->getColumn('is_deleted') => false])) {
            $this->triggerEvent(static::EVENT_RESTORED, [$iId, $this]);
            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Permanently deletes an object
     *
     * This method will attempt to delete the row from the table, regardless of whether
     * destructive deletion is enabled or not.
     *
     * @param int $iId The ID  of the object to destroy
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function destroy($iId): bool
    {
        $this->triggerEvent(static::EVENT_DESTROYING, [$iId, $this]);

        /** @var Database $oDb */
        $oDb    = Factory::service('Database');
        $sTable = $this->getTableName();

        // --------------------------------------------------------------------------

        $oDb->where('id', $iId);
        if ($oDb->delete($sTable)) {
            $this->triggerEvent(static::EVENT_DESTROYED, [$iId, $this]);
            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Desrtroys multiple items
     *
     * @param array $aIds An array of item IDs to destroy
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function destroyMany(array $aIds): bool
    {
        /**
         * Ensure that passed IDs are unique; if they're not then we'll encounter
         * an error when the second iteration of the item is hit because it has
         * already been destroyed.
         */
        $aIds = array_unique($aIds);
        $aIds = array_filter($aIds);
        $aIds = array_values($aIds);

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        try {
            $oDb->transaction()->start();
            foreach ($aIds as $iId) {
                if (!$this->destroy($iId)) {
                    throw new ModelException('Failed to destroy item with ID ' . $iId);
                }
            }
            $oDb->transaction()->commit();
            return true;
        } catch (\Exception $e) {
            $oDb->transaction()->rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Truncates the entire table
     *
     * @return bool
     */
    public function truncate()
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        return $oDb->truncate($this->getTableName());
    }

    /**
     * --------------------------------------------------------------------------
     *
     * RETRIEVAL & COUNTING METHODS
     * These methods provide a consistent interface for retrieving and counting objects
     *
     * --------------------------------------------------------------------------
     */

    /**
     * Fetches all objects, optionally paginated. Returns the basic query object with no formatting.
     *
     * @param int|null|array $iPage           The page number of the results, if null then no pagination; also accepts an $aData array
     * @param int|null       $iPerPage        How many items per page of paginated results
     * @param array          $aData           Any data to pass to getCountCommon()
     * @param bool           $bIncludeDeleted If non-destructive delete is enabled then this flag allows you to include deleted
     *                                        items
     *
     * @return \CI_DB_mysqli_result
     * @throws ModelException
     * @throws FactoryException
     */
    public function getAllRawQuery($iPage = null, $iPerPage = null, array $aData = [], $bIncludeDeleted = false): \CI_DB_mysqli_result
    {
        //  If the first value is an array then treat as if called with getAll(null, null, $aData);
        if (is_array($iPage)) {
            $aData = $iPage;
            $iPage = null;
        }

        // --------------------------------------------------------------------------

        /** @var Database $oDb */
        $oDb    = Factory::service('Database');
        $sTable = $this->getTableName(true);

        // --------------------------------------------------------------------------

        //  Define the default sorting
        if (empty($aData['sort']) && $this->getDefaultSortColumn()) {
            $aData['sort'] = [
                $this->getDefaultSortColumn(),
                $this->getDefaultSortOrder(),
            ];
        }

        // --------------------------------------------------------------------------

        if (!empty($aData[static::KEYWORD_PROPERTY])) {
            $this->applySearchConditionals($aData, $aData[static::KEYWORD_PROPERTY]);
        }

        // --------------------------------------------------------------------------

        //  Apply common items; pass $aData
        $this->getCountCommon($aData);

        // --------------------------------------------------------------------------

        if (array_key_exists('limit', $aData)) {
            if (is_numeric($aData['limit'])) {
                //  Consider limit to be the maximum number of items to return
                $iPage    = 0;
                $iPerPage = $aData['limit'];
            } elseif (is_array($aData['limit'])) {
                //  Consider the first element to be the page number and the second the number of results
                $iPage    = Helper\ArrayHelper::getFromArray(0, $aData['limit'], 0);
                $iPerPage = Helper\ArrayHelper::getFromArray(1, $aData['limit']);
            }
        }

        //  Facilitate pagination
        if (!is_null($iPage)) {

            /**
             * Adjust the page variable, reduce by one so that the offset is calculated
             * correctly. Make sure we don't go into negative numbers
             */

            $iPage--;
            $iPage = $iPage < 0 ? 0 : $iPage;

            //  Work out what the offset should be
            $iPerPage = is_null($iPerPage) ? $this->perPage : (int) $iPerPage;
            $iOffset  = $iPage * $iPerPage;

            $oDb->limit($iPerPage, $iOffset);
        }

        // --------------------------------------------------------------------------

        //  If non-destructive delete is enabled then apply the delete query
        if (!$this->isDestructiveDelete() && !$bIncludeDeleted) {
            $oDb->where($this->getTableAlias(true) . $this->getColumn('is_deleted'), false);
        }

        // --------------------------------------------------------------------------

        if (array_key_exists('group_by', $aData)) {
            $oDb->group_by($aData['group_by']);
        }

        // --------------------------------------------------------------------------

        return $oDb->get($sTable);
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all objects and formats them, optionally paginated
     *
     * @param int|null|array $iPage           The page number of the results, if null then no pagination; also accepts an $aData array
     * @param int|null       $iPerPage        How many items per page of paginated results
     * @param mixed          $aData           Any data to pass to getCountCommon()
     * @param bool           $bIncludeDeleted If non-destructive delete is enabled then this flag allows you to include deleted items
     *
     * @return Resource[]
     * @throws ModelException
     */
    public function getAll($iPage = null, $iPerPage = null, array $aData = [], $bIncludeDeleted = false): array
    {
        //  If the first value is an array then treat as if called with getAll(null, null, $aData);
        if (is_array($iPage)) {
            $aData = $iPage;
            $iPage = null;
        }

        $oResults    = $this->getAllRawQuery($iPage, $iPerPage, $aData, $bIncludeDeleted);
        $aResults    = $oResults->result();
        $iNumResults = count($aResults);

        $this->expandExpandableFields($aResults, $aData);

        for ($i = 0; $i < $iNumResults; $i++) {
            $this->formatObject($aResults[$i], $aData);
        }

        return $aResults;
    }

    // --------------------------------------------------------------------------

    /**
     * Handle requests for expanding objects. There are two types of expandable objects:
     *  1. Fields which are an ID, these can be expanded by the appropriate model (1 to 1)
     *  2. Query a model for items which reference this item's ID  (1 to many)
     *
     * @param array $aResults The results to iterate over
     * @param array $aData    The configuration array
     */
    protected function expandExpandableFields(array &$aResults, array $aData)
    {
        $aExpandableFields = $this->getExpandableFields();

        if (empty($aExpandableFields)) {
            return;
        }

        // --------------------------------------------------------------------------

        /**
         * Extract any Expand and Expand\Group objects into the main expand property
         * and compile them.
         */
        $aHelpers = Helper\Model\Expand::extractHelpers($aData);

        if (empty($aData['expand'])) {
            $aData['expand'] = [];
        }

        if ($aData['expand'] !== static::EXPAND_ALL) {

            $aData['expand'] = array_merge($aData['expand'], $aHelpers);

            $aHelpers = Helper\Model\Expand::extractHelpers($aData['expand']);

            foreach ($aHelpers as $mTrigger) {
                if ($mTrigger instanceof Helper\Model\Expand\Group) {
                    $aData['expand'] = array_merge($aData['expand'], $mTrigger->compile());
                } elseif ($mTrigger instanceof Helper\Model\Expand) {
                    $aData['expand'][] = $mTrigger->compile();
                }
            }

            $aData['expand'] = array_values($aData['expand']);
        }

        // --------------------------------------------------------------------------

        /**
         * Prepare the expand request; The developer can pass an array of triggers to expand, any of
         * those triggers may themselves be an array with options to pass to the model (e.g to expand
         * a field in the expanded object, or to order etc). Therefore we must prepare two arrays:
         *  1. a flat list of triggers to expand
         *  2. a list of config arrays to pass to the model
         */

        $aTriggers    = [];
        $aTriggerData = [];

        if (array_key_exists('expand', $aData) && is_array($aData['expand'])) {
            foreach ($aData['expand'] as $mTrigger) {
                if (is_string($mTrigger)) {
                    $aTriggers[]             = $mTrigger;
                    $aTriggerData[$mTrigger] = [];
                } elseif (is_array($mTrigger)) {
                    $sArrayTrigger     = Helper\ArrayHelper::getFromArray(0, $mTrigger) ?: Helper\ArrayHelper::getFromArray('trigger', $mTrigger);
                    $aArrayTriggerData = Helper\ArrayHelper::getFromArray(1, $mTrigger) ?: Helper\ArrayHelper::getFromArray('data', $mTrigger, []);
                    if (!empty($sArrayTrigger)) {
                        $aTriggers[]                  = $sArrayTrigger;
                        $aTriggerData[$sArrayTrigger] = $aArrayTriggerData;
                    }
                }
            }
        }

        foreach ($aExpandableFields as $oExpandableField) {

            $bAutoExpand            = $oExpandableField->auto_expand;
            $bExpandAll             = false;
            $bExpandForTrigger      = false;
            $bExpandForTriggerCount = false;
            //  If we're not auto-expanding, check if we're expanding everything
            if (!$bAutoExpand && array_key_exists('expand', $aData)) {
                $bExpandAll = $aData['expand'] === static::EXPAND_ALL;
            }

            //  If we're not auto-expanding or expanding everything, check if we should expand based
            //  on the `expand` index of $aTriggers
            if (!$bAutoExpand && !$bExpandAll) {
                $bExpandForTrigger      = in_array($oExpandableField->trigger, $aTriggers);
                $bExpandForTriggerCount = in_array($oExpandableField->trigger . ':count', $aTriggers);
            }

            if ($bAutoExpand || $bExpandAll || $bExpandForTrigger || $bExpandForTriggerCount) {

                //  Merge any data defined with the expandable field with any custom data added by the expansion
                $aMergedData = array_merge(
                    $oExpandableField->data,
                    Helper\ArrayHelper::getFromArray($oExpandableField->trigger, $aTriggerData, [])
                );

                if ($oExpandableField->type === static::EXPANDABLE_TYPE_SINGLE) {

                    $this->getSingleAssociatedItem(
                        $aResults,
                        $oExpandableField->id_column,
                        $oExpandableField->property,
                        $oExpandableField->model,
                        $oExpandableField->provider,
                        $aMergedData
                    );

                } elseif (($bAutoExpand || $bExpandForTrigger || $bExpandAll) && $oExpandableField->type === static::EXPANDABLE_TYPE_MANY) {

                    $this->getManyAssociatedItems(
                        $aResults,
                        $oExpandableField->property,
                        $oExpandableField->id_column,
                        $oExpandableField->model,
                        $oExpandableField->provider,
                        $aMergedData
                    );

                } elseif ($bExpandForTriggerCount && $oExpandableField->type === static::EXPANDABLE_TYPE_MANY) {

                    $this->countManyAssociatedItems(
                        $aResults,
                        $oExpandableField->property,
                        $oExpandableField->id_column,
                        $oExpandableField->model,
                        $oExpandableField->provider,
                        $aMergedData
                    );
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all objects as a flat array
     *
     * @param int|null $iPage           The page number of the results
     * @param int|null $iPerPage        The number of items per page
     * @param array    $aData           Any data to pass to getCountCommon()
     * @param bool     $bIncludeDeleted Whether or not to include deleted items
     *
     * @return array
     * @throws ModelException
     */
    public function getAllFlat($iPage = null, $iPerPage = null, array $aData = [], $bIncludeDeleted = false)
    {
        if (!array_key_exists('select', $aData)) {
            $aData['select'] = [];
        }

        $aData['select'][] = $this->getColumn('id');
        $aData['select'][] = $this->getColumn('label');

        $aItems = $this->getAllRawQuery($iPage, $iPerPage, $aData, $bIncludeDeleted)->result();
        $aOut   = [];

        //  Nothing returned? Skip the rest of this method, it's pointless.
        if (!$aItems) {
            return [];
        }

        // --------------------------------------------------------------------------

        /**
         * Test columns
         * We need an ID, label is encouraged but if not available we can use the ID
         */
        $oTest = reset($aItems);

        if (!property_exists($oTest, $this->getColumn('id'))) {
            throw new ModelException(
                static::class . '::getAllFlat() "' . $this->getColumn('id') . '" is not a valid column.'
            );
        }

        unset($oTest);

        // --------------------------------------------------------------------------

        foreach ($aItems as $oItem) {

            $iId    = $oItem->{$this->getColumn('id')};
            $sLabel = $oItem->{$this->getColumn('label')} ?? 'Item #' . $iId;

            $aOut[$iId] = $sLabel;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns item(s) by a column and its value
     *
     * @param string $sColumn      The column to search on
     * @param mixed  $mValue       The value(s) to look for
     * @param array  $aData        Any additional data to pass in
     * @param bool   $bReturnsMany Whether the method expects to return a single item, or many
     *
     * @return Resource[]|Resource|null
     * @throws ModelException
     */
    protected function getByColumn($sColumn, $mValue, array $aData, $bReturnsMany = false)
    {
        if (empty($sColumn)) {
            throw new ModelException('Column cannot be empty');
        }

        if (empty($mValue)) {
            return $bReturnsMany ? [] : null;
        }

        // -------------------------------------------------------------------------

        $sWhereType = is_array($mValue) ? 'where_in' : 'where';
        if (!isset($aData[$sWhereType])) {
            $aData[$sWhereType] = [];
        }

        if (strpos($sColumn, '.') !== false) {
            $aData[$sWhereType][] = [$sColumn, $mValue];
        } else {
            $aData[$sWhereType][] = [$this->getTableAlias(true) . $sColumn, $mValue];
        }

        // --------------------------------------------------------------------------

        $sCacheKey = $this->prepareCacheKey($sColumn, $mValue, $aData);
        $aCache    = $this->getCache($sCacheKey);
        if (!empty($aCache)) {
            return $bReturnsMany ? $aCache : reset($aCache);
        }

        // --------------------------------------------------------------------------

        $aResult = $this->getAll($aData);

        // --------------------------------------------------------------------------

        if (empty($aResult)) {
            return $bReturnsMany ? [] : null;
        }

        // --------------------------------------------------------------------------

        foreach ($aResult as $oResult) {
            foreach ($this->aCacheColumns as $sColumn) {
                if (!property_exists($oResult, $sColumn)) {
                    continue;
                }

                if ($sColumn === $this->getColumn('id')) {
                    $this->setCache(
                        $this->prepareCacheKey($sColumn, $oResult->{$sColumn}, $aData),
                        [$oResult]
                    );
                } else {
                    $this->setCacheAlias(
                        $this->prepareCacheKey($sColumn, $oResult->{$sColumn}, $aData),
                        $this->prepareCacheKey($this->getColumn('id'), $oResult->{$this->getColumn('id')}, $aData)
                    );
                }
            }
        }

        // --------------------------------------------------------------------------

        return $bReturnsMany ? $aResult : reset($aResult);
    }

    // --------------------------------------------------------------------------

    /**
     * Formats the key used by the cache, taking into consideration $aData
     *
     * @param string $sColumn The column name
     * @param mixed  $mValue  The value(s)
     * @param array  $aData   The data array
     *
     * @return string
     */
    protected function prepareCacheKey($sColumn, $mValue, array $aData = [])
    {
        /**
         * Remove some elements from the $aData array as they are unlikely to affect the
         * _contents_ of an object, only whether it's returned or not. Things like `expand`
         * will most likely alter a result so leave them in as identifiers.
         */

        $aRemove = [
            'where',
            'or_where',
            'where_in',
            'or_where_in',
            'where_not_in',
            'or_where_not_in',
            'like',
            'or_like',
            'not_like',
            'or_not_like',
        ];
        foreach ($aRemove as $sKey) {
            unset($aData[$sKey]);
        }

        $aKey = array_filter([
            strtoupper($sColumn),
            serialize($mValue),
            $aData !== null ? md5(serialize($aData)) : null,
        ]);

        return implode(':', $aKey);
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by it's ID
     *
     * @param int   $iId   The ID of the object to fetch
     * @param mixed $aData Any data to pass to getCountCommon()
     *
     * @return Resource|null
     * @throws ModelException
     */
    public function getById($iId, array $aData = [])
    {
        if (!$this->getColumn('id')) {
            throw new ModelException(static::class . '::getById() Column variable not set.', 1);
        }

        return $this->getByColumn($this->getColumn('id'), $iId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by their IDs
     *
     * @param array $aIds                An array of IDs to fetch
     * @param mixed $aData               Any data to pass to getCountCommon()
     * @param bool  $bMaintainInputOrder Whether to maintain the input order
     *
     * @return Resource[]
     * @throws ModelException
     */
    public function getByIds($aIds, array $aData = [], $bMaintainInputOrder = false)
    {
        if (!$this->getColumn('id')) {
            throw new ModelException(static::class . '::getByIds() Column variable not set.', 1);
        }

        $aItems = $this->getByColumn($this->getColumn('id'), $aIds, $aData, true);
        if ($bMaintainInputOrder) {
            return $this->sortItemsByColumn($aItems, $aIds, $this->getColumn('id'));
        } else {
            return $aItems;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by it's slug
     *
     * @param string $sSlug The slug of the object to fetch
     * @param array  $aData Any data to pass to getCountCommon()
     *
     * @return Resource|null
     * @throws ModelException
     */
    public function getBySlug($sSlug, array $aData = [])
    {
        if (!$this->getColumn('slug')) {
            throw new ModelException(static::class . '::getBySlug() Column variable not set.', 1);
        }

        return $this->getByColumn($this->getColumn('slug'), $sSlug, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by their slugs
     *
     * @param array $aSlugs              An array of slugs to fetch
     * @param array $aData               Any data to pass to getCountCommon()
     * @param bool  $bMaintainInputOrder Whether to maintain the input order
     *
     * @return Resource[]
     * @throws ModelException
     */
    public function getBySlugs($aSlugs, array $aData = [], $bMaintainInputOrder = false)
    {
        if (!$this->getColumn('slug')) {
            throw new ModelException(static::class . '::getBySlugs() Column variable not set.', 1);
        }

        $aItems = $this->getByColumn($this->getColumn('slug'), $aSlugs, $aData, true);
        if ($bMaintainInputOrder) {
            return $this->sortItemsByColumn($aItems, $aSlugs, $this->getColumn('slug'));
        } else {
            return $aItems;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by it's id or slug
     *
     * Auto-detects whether to use the ID or slug as the selector when fetching
     * an object. Note that this method uses is_numeric() to determine whether
     * an ID or a slug has been passed, thus numeric slugs (which are against
     * Nails style guidelines) will be interpreted incorrectly.
     *
     * @param mixed $mIdSlug The ID or slug of the object to fetch
     * @param array $aData   Any data to pass to getCountCommon()
     *
     * @return Resource|null
     */
    public function getByIdOrSlug($mIdSlug, array $aData = [])
    {
        if (is_numeric($mIdSlug)) {
            return $this->getById($mIdSlug, $aData);
        } else {
            return $this->getBySlug($mIdSlug, $aData);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by its token
     *
     * @param string $sToken The token of the object to fetch
     * @param array  $aData  Any data to pass to getCountCommon()
     *
     * @return Resource|null
     * @throws ModelException if object property tableTokenColumn is not set
     */
    public function getByToken($sToken, array $aData = [])
    {
        if (!$this->getColumn('token')) {
            throw new ModelException(static::class . '::getByToken() Column variable not set.', 1);
        }

        return $this->getByColumn($this->getColumn('token'), $sToken, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by an array of tokens
     *
     * @param array $aTokens             An array of tokens to fetch
     * @param array $aData               Any data to pass to getCountCommon()
     * @param bool  $bMaintainInputOrder Whether to maintain the input order
     *
     * @return Resource[]
     * @throws ModelException if object property tableTokenColumn is not set
     */
    public function getByTokens($aTokens, array $aData = [], $bMaintainInputOrder = false)
    {
        if (!$this->getColumn('token')) {
            throw new ModelException(static::class . '::getByTokens() Column variable not set.', 1);
        }

        $aItems = $this->getByColumn($this->getColumn('token'), $aTokens, $aData, true);
        if ($bMaintainInputOrder) {
            return $this->sortItemsByColumn($aItems, $aTokens, $this->getColumn('token'));
        } else {
            return $aItems;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns items, picked at random
     *
     * @param array $aData  Any data to pass to getCountCommon()
     * @param int   $iLimit The number of items to return
     *
     * @return Resource[]
     * @throws ModelException
     */
    public function getRandom(array $aData = [], int $iLimit = 1): array
    {
        $aData = array_merge(
            $aData,
            [
                'limit' => $iLimit,
                'sort'  => ['*', 'RANDOM'],
            ]
        );

        return $this->getAll($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Sorts items into a specific order based on a specific column
     *
     * @param array  $aItems      The items to sort
     * @param array  $aInputOrder The order to sort them in
     * @param string $sColumn     The column to sort on
     *
     * @return array
     */
    protected function sortItemsByColumn(array $aItems, array $aInputOrder, $sColumn)
    {
        $aOut = [];
        foreach ($aInputOrder as $sInputItem) {
            foreach ($aItems as $oItem) {
                if ($oItem->{$sColumn} == $sInputItem) {
                    $aOut[] = $oItem;
                }
            }
        }
        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Get associated content for the items in the result set where the the relationship is 1 to 1 and the binding
     * is made in the item object (i.e current item contains the associated item's ID)
     *
     * @param array   &$aItems                    The result set of items
     * @param string   $sAssociatedItemIdColumn   Which property in the result set contains the associated content's ID
     * @param string   $sItemProperty             What property of each item to assign the associated content
     * @param string   $sAssociatedModel          The name of the model which handles the associated content
     * @param string   $sAssociatedModelProvider  Which module provides the associated model
     * @param array    $aAssociatedModelData      Data to pass to the associated model's getByIds method()
     * @param bool     $bUnsetOriginalProperty    Whether to remove the original property (i.e the property defined by
     *                                            $sAssociatedItemIdColumn)
     *
     * @return void
     */
    public function getSingleAssociatedItem(
        array &$aItems,
        $sAssociatedItemIdColumn,
        $sItemProperty,
        $sAssociatedModel,
        $sAssociatedModelProvider,
        array $aAssociatedModelData = [],
        $bUnsetOriginalProperty = true
    ) {
        if (!empty($aItems)) {

            $oAssociatedModel   = Factory::model($sAssociatedModel, $sAssociatedModelProvider);
            $aAssociatedItemIds = [];

            foreach ($aItems as $oItem) {

                //  Note the associated item's ID
                $aAssociatedItemIds[] = $oItem->{$sAssociatedItemIdColumn};

                //  Set the base property, only if it's not already set
                if (!property_exists($oItem, $sItemProperty)) {
                    $oItem->{$sItemProperty} = null;
                }
            }

            $aAssociatedItemIds = array_unique($aAssociatedItemIds);
            $aAssociatedItemIds = array_filter($aAssociatedItemIds);
            $aAssociatedItems   = $oAssociatedModel->getByIds($aAssociatedItemIds, $aAssociatedModelData);

            foreach ($aItems as $oItem) {
                foreach ($aAssociatedItems as $oAssociatedItem) {
                    if ($oItem->{$sAssociatedItemIdColumn} == $oAssociatedItem->id) {
                        $oItem->{$sItemProperty} = $oAssociatedItem;
                        break;
                    }
                }

                /**
                 * Unset the original property, but only if it's not the same as the new property,
                 * otherwise we'll remove the property which was just set!
                 */
                if ($bUnsetOriginalProperty && $sAssociatedItemIdColumn !== $sItemProperty) {
                    unset($oItem->{$sAssociatedItemIdColumn});
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get associated content for the items in the result set where the the relationship is 1 to many
     *
     * @param array  &$aItems                   The result set of items
     * @param string  $sItemProperty            What property of each item to assign the associated content
     * @param string  $sAssociatedItemIdColumn  Which property in the associated content which contains the item's ID
     * @param string  $sAssociatedModel         The name of the model which handles the associated content
     * @param string  $sAssociatedModelProvider Which module provides the associated model
     * @param array   $aAssociatedModelData     Data to pass to the associated model's getByIds method()
     *
     * @return void
     */
    protected function getManyAssociatedItems(
        array &$aItems,
        $sItemProperty,
        $sAssociatedItemIdColumn,
        $sAssociatedModel,
        $sAssociatedModelProvider,
        array $aAssociatedModelData = []
    ) {
        if (!empty($aItems)) {

            $oAssociatedModel = Factory::model($sAssociatedModel, $sAssociatedModelProvider);

            $aItemIds = [];
            foreach ($aItems as $oItem) {

                //  Note the ID
                $aItemIds[] = $oItem->id;

                //  Set the base property
                $oItem->{$sItemProperty} = Factory::resource('ExpandableField');
            }

            if (empty($aAssociatedModelData['where_in'])) {
                $aAssociatedModelData['where_in'] = [];
            }

            $aAssociatedModelData['where_in'][] = [
                $oAssociatedModel->getTableAlias() . '.' . $sAssociatedItemIdColumn,
                $aItemIds,
            ];

            $aAssociatedItems = $oAssociatedModel->getAll(null, null, $aAssociatedModelData);

            foreach ($aItems as $oItem) {
                foreach ($aAssociatedItems as $oAssociatedItem) {
                    if ($oItem->id == $oAssociatedItem->{$sAssociatedItemIdColumn}) {
                        $oItem->{$sItemProperty}->data[] = $oAssociatedItem;
                        $oItem->{$sItemProperty}->count++;
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Count associated content for the items in the result set where the the relationship is 1 to many
     *
     * @param array  &$aItems                   The result set of items
     * @param string  $sItemProperty            What property of each item to assign the associated content
     * @param string  $sAssociatedItemIdColumn  Which property in the associated content which contains the item's ID
     * @param string  $sAssociatedModel         The name of the model which handles the associated content
     * @param string  $sAssociatedModelProvider Which module provides the associated model
     * @param array   $aAssociatedModelData     Data to pass to the associated model's getByIds method()
     *
     * @return void
     */
    protected function countManyAssociatedItems(
        array &$aItems,
        $sItemProperty,
        $sAssociatedItemIdColumn,
        $sAssociatedModel,
        $sAssociatedModelProvider,
        array $aAssociatedModelData = []
    ) {
        if (!empty($aItems)) {

            $oAssociatedModel = Factory::model($sAssociatedModel, $sAssociatedModelProvider);

            foreach ($aItems as $oItem) {
                //  Use a new array so as not to impact the original request
                $aQueryData = $aAssociatedModelData;
                if (empty($aQueryData['where'])) {
                    $aQueryData['where'] = [];
                }
                $aQueryData['where'][]   = [$sAssociatedItemIdColumn, $oItem->id];
                $oItem->{$sItemProperty} = $oAssociatedModel->countAll($aQueryData);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get associated content for the items in the result set using a taxonomy table
     *
     * @param array  &$aItems                      The result set of items
     * @param string  $sItemProperty               What property of each item to assign the associated content
     * @param string  $sTaxonomyModel              The name of the model which handles the taxonomy relationships
     * @param string  $sTaxonomyModelProvider      Which module provides the taxonomy model
     * @param string  $sAssociatedModel            The name of the model which handles the associated content
     * @param string  $sAssociatedModelProvider    Which module provides the associated model
     * @param array   $aAssociatedModelData        Data to pass to the associated model's getByIds method()
     * @param string  $sTaxonomyItemIdColumn       The name of the column in the taxonomy table for the item ID
     * @param string  $sTaxonomyAssociatedIdColumn The name of the column in the taxonomy table for the associated ID
     *
     * @return void
     */
    protected function getManyAssociatedItemsWithTaxonomy(
        array &$aItems,
        $sItemProperty,
        $sTaxonomyModel,
        $sTaxonomyModelProvider,
        $sAssociatedModel,
        $sAssociatedModelProvider,
        array $aAssociatedModelData = [],
        $sTaxonomyItemIdColumn = 'item_id',
        $sTaxonomyAssociatedIdColumn = 'associated_id'
    ) {
        if (!empty($aItems)) {

            //  Load the required models
            $oTaxonomyModel   = Factory::model($sTaxonomyModel, $sTaxonomyModelProvider);
            $oAssociatedModel = Factory::model($sAssociatedModel, $sAssociatedModelProvider);

            //  Extract all the item IDs and set the base array for the associated content
            $aItemIds = [];
            foreach ($aItems as $oItem) {

                //  Note the ID
                $aItemIds[] = $oItem->id;

                //  Set the base property
                $oItem->{$sItemProperty} = Factory::resource('ExpandableField');
            }

            //  Get all associations for items in the resultset
            $aTaxonomy = $oTaxonomyModel->getAll(
                null,
                null,
                [
                    'where_in' => [
                        [$sTaxonomyItemIdColumn, $aItemIds],
                    ],
                ]
            );

            if (!empty($aTaxonomy)) {

                //  Extract the IDs of the associated content
                $aAssociatedIds = [];
                foreach ($aTaxonomy as $oTaxonomy) {
                    $aAssociatedIds[] = $oTaxonomy->{$sTaxonomyAssociatedIdColumn};
                }
                $aAssociatedIds = array_unique($aAssociatedIds);

                if (!empty($aAssociatedIds)) {

                    //  Get all associated content
                    $aAssociated = $oAssociatedModel->getByIds($aAssociatedIds, $aAssociatedModelData);

                    if (!empty($aAssociated)) {

                        //  Merge associated content into items
                        foreach ($aAssociated as $oAssociated) {
                            foreach ($aTaxonomy as $oTaxonomy) {
                                if ($oTaxonomy->{$sTaxonomyAssociatedIdColumn} == $oAssociated->id) {
                                    foreach ($aItems as $oItem) {
                                        if ($oTaxonomy->{$sTaxonomyItemIdColumn} == $oItem->id) {
                                            $oItem->{$sItemProperty}->data[] = $oAssociated;
                                            $oItem->{$sItemProperty}->count++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Saves an associated single item
     *
     * @param int    $iItemId                  The ID of the main item
     * @param array  $aAssociatedData          The data to save
     * @param string $sAssociatedItemIdColumn  The name of the ID column in the associated table
     * @param string $sAssociatedModel         The name of the model which is responsible for associated items
     * @param string $sAssociatedModelProvider What module provide the associated item model
     *
     * @return mixed
     * @throws FactoryException
     * @throws ModelException
     */
    protected function saveAssociatedItem(
        int $iItemId,
        array $aAssociatedData,
        string $sAssociatedItemIdColumn,
        string $sAssociatedModel,
        string $sAssociatedModelProvider
    ): bool {

        if (empty($aAssociatedData)) {
            return true;
        }

        /** @var Database $oDb */
        $oDb                  = Factory::service('Database');
        $oAssociatedItemModel = Factory::model($sAssociatedModel, $sAssociatedModelProvider);

        if (array_key_exists($this->getColumn('id'), $aAssociatedData)) {
            $iId = $aAssociatedData[$this->getColumn('id')];
            unset($aAssociatedData[$this->getColumn('id')]);
            $oAssociatedItemModel->update($iId, $aAssociatedData);

        } else {
            $iId = $oAssociatedItemModel->create($aAssociatedData);
        }

        $oDb->set($sAssociatedItemIdColumn, $iId);
        $oDb->where($this->getColumn('id'), $iItemId);
        if (!$oDb->update($this->getTableName())) {
            throw new ModelException(
                'Failed to update associated item (' . $sAssociatedModelProvider . ':' . $sAssociatedModel . ') ' . $oAssociatedItemModel->lastError()
            );
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Save associated items for an object
     *
     * @param int    $iItemId                  The ID of the main item
     * @param array  $aAssociatedItems         The data to save, multi-dimensional array of data
     * @param string $sAssociatedItemIdColumn  The name of the ID column in the associated table
     * @param string $sAssociatedModel         The name of the model which is responsible for associated items
     * @param string $sAssociatedModelProvider What module provide the associated item model
     *
     * @return bool
     * @throws ModelException
     */
    protected function saveAssociatedItems(
        int $iItemId,
        array $aAssociatedItems,
        string $sAssociatedItemIdColumn,
        string $sAssociatedModel,
        string $sAssociatedModelProvider
    ): bool {

        $oAssociatedItemModel = Factory::model($sAssociatedModel, $sAssociatedModelProvider);
        $aTouchedIds          = [];
        $aExistingItemIds     = [];

        //  Get IDs of current items, we'll compare these later to see which ones to delete.
        $aData = [
            'where' => [
                [$oAssociatedItemModel->getTableAlias() . '.' . $sAssociatedItemIdColumn, $iItemId],
            ],
        ];

        $aExistingItems   = $oAssociatedItemModel->getAllFlat($aData);
        $aExistingItemIds = array_keys($aExistingItems);

        // --------------------------------------------------------------------------

        //  Update/insert all known items
        foreach ($aAssociatedItems as $aAssociatedItem) {

            $aAssociatedItem = (array) $aAssociatedItem;

            if (!empty($aAssociatedItem['id'])) {

                //  Safety, no updating of IDs
                $iAssociatedItemId = $aAssociatedItem['id'];
                unset($aAssociatedItem['id']);

                //  Perform update
                if (!$oAssociatedItemModel->update($iAssociatedItemId, $aAssociatedItem)) {
                    throw new ModelException(
                        'Failed to update associated item (' . $sAssociatedModelProvider . ':' . $sAssociatedModel . ') ' . $oAssociatedItemModel->lastError()
                    );
                } else {
                    $aTouchedIds[] = $iAssociatedItemId;
                }

            } else {

                //  Safety, no setting of IDs
                unset($aAssociatedItem['id']);

                //  Ensure the related column is set
                $aAssociatedItem[$sAssociatedItemIdColumn] = $iItemId;

                //  Perform the create
                $iAssociatedItemId = $oAssociatedItemModel->create($aAssociatedItem);
                if (!$iAssociatedItemId) {
                    throw new ModelException(
                        'Failed to create associated item (' . $sAssociatedModelProvider . ':' . $sAssociatedModel . ') ' . $oAssociatedItemModel->lastError()
                    );
                } else {
                    $aTouchedIds[] = $iAssociatedItemId;
                }
            }
        }

        // --------------------------------------------------------------------------

        //  We want to delete items which are no longer in use
        $aIdDiff = array_diff($aExistingItemIds, $aTouchedIds);

        //  Delete those we no longer require
        if (!empty($aIdDiff)) {
            if (!$oAssociatedItemModel->deleteMany($aIdDiff)) {
                throw new ModelException(
                    'Failed to delete old associated items (' . $sAssociatedModelProvider . ':' . $sAssociatedModel . '). ' . $oAssociatedItemModel->lastError()
                );
            }
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Counts all objects
     *
     * @param array $aData           An array of data to pass to getCountCommon()
     * @param bool  $bIncludeDeleted Whether to include deleted objects or not
     *
     * @return int
     * @throws ModelException
     */
    public function countAll(array $aData = [], $bIncludeDeleted = false)
    {
        /** @var Database $oDb */
        $oDb   = Factory::service('Database');
        $table = $this->getTableName(true);

        // --------------------------------------------------------------------------

        if (!empty($aData[static::KEYWORD_PROPERTY])) {
            $this->applySearchConditionals($aData, $aData[static::KEYWORD_PROPERTY]);
        }

        // --------------------------------------------------------------------------

        //  Apply common items
        $this->getCountCommon($aData);

        // --------------------------------------------------------------------------

        //  If non-destructive delete is enabled then apply the delete query
        if (!$this->isDestructiveDelete() && !$bIncludeDeleted) {
            $oDb->where($this->getTableAlias(true) . $this->getColumn('is_deleted'), false);
        }

        // --------------------------------------------------------------------------

        return $oDb->count_all_results($table);
    }

    // --------------------------------------------------------------------------

    /**
     * Searches for objects, optionally paginated.
     *
     * @param string   $sKeywords        The search term
     * @param int|null $iPage            The page number of the results, if null then no pagination
     * @param int|null $iPerPage         How many items per page of paginated results
     * @param mixed    $aData            Any data to pass to getCountCommon()
     * @param bool     $bIncludeDeleted  If non-destructive delete is enabled then this flag allows you to include
     *                                   deleted items
     *
     * @return \stdClass
     */
    public function search($sKeywords, $iPage = null, $iPerPage = null, array $aData = [], $bIncludeDeleted = false)
    {
        //  If the second parameter is an array then treat as if called with search($sKeywords, null, null, $aData);
        if (is_array($iPage)) {
            $aData = $iPage;
            $iPage = null;
        }

        $aData[static::KEYWORD_PROPERTY] = $sKeywords;

        return (object) [
            'page'    => $iPage,
            'perPage' => $iPerPage,
            'total'   => $this->countAll($aData),
            'data'    => $this->getAll($iPage, $iPerPage, $aData, $bIncludeDeleted),
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Mutates the data array and adds the conditionals for searching
     *
     * @param array $aData
     * @param       $sKeywords
     */
    protected function applySearchConditionals(array &$aData, $sKeywords)
    {
        if (empty($aData['or_like'])) {
            $aData['or_like'] = [];
        }

        $sAlias = $this->getTableAlias(true);

        foreach ($this->searchableFields as $mField) {

            //  If the field is an array then search across the columns concatenated together
            if (is_array($mField)) {

                $sMappedFields = array_map(function ($sInput) use ($sAlias) {
                    if (strpos($sInput, '.') !== false || preg_match('/^\(.*\)$/', $sInput)) {
                        return $sInput;
                    } else {
                        return $sAlias . $sInput;
                    }
                }, $mField);

                $aData['or_like'][] = ['CONCAT_WS(" ", ' . implode(',', $sMappedFields) . ')', $sKeywords];

            } elseif (preg_match('/^\(.*\)$/', $mField)) {
                $aData['or_like'][] = [$mField, $sKeywords];

            } elseif (strpos($mField, '.') !== false) {
                $aData['or_like'][] = [$mField, $sKeywords];

            } else {
                $aData['or_like'][] = [$sAlias . $mField, $sKeywords];
            }
        }
    }

    /**
     * --------------------------------------------------------------------------
     *
     * HELPER METHODS
     * These methods provide additional functionality to models
     *
     * --------------------------------------------------------------------------
     */

    /**
     * Generates a unique slug
     *
     * @param array $aData     The data to use when generating a slug
     * @param int   $iIgnoreId The ID of an item to ignore
     *
     * @return string
     * @throws ModelException
     */
    protected function generateSlug(array $aData = [], int $iIgnoreId = null)
    {
        $sSlug    = $this->generateSlugBase($aData);
        $iCounter = 0;

        do {

            $sSlugTest = $iCounter
                ? $sSlug . '-' . $iCounter
                : $sSlug;

            $iCounter++;

        } while (!$this->isValidSlug($sSlugTest, $iIgnoreId, $aData));

        return $sSlugTest;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the slug's base, compiled from the label
     *
     * @param string $sLabel The item's label
     * @param string $sKey   The key to use from the supplied data array
     *
     * @return string
     */
    protected function generateSlugBase(array $aData, string $sKey = null): string
    {
        $sKey = $sKey ?? $this->getColumn('label');

        if (!array_key_exists($sKey, $aData)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Key `%s` is required when generating slugs',
                    $sKey
                )
            );
        }

        return Transliterator::transliterate(
            getFromArray($sKey, $aData)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * checks whether a slug is valid
     *
     * @param string   $sSlug     The slug to check
     * @param int|null $iIgnoreId An ID to ignore when checking
     * @param array    $aData     Any data to pass to generateSlugHook()
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function isValidSlug(string $sSlug, int $iIgnoreId = null, array $aData = []): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        if ($iIgnoreId) {
            $oDb->where($this->getColumn('id') . ' !=', $iIgnoreId);
        }

        $oDb->where($this->getColumn('slug'), $sSlug);
        $this->generateSlugHook($aData);

        return $oDb->count_all_results($this->getTableName()) === 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Provides a hook for the extending model to manipulate the query
     *
     * @param array $aData
     */
    protected function generateSlugHook(array $aData = []): void
    {
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a unique token for a record
     *
     * @param string|null $sMask   The token mask, defaults to $this->getTokenMask()
     * @param string|null $sTable  The table to use defaults to $this->getTableName()
     * @param string|null $sColumn The column to use, defaults to $this->getColumn('token')
     *
     * @return string
     * @throws ModelException
     */
    protected function generateToken(string $sMask = null, string $sTable = null, string $sColumn = null): string
    {
        $sMask   = $sMask ?? $this->getTokenMask();
        $sTable  = $sTable ?? $this->getTableName();
        $sColumn = $sColumn ?? $this->getColumn('token');

        if (!$sColumn) {
            throw new ModelException(
                sprintf(
                    '%s::generateToken() Token variable not set',
                    static::class
                )
            );
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        do {

            $sToken = generateToken($sMask);
            $oDb->where($sColumn, $sToken);

        } while ($oDb->count_all_results($sTable));

        return $sToken;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the token mask to use
     *
     * @return string|null
     */
    protected function getTokenMask(): ?string
    {
        return static::TOKEN_MASK ?? $this->sTokenMask;
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param object $oObj      A reference to the object being formatted.
     * @param array  $aData     The same data array which is passed to _getCountCommon, for reference if needed
     * @param array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param array  $aBools    Fields which should be cast as booleans if not null
     * @param array  $aFloats   Fields which should be cast as floats if not null
     *
     * @return void
     */
    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
    ) {
        //  @todo (Pablo - 2019-11-28) - Allow these to be passed in as arguments
        $aDates     = [];
        $aDateTimes = [];

        /** @var Field[] $aFields */
        $aFields = $this->describeFields();
        foreach ($aFields as $oField) {
            switch ($oField->type) {
                case Helper\Form::FIELD_NUMBER:
                    $aIntegers[] = $oField->key;
                    break;
                case Helper\Form::FIELD_BOOLEAN:
                    $aBools[] = $oField->key;
                    break;
                case Helper\Form::FIELD_DATE:
                    $aDates[] = $oField->key;
                    break;
                case Helper\Form::FIELD_DATETIME:
                    $aDateTimes[] = $oField->key;
                    break;
            }
        }

        // --------------------------------------------------------------------------

        foreach ($aIntegers as $sProperty) {
            if (property_exists($oObj, $sProperty)) {
                if (is_numeric($oObj->{$sProperty}) && !is_null($oObj->{$sProperty})) {
                    $oObj->{$sProperty} = (int) $oObj->{$sProperty};
                }
            }
        }

        foreach ($aBools as $sProperty) {
            if (property_exists($oObj, $sProperty)) {
                if (!is_null($oObj->{$sProperty})) {
                    $oObj->{$sProperty} = (bool) $oObj->{$sProperty};
                }
            }
        }

        foreach ($aFloats as $sProperty) {
            if (property_exists($oObj, $sProperty)) {
                if (is_numeric($oObj->{$sProperty}) && !is_null($oObj->{$sProperty})) {
                    $oObj->{$sProperty} = (float) $oObj->{$sProperty};
                }
            }
        }

        foreach ($aDateTimes as $sProperty) {
            if (property_exists($oObj, $sProperty) && !is_null($oObj->{$sProperty})) {
                $oObj->{$sProperty} = Factory::resource('DateTime', null, ['raw' => $oObj->{$sProperty}]);
            }
        }

        foreach ($aDates as $sProperty) {
            if (property_exists($oObj, $sProperty) && !is_null($oObj->{$sProperty})) {
                $oObj->{$sProperty} = Factory::resource('Date', null, ['raw' => $oObj->{$sProperty}]);
            }
        }

        // --------------------------------------------------------------------------

        //  Convert to a resource
        $oObj = Factory::resource(static::RESOURCE_NAME, static::RESOURCE_PROVIDER, $oObj);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $table
     *
     * @param bool $bIncludeAlias Whether to include the table's alias
     *
     * @throws ModelException
     * @return string
     */
    public function getTableName($bIncludeAlias = false)
    {
        //  @todo (Pablo - 2019-03-14) - Phase out support for $this->table
        if (empty($this->table) && empty(static::TABLE)) {
            throw new ModelException(static::class . '::TABLE not set', 1);
        }

        $sTable = static::TABLE ?? $this->table;

        return $bIncludeAlias ? trim($sTable . ' as `' . $this->getTableAlias() . '`') : $sTable;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $tableAlias
     *
     * @param bool $bIncludeSeparator Whether to include the prefix separator
     *
     * @return string
     * @throws ModelException
     */
    public function getTableAlias($bIncludeSeparator = false)
    {
        //  @todo (Pablo - 2019-04-15) - Deprecate $this->tableAlias
        $sOut = static::TABLE_ALIAS ?? $this->tableAlias ?? '';

        if (empty($sOut)) {

            $sTable = strtolower($this->getTableName());
            $sTable = preg_replace('/[^a-z_]/', '', $sTable);
            $sTable = preg_replace('/_/', ' ', $sTable);
            $aTable = explode(' ', $sTable);

            foreach ($aTable as $sWord) {
                $sOut .= $sWord[0];
            }
        }

        return !empty($sOut) && $bIncludeSeparator
            ? $sOut . '.'
            : $sOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default sort column
     *
     * @return string|null
     */
    public function getDefaultSortColumn(): ?string
    {
        if (classUses(static::class, Sortable::class)) {
            $sColumn = $this->getSortableColumn();
        } else {
            $sColumn = static::DEFAULT_SORT_COLUMN ?? $this->defaultSortColumn;
        }

        if (empty($sColumn)) {
            return null;
        }

        $sColumn = $this->getColumn($sColumn);

        if ($sColumn && strpos($sColumn, '.') === false) {
            $sColumn = $this->getTableAlias(true) . $sColumn;
        }

        return $sColumn;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the default sort order
     *
     * @return string
     */
    public function getDefaultSortOrder(): string
    {
        return static::DEFAULT_SORT_ORDER ?? $this->defaultSortOrder ?? static::SORT_ASC;
    }

    // --------------------------------------------------------------------------

    /**
     * Define expandable objects
     *
     * @param array $aOptions An array describing the expandable field
     *
     * @return $this
     * @throws ModelException
     */
    protected function addExpandableField(array $aOptions): self
    {
        //  Validation
        if (!array_key_exists('trigger', $aOptions)) {
            throw new ModelException('Expandable fields must define a "trigger".');
        }

        if (!array_key_exists('type', $aOptions)) {
            $aOptions['type'] = static::EXPANDABLE_TYPE_SINGLE;
        }

        if (!array_key_exists('model', $aOptions)) {
            throw new ModelException('Expandable fields must define a "model".');
        }

        if (!array_key_exists('id_column', $aOptions)) {
            throw new ModelException('Expandable fields must define a "id_column".');
        }

        //  Optional elements
        if (!array_key_exists('property', $aOptions)) {
            $aOptions['property'] = $aOptions['trigger'];
        }

        if (!array_key_exists('provider', $aOptions)) {
            $aOptions['provider'] = 'app';
        }

        $this->aExpandableFields[] = (object) [

            //  The text which triggers this expansion, passed in via $aData['expand']
            'trigger'     => $aOptions['trigger'],

            //  The type of expansion: single or many
            //  This must be one of static::EXPAND_TYPE_SINGLE or static::EXPAND_TYPE_MANY
            'type'        => $aOptions['type'],

            //  What property to assign the results of the expansion to
            'property'    => $aOptions['property'],

            //  Which model to use for the expansion
            'model'       => $aOptions['model'],

            //  The provider of the model
            'provider'    => $aOptions['provider'],

            //  Any data to pass to the getAll (every time)
            'data'        => Helper\ArrayHelper::getFromArray('data', $aOptions, []),

            /**
             * The ID column to use; for EXPANDABLE_TYPE_SINGLE this is property of the
             * parent object which contains the ID, for EXPANDABLE_TYPE_MANY, this is the
             * property of the child object which contains the parent's ID.
             */
            'id_column'   => $aOptions['id_column'],

            //  Whether the field is expanded by default
            'auto_expand' => Helper\ArrayHelper::getFromArray('auto_expand', $aOptions, false),

            //  Whether to automatically save expanded objects when the trigger is
            //  passed as a key to the create or update methods
            'auto_save'   => array_key_exists('auto_save', $aOptions) ? !empty($aOptions['auto_save']) : true,
        ];

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method for defining on-to-one relationships
     *
     * @param string      $sTrigger     The trigger word
     * @param string      $sModel       The related model
     * @param string      $sProvider    The provider of the related model
     * @param string|null $sLocalColumn The local column contianing the foreign ID
     * @param array       $aData        Any data to bind to the expandable field
     *
     * @return $this
     * @throws ModelException
     */
    protected function hasOne(
        string $sTrigger,
        string $sModel,
        string $sProvider = 'app',
        string $sLocalColumn = null,
        array $aData = []
    ): self {
        return $this
            ->addExpandableField([
                'type'      => static::EXPANDABLE_TYPE_SINGLE,
                'trigger'   => $sTrigger,
                'model'     => $sModel,
                'provider'  => $sProvider,
                'id_column' => $sLocalColumn ?? sprintf('%s_id', $sTrigger),
                'data'      => $aData,
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Utility method for defining ont-to-many relationships
     *
     * @param string $sTrigger       The trigger word
     * @param string $sModel         The related model
     * @param string $sForeignColumn The foreign column contianing the local ID
     * @param string $sProvider      The provider of the related model
     * @param array  $aData          Any data to bind to the expandable field
     *
     * @return $this
     * @throws ModelException
     */
    protected function hasMany(
        string $sTrigger,
        string $sModel,
        string $sForeignColumn,
        string $sProvider = 'app',
        array $aData = []
    ): self {
        return $this
            ->addExpandableField([
                'type'      => static::EXPANDABLE_TYPE_MANY,
                'trigger'   => $sTrigger,
                'model'     => $sModel,
                'provider'  => $sProvider,
                'id_column' => $sForeignColumn,
                'data'      => $aData,
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the configured expandable fields
     *
     * @returns array
     */
    public function getExpandableFields()
    {
        return $this->aExpandableFields;
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether a string is an expandable field trigger
     *
     * @param string $sTrigger
     *
     * @return bool
     */
    public function isExpandableFieldTrigger(string $sTrigger): bool
    {
        $aTriggers = arrayExtractProperty($this->getExpandableFields(), 'trigger');
        return array_search($sTrigger, $aTriggers) !== false;
    }

    // --------------------------------------------------------------------------

    /**
     * Extracts any autosaveable expandable fields and unsets them from the main array
     *
     * @param array $aData The data passed to create() or update()
     *
     * @return array
     */
    protected function autoSaveExpandableFieldsExtract(array &$aData)
    {
        $aFields           = [];
        $aOut              = [];
        $aExpandableFields = $this->getExpandableFields();

        foreach ($aExpandableFields as $oField) {
            if ($oField->auto_save) {
                $aFields[$oField->trigger] = $oField;
            }
        }

        foreach ($aData as $sKey => $mValue) {
            if (array_key_exists($sKey, $aFields)) {

                /**
                 * For single type expandable fields, we only want to auto-save using the related
                 * model if  the item being passed is an object or an array (i.e data to create/update
                 * the related item with) – if it's anything else (typically a numeric) we can conider
                 * this an ID and update the current tabke rather than the target, related, table.
                 */
                if (
                    $aFields[$sKey]->type === static::EXPANDABLE_TYPE_SINGLE
                    && !is_object($mValue)
                    && !is_array($mValue)
                ) {
                    continue;
                }

                $aOut[$sKey]       = $aFields[$sKey];
                $aOut[$sKey]->data = $mValue;
                unset($aData[$sKey]);
            }
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Saves extracted expandable fields
     *
     * @param int   $iId
     * @param array $aExpandableFields
     *
     * @return $this
     */
    protected function autoSaveExpandableFieldsSave($iId, array $aExpandableFields): Base
    {
        foreach ($aExpandableFields as $oField) {

            $aData = array_filter((array) $oField->data);

            if ($oField->type === static::EXPANDABLE_TYPE_MANY) {
                $this->saveAssociatedItems(
                    $iId,
                    $aData,
                    $oField->id_column,
                    $oField->model,
                    $oField->provider
                );

            } elseif ($oField->type === static::EXPANDABLE_TYPE_SINGLE) {
                $this->saveAssociatedItem(
                    $iId,
                    $aData,
                    $oField->id_column,
                    $oField->model,
                    $oField->provider
                );
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether this model deletes destructively or not.
     *
     * @return bool
     */
    public function isDestructiveDelete()
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->destructiveDelete
        return $this->destructiveDelete ?? static::DESTRUCTIVE_DELETE;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether this model automatically sets created/modified users or not
     *
     * @return bool
     */
    public function isAutoSetUsers()
    {
        return static::AUTO_SET_USER
            && function_exists('activeUser')
            && function_exists('isLoggedIn');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether this model automatically generates slugs or not
     *
     * @return bool
     */
    public function isAutoSetSlugs()
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->tableAutoSetSlugs
        return $this->tableAutoSetSlugs ?? static::AUTO_SET_SLUG;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether this model automatically generates tokens or not
     *
     * @return bool
     */
    public function isAutoSetTokens()
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->tableAutoSetTokens
        return $this->tableAutoSetTokens ?? static::AUTO_SET_TOKEN;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column name for specific columns of interest
     *
     * @param string      $sColumn  The column to query
     * @param string|null $sDefault The default value if not defined
     *
     * @return string|null
     */
    public function getColumn(string $sColumn, string $sDefault = null): ?string
    {
        $sProperty = sprintf(
            'table%sColumn',
            ucfirst(underscoreToCamelcase(trim($sColumn)))
        );

        return property_exists($this, $sProperty)
            ? $this->{$sProperty}
            : ($sDefault ?? $sColumn);
    }

    // --------------------------------------------------------------------------

    /**
     * Describes the fields for this model automatically and with some guesswork;
     * for more fine grained control models should overload this method.
     *
     * @param string|null $sTable The database table to query
     *
     * @return Field[]
     */
    public function describeFields($sTable = null)
    {
        //  @todo (Pablo - 2019-05-09) - This doesn't feel right
        $sTable    = $sTable ?: $this->getTableName();
        $sCacheKey = 'MODEL-FIELDS:' . $sTable;
        $aCache    = $this->getCache($sCacheKey);
        if (!empty($aCache)) {
            return $aCache;
        }

        // --------------------------------------------------------------------------

        /** @var Database $oDb */
        $oDb     = Factory::service('Database');
        $aResult = $oDb->query('DESCRIBE `' . $sTable . '`;')->result();
        $aFields = [];

        if (classUses($this, Localised::class)) {

            /** @var Locale $oLocale */
            $oLocale           = Factory::service('Locale');
            $aSupportedLocales = $oLocale->getSupportedLocales();
            $aOptions          = array_combine(
                $aSupportedLocales,
                array_map(function (\Nails\Common\Factory\Locale $oLocale) {
                    return $oLocale->getDisplayLanguage();
                }, $aSupportedLocales)
            );

            /** @var Field $oTemp */
            $oTemp = Factory::factory('ModelField');
            $oTemp
                ->setKey('locale')
                ->setLabel('Locale')
                ->setType(Helper\Form::FIELD_DROPDOWN)
                ->setAllowNull(false)
                ->setValidation([
                    FormValidation::RULE_REQUIRED,
                    FormValidation::RULE_SUPPORTED_LOCALE,
                ])
                ->setOptions($aOptions)
                ->setClass('select2')
                ->setInfo('This field specifies what language the item is written in.')
                ->setDefault($oLocale->getDefautLocale());

            $aFields['locale'] = $oTemp;
        }

        foreach ($aResult as $oField) {

            /** @var Field $oTemp */
            $oTemp = Factory::factory('ModelField');
            $oTemp
                ->setKey($oField->Field)
                ->setLabel($this->describeFieldsPrepareLabel($oField->Field))
                ->setAllowNull($oField->Null === 'YES');

            $this
                ->describeFieldsGuessType($oTemp, $oField)
                ->describeFieldsGuessValidation($oTemp, $oField);

            $aFields[$oTemp->key] = $oTemp;
        }

        if (classUses($this, Localised::class)) {
            unset($aFields['language']);
            unset($aFields['region']);
        }

        $this->setCache($sCacheKey, $aFields);

        return $aFields;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a human friendly label from the field's key
     *
     * @param string $sLabel The label to format
     *
     * @return string
     */
    protected function describeFieldsPrepareLabel(string $sLabel): string
    {
        $aPatterns = [
            //  Common words
            '/\bid\b/i'   => 'ID',
            '/\burl\b/i'  => 'URL',
            '/\bhtml\b/i' => 'HTML',
            //  Common file extensions
            '/\bpdf\b/i'  => 'PDF',
        ];

        $sLabel = ucwords(preg_replace('/[\-_]/', ' ', $sLabel));
        $sLabel = preg_replace(array_keys($aPatterns), array_values($aPatterns), $sLabel);

        return $sLabel;
    }

    // --------------------------------------------------------------------------

    /**
     * Guesses the field's type and sets it accordingly
     *
     * @param Field     $oField   The field object
     * @param \stdClass $oDbField The database field
     *
     * @return $this
     */
    protected function describeFieldsGuessType(Field &$oField, \stdClass $oDbField): self
    {
        preg_match('/^(.*?)(\((.+?)\)(.*))?$/', $oDbField->Type, $aMatches);

        $sType       = Helper\ArrayHelper::getFromArray(1, $aMatches, 'text');
        $sTypeConfig = trim(Helper\ArrayHelper::getFromArray(3, $aMatches));
        $iLength     = is_numeric($sTypeConfig) ? (int) $sTypeConfig : null;

        switch ($sType) {

            /**
             * Numeric
             */
            case 'int':
            case 'int unsigned':
            case 'mediumint':
            case 'mediumint unsigned':
            case 'bigint':
            case 'bigint unsigned':
                //  @todo (Pablo - 2019-11-28) - This is only number to match the form type, and could be misleading
                $oField
                    ->setType(Helper\Form::FIELD_NUMBER);
                break;

            /**
             * Boolean
             */
            //  @todo (Pablo 02/11/2020) - Consider whether the asumption of tinyin == boolean is too much of a gotcha
            case 'tinyint':
            case 'tinyint unsigned':
            case 'bool':
            case 'boolean':
            case 'bit':
                $oField
                    ->setType(Helper\Form::FIELD_BOOLEAN);
                break;

            /**
             * String
             */
            case 'varchar':
                $oField
                    ->setType(Helper\Form::FIELD_TEXT)
                    ->setMaxLength($iLength ?: null);
                break;
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                $oField
                    ->setType(Helper\Form::FIELD_TEXTAREA);
                break;

            /**
             * Date and time
             */
            case 'date':
                $oField
                    ->setType(Helper\Form::FIELD_DATE);
                break;
            case 'datetime':
                $oField
                    ->setType(Helper\Form::FIELD_DATETIME);
                break;
            case 'time':
                $oField
                    ->setType(Helper\Form::FIELD_TIME);
                break;

            /**
             * ENUM
             */
            case 'enum':
            case 'set':
                if ($sType === 'enum') {
                    $oField
                        ->setType(Helper\Form::FIELD_DROPDOWN);
                } else {
                    $oField->key .= '[]';
                    $oField
                        ->setType(Helper\Form::FIELD_DROPDOWN_MULTIPLE);
                }
                $aOptions = explode("','", substr($sTypeConfig, 1, -1));
                $oField
                    ->setOptions(array_combine($aOptions, $aOptions))
                    ->setClass('select2');;
                break;

            /**
             * Default to basic string
             */
            default:
                $oField
                    ->setType(Helper\Form::FIELD_TEXT)
                    ->setMaxLength($iLength ?: null);
                break;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Guesses the field's validation rules based on it's type
     *
     * @param Field     $oField   The field object
     * @param \stdClass $oDbField The database field
     *
     * @return $this
     */
    protected function describeFieldsGuessValidation(Field &$oField, \stdClass $oDbField): self
    {
        preg_match('/^(.*?)(\((.+?)\)(.*))?$/', $oDbField->Type, $aMatches);

        $sType   = Helper\ArrayHelper::getFromArray(1, $aMatches, 'text');
        $iLength = Helper\ArrayHelper::getFromArray(3, $aMatches);
        $sExtra  = trim(strtolower(Helper\ArrayHelper::getFromArray(4, $aMatches)));

        switch ($sType) {

            /**
             * Numeric
             */
            case 'int':
                $oField
                    ->addValidation(FormValidation::RULE_INTEGER);
                break;

            case 'int unsigned':
                $oField
                    ->addValidation(FormValidation::RULE_INTEGER)
                    ->addValidation(sprintf('%s[%s]', FormValidation::RULE_GREATER_THAN, -1));
                break;

            case 'tinyint':
                if ($oField->type === 'boolean') {
                    $oField
                        ->addValidation(FormValidation::RULE_IS_BOOL);
                } else {
                    $oField
                        ->addValidation(FormValidation::RULE_INTEGER);
                }
                break;

            /**
             * String
             */
            case 'varchar':
                if ($iLength) {
                    //  @todo (Pablo - 2019-12-18) - Use FormValidation::rule when CI is no longer a dependency
                    $oField
                        ->addValidation(sprintf('%s[%s]', FormValidation::RULE_MAX_LENGTH, $iLength));
                }
                break;

            /**
             * Date and time
             */
            case 'date':
                $oField
                    ->addValidation(FormValidation::RULE_VALID_DATE);
                break;

            case 'datetime':
                $oField
                    ->addValidation(FormValidation::RULE_VALID_DATETIME);
                break;

            /**
             * ENUM
             */
            case 'enum':
            case 'set':
                //  @todo (Pablo - 2019-12-18) - Use FormValidation::rule when CI is no longer a dependency
                $oField
                    ->addValidation(
                        sprintf(
                            '%s[%s]',
                            FormValidation::RULE_IN_LIST,
                            implode(',', array_keys($oField->options))
                        )
                    );
                break;

            /**
             * Default to basic string
             */
            default:
                if ($iLength) {
                    //  @todo (Pablo - 2019-12-18) - Use FormValidation::rule when CI is no longer a dependency
                    $oField
                        ->addValidation(
                            sprintf(
                                '%s[%s]',
                                FormValidation::RULE_MAX_LENGTH,
                                $iLength
                            )
                        );
                }
                break;
        }

        if ($this->isAutoSetSlugs() && $oField->key == $this->getColumn('label')) {
            $oField
                ->addValidation(FormValidation::RULE_REQUIRED);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns any sensitive fields
     *
     * @return string[]
     */
    public function sensitiveFields(): array
    {
        return static::SENSITIVE_FIELDS;
    }

    // --------------------------------------------------------------------------

    /**
     * Genenrates the event namespace for this class
     *
     * @return string
     * @throws \ReflectionException
     */
    public static function getEventNamespace(): string
    {
        return Components::detectClassComponent(static::class)->slug . ':' . static::class;
    }

    // --------------------------------------------------------------------------

    /**
     * Triggers an event
     *
     * @param string $sEvent The event to trigger
     * @param array  $aData  Data to pass to listeners
     *
     * @throws ModelException
     */
    protected function triggerEvent($sEvent, array $aData): Base
    {
        if ($sEvent) {
            /** @var Event $oEvent */
            $oEvent = Factory::service('Event');
            $oEvent
                ->trigger(
                    $sEvent,
                    static::getEventNamespace(),
                    $aData
                );
        }

        return $this;
    }
}
