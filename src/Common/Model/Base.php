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
use Nails\Common\Factory\Model\Field;
use Nails\Common\Helper\ArrayHelper;
use Nails\Common\Helper\Form;
use Nails\Common\Resource;
use Nails\Common\Service\FormValidation;
use Nails\Common\Service\Locale;
use Nails\Common\Traits\Caching;
use Nails\Common\Traits\ErrorHandling;
use Nails\Common\Traits\GetCountCommon;
use Nails\Common\Traits\Model\Localised;
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
    protected $tableDeletedColumn = 'is_deleted';

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
     * Override the default token mask when automatically generating tokens for items
     *
     * @var string
     */
    protected $sTokenMask;

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
     * The default column to use for sorting items
     *
     * @var string|null
     */
    protected $defaultSortColumn;

    /**
     * The default sort order
     *
     * @var string
     */
    protected $defaultSortOrder = 'ASC';

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
     * Whether to automatically set created/modified timestamps
     *
     * @var bool|null
     * @deprecated Use constant AUTO_SET_TIMESTAMP instead
     */
    protected $tableAutoSetTimestamps;

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
     * --------------------------------------------------------------------------
     * CONSTRUCTOR && DESTRUCTOR
     * The constructor preps common variables and sets the model up for user.
     * The destructor clears
     * --------------------------------------------------------------------------
     */

    /**
     * Base constructor.
     */
    public function __construct()
    {
        $this->clearErrors();

        if (classUses(static::class, 'Nails\Common\Traits\Model\Sortable')) {
            $this->defaultSortColumn = $this->getSortableColumn();
        }

        $this->aCacheColumns = [
            $this->tableIdColumn,
            $this->tableSlugColumn,
            $this->tableTokenColumn,
        ];

        /**
         * Set up default searchable fields. Each field is passed directly to the
         * `column` parameter in getCountCommon() so can be in any form accepted by that.
         *
         * @todo  allow some sort of cleansing callback so that models can prep the
         * search string if needed.
         */
        $this->searchableFields[] = $this->tableIdColumn;
        $this->searchableFields[] = $this->tableLabelColumn;
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
        $this->triggerEvent(static::EVENT_CREATING, [$aData]);

        $oDb    = Factory::service('Database');
        $sTable = $this->getTableName();

        // --------------------------------------------------------------------------

        //  Execute the data pre-write hooks
        $this
            ->prepareCreateData($aData)
            ->prepareWriteData($aData);

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

            $this->autoSaveExpandableFieldsSave($iId, $aAutoSaveExpandableFields);
            $this->triggerEvent(static::EVENT_CREATED, [$iId]);

            return $bReturnObject ? $this->getById($iId) : $iId;

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
        $oDb = Factory::service('Database');
        try {
            $oDb->trans_begin();
            foreach ($aData as $aDatum) {
                if (!$this->create($aDatum)) {
                    throw new ModelException('Failed to create item with data: ' . json_encode($aDatum));
                }
            }
            $oDb->trans_commit();
            return true;
        } catch (\Exception $e) {
            $oDb->trans_rollback();
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
        $this->triggerEvent(static::EVENT_UPDATING, [$aData]);

        $sAlias = $this->getTableAlias(true);
        $sTable = $this->getTableName(true);
        $oDb    = Factory::service('Database');

        // --------------------------------------------------------------------------

        //  Execute the data pre-write hooks
        $this
            ->prepareUpdateData($aData)
            ->prepareWriteData($aData);

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
            $this->autoSaveExpandableFieldsSave($iId, $aAutoSaveExpandableFields);

            //  Clear the cache
            foreach ($this->aCacheColumns as $sColumn) {
                $sKey = strtoupper($sColumn) . ':' . json_encode($iId);
                $this->unsetCachePrefix($sKey);
            }

            $this->triggerEvent(static::EVENT_UPDATED, [$iId]);

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
        $oDb = Factory::service('Database');
        try {
            $oDb->trans_begin();
            foreach ($aIds as $iId) {
                if (!$this->update($iId, $aData)) {
                    throw new ModelException('Failed to update item with ID ' . $iId);
                }
            }
            $oDb->trans_commit();
            return true;
        } catch (\Exception $e) {
            $oDb->trans_rollback();
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
     * Sets the timestamps on the $aData array
     *
     * @param array $aData       The data being passed to the model
     * @param bool  $bSetCreated Whether to set the created timestamp or not
     *
     * @return $this
     * @throws FactoryException
     */
    protected function setDataTimestamps(array &$aData, bool $bSetCreated = true): Base
    {
        if ($this->isAutoSetTimestamps()) {

            $oDate = Factory::factory('DateTime');

            if ($bSetCreated && empty($aData[$this->tableCreatedColumn])) {
                $aData[$this->tableCreatedColumn] = $oDate->format('Y-m-d H:i:s');
            }
            if (empty($aData[$this->tableModifiedColumn])) {
                $aData[$this->tableModifiedColumn] = $oDate->format('Y-m-d H:i:s');
            }
        }

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
                if ($bSetCreated && empty($aData[$this->tableCreatedByColumn])) {
                    $aData[$this->tableCreatedByColumn] = activeUser('id');
                }
                if (empty($aData[$this->tableModifiedByColumn])) {
                    $aData[$this->tableModifiedByColumn] = activeUser('id');
                }
            } else {
                if ($bSetCreated && empty($aData[$this->tableCreatedByColumn])) {
                    $aData[$this->tableCreatedByColumn] = null;
                }
                if (empty($aData[$this->tableModifiedByColumn])) {
                    $aData[$this->tableModifiedByColumn] = null;
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
            empty($aData[$this->tableSlugColumn]) &&
            ($bIsCreate || !static::AUTO_SET_SLUG_IMMUTABLE)
        ) {

            if (empty($this->tableSlugColumn)) {
                throw new ModelException(static::class . '::create() Slug column variable not set', 1);
            }

            if (empty($this->tableLabelColumn)) {
                throw new ModelException(static::class . '::create() Label column variable not set', 1);
            }

            if (empty($aData[$this->tableLabelColumn])) {
                throw new ModelException(
                    static::class . '::create() "' . $this->tableLabelColumn .
                    '" is required when automatically generating slugs.',
                    1
                );
            }

            $aData[$this->tableSlugColumn] = $this->generateSlug(
                $aData[$this->tableLabelColumn],
                $iIgnoreId,
                $aData
            );
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
        if ($bIsCreate && $this->isAutoSetTokens() && empty($aData[$this->tableTokenColumn])) {
            if (empty($this->tableTokenColumn)) {
                throw new ModelException(static::class . '::create() Token column variable not set', 1);
            }
            $aData[$this->tableTokenColumn] = $this->generateToken();
        } elseif (!$bIsCreate) {
            //  Automatically set tokens are permanent and immutable
            if ($this->isAutoSetTokens() && !empty($aData[$this->tableTokenColumn])) {
                unset($aData[$this->tableTokenColumn]);
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
     * $this->tableDeletedColumn field will be set to true.
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

        $this->triggerEvent(static::EVENT_DELETING, [$oItem]);

        if ($this->isDestructiveDelete()) {
            $bResult = $this->destroy($iId);
        } else {
            $bResult = $this->update($iId, [$this->tableDeletedColumn => true]);
        }

        if ($bResult) {
            $this->triggerEvent(static::EVENT_DELETED, [$iId, $oItem]);
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

        $oDb = Factory::service('Database');
        try {
            $oDb->trans_begin();
            foreach ($aIds as $iId) {
                if (!$this->delete($iId)) {
                    throw new ModelException('Failed to delete item with ID ' . $iId . '. ' . $this->lastError());
                }
            }
            $oDb->trans_commit();
            return true;
        } catch (\Exception $e) {
            $oDb->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unmarks an object as deleted
     *
     * If destructive deletion is enabled then this method will return null.
     * If Non-destructive deletion is enabled then the $this->tableDeletedColumn
     * field will be set to false.
     *
     * @param int $iId The ID of the object to restore
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function restore($iId): bool
    {
        $this->triggerEvent(static::EVENT_RESTORING, [$iId]);

        if ($this->isDestructiveDelete()) {
            return null;
        } elseif ($this->update($iId, [$this->tableDeletedColumn => false])) {
            $this->triggerEvent(static::EVENT_RESTORED, [$iId]);
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
        $this->triggerEvent(static::EVENT_DESTROYING, [$iId]);

        $oDb    = Factory::service('Database');
        $sTable = $this->getTableName();

        // --------------------------------------------------------------------------

        $oDb->where('id', $iId);
        if ($oDb->delete($sTable)) {
            $this->triggerEvent(static::EVENT_DESTROYED, [$iId]);
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

        $oDb = Factory::service('Database');
        try {
            $oDb->trans_begin();
            foreach ($aIds as $iId) {
                if (!$this->destroy($iId)) {
                    throw new ModelException('Failed to destroy item with ID ' . $iId);
                }
            }
            $oDb->trans_commit();
            return true;
        } catch (\Exception $e) {
            $oDb->trans_rollback();
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

        $oDb    = Factory::service('Database');
        $sTable = $this->getTableName(true);

        // --------------------------------------------------------------------------

        //  Define the default sorting
        if (empty($aData['sort']) && !empty($this->defaultSortColumn)) {
            if (strpos($this->defaultSortColumn, '.') !== false) {
                $sColumn = $this->defaultSortColumn;
            } else {
                $sColumn = $this->getTableAlias(true) . $this->defaultSortColumn;
            }
            $aData['sort'] = [$sColumn, $this->defaultSortOrder];
        }

        // --------------------------------------------------------------------------

        if (!empty($aData['keywords'])) {
            $this->applySearchConditionals($aData, $aData['keywords']);
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
                $iPage    = ArrayHelper::getFromArray(0, $aData['limit'], 0);
                $iPerPage = ArrayHelper::getFromArray(1, $aData['limit']);
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
            $oDb->where($this->getTableAlias(true) . $this->tableDeletedColumn, false);
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
                    $sArrayTrigger     = ArrayHelper::getFromArray(0, $mTrigger) ?: ArrayHelper::getFromArray('trigger', $mTrigger);
                    $aArrayTriggerData = ArrayHelper::getFromArray(1, $mTrigger) ?: ArrayHelper::getFromArray('data', $mTrigger, []);
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
                    ArrayHelper::getFromArray($oExpandableField->trigger, $aTriggerData, [])
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

                } elseif (($bExpandForTrigger || $bExpandAll) && $oExpandableField->type === static::EXPANDABLE_TYPE_MANY) {

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
        $aItems = $this->getAll($iPage, $iPerPage, $aData, $bIncludeDeleted);
        $aOut   = [];

        //  Nothing returned? Skip the rest of this method, it's pointless.
        if (!$aItems) {
            return [];
        }

        // --------------------------------------------------------------------------

        //  Test columns
        $oTest = reset($aItems);

        if (!property_exists($oTest, $this->tableLabelColumn)) {
            throw new ModelException(
                static::class . '::getAllFlat() "' . $this->tableLabelColumn . '" is not a valid column.',
                1
            );
        }

        if (!property_exists($oTest, $this->tableLabelColumn)) {
            throw new ModelException(
                static::class . '::getAllFlat() "' . $this->tableIdColumn . '" is not a valid column.',
                1
            );
        }

        unset($oTest);

        // --------------------------------------------------------------------------

        foreach ($aItems as $oItem) {
            $aOut[$oItem->{$this->tableIdColumn}] = $oItem->{$this->tableLabelColumn};
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

                if ($sColumn === $this->tableIdColumn) {
                    $this->setCache(
                        $this->prepareCacheKey($sColumn, $oResult->{$sColumn}, $aData),
                        [$oResult]
                    );
                } else {
                    $this->setCacheAlias(
                        $this->prepareCacheKey($sColumn, $oResult->{$sColumn}, $aData),
                        $this->prepareCacheKey($this->tableIdColumn, $oResult->{$this->tableIdColumn}, $aData)
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
            json_encode($mValue),
            $aData !== null ? md5(json_encode($aData)) : null,
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
        if (!$this->tableIdColumn) {
            throw new ModelException(static::class . '::getById() Column variable not set.', 1);
        }

        return $this->getByColumn($this->tableIdColumn, $iId, $aData);
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
        if (!$this->tableIdColumn) {
            throw new ModelException(static::class . '::getByIds() Column variable not set.', 1);
        }

        $aItems = $this->getByColumn($this->tableIdColumn, $aIds, $aData, true);
        if ($bMaintainInputOrder) {
            return $this->sortItemsByColumn($aItems, $aIds, $this->tableIdColumn);
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
        if (!$this->tableSlugColumn) {
            throw new ModelException(static::class . '::getBySlug() Column variable not set.', 1);
        }

        return $this->getByColumn($this->tableSlugColumn, $sSlug, $aData);
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
        if (!$this->tableSlugColumn) {
            throw new ModelException(static::class . '::getBySlugs() Column variable not set.', 1);
        }

        $aItems = $this->getByColumn($this->tableSlugColumn, $aSlugs, $aData, true);
        if ($bMaintainInputOrder) {
            return $this->sortItemsByColumn($aItems, $aSlugs, $this->tableSlugColumn);
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
        if (!$this->tableTokenColumn) {
            throw new ModelException(static::class . '::getByToken() Column variable not set.', 1);
        }

        return $this->getByColumn($this->tableTokenColumn, $sToken, $aData);
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
        if (!$this->tableTokenColumn) {
            throw new ModelException(static::class . '::getByTokens() Column variable not set.', 1);
        }

        $aItems = $this->getByColumn($this->tableTokenColumn, $aTokens, $aData, true);
        if ($bMaintainInputOrder) {
            return $this->sortItemsByColumn($aItems, $aTokens, $this->tableTokenColumn);
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
        $iItemId,
        array $aAssociatedItems,
        $sAssociatedItemIdColumn,
        $sAssociatedModel,
        $sAssociatedModelProvider
    ) {
        $oAssociatedItemModel = Factory::model($sAssociatedModel, $sAssociatedModelProvider);
        $aTouchedIds          = [];
        $aExistingItemIds     = [];

        //  Get IDs of current items, we'll compare these later to see which ones to delete.
        $aData = [
            'where' => [
                [$oAssociatedItemModel->getTableAlias() . '.' . $sAssociatedItemIdColumn, $iItemId],
            ],
        ];

        $aExistingItems = $oAssociatedItemModel->getAll($aData);
        foreach ($aExistingItems as $oExistingItem) {
            $aExistingItemIds[] = $oExistingItem->id;
        }

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
        $oDb   = Factory::service('Database');
        $table = $this->getTableName(true);

        // --------------------------------------------------------------------------

        //  Apply common items
        $this->getCountCommon($aData);

        // --------------------------------------------------------------------------

        //  If non-destructive delete is enabled then apply the delete query
        if (!$this->isDestructiveDelete() && !$bIncludeDeleted) {
            $oDb->where($this->getTableAlias(true) . $this->tableDeletedColumn, false);
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

        $this->applySearchConditionals($aData, $sKeywords);

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
                    if (strpos($sInput, '.') !== false) {
                        return $sInput;
                    } else {
                        return $sAlias . $sInput;
                    }
                }, $mField);

                $aData['or_like'][] = ['CONCAT_WS(" ", ' . implode(',', $sMappedFields) . ')', $sKeywords];
            } else {
                if (strpos($mField, '.') !== false) {
                    $aData['or_like'][] = [$mField, $sKeywords];
                } else {
                    $aData['or_like'][] = [$sAlias . $mField, $sKeywords];
                }
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
     * @param string $sLabel    The label from which to generate a slug
     * @param int    $iIgnoreId The ID of an item to ignore
     *
     * @return string
     * @throws ModelException
     */
    protected function generateSlug(string $sLabel, int $iIgnoreId = null, array $aData = [])
    {
        $iCounter = 0;
        $oDb      = Factory::service('Database');
        $sSlug    = Transliterator::transliterate($sLabel);

        do {

            if ($iCounter) {
                $sSlugTest = $sSlug . '-' . $iCounter;
            } else {
                $sSlugTest = $sSlug;
            }

            if ($iIgnoreId) {
                $oDb->where($this->tableIdColumn . ' !=', $iIgnoreId);
            }

            $oDb->where($this->tableSlugColumn, $sSlugTest);
            $this->generateSlugHook($aData);
            $iCounter++;

        } while ($oDb->count_all_results($this->getTableName()));

        return $sSlugTest;
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
     * @param string|null $sMask   The token mask, defaults to $this->sTokenMask
     * @param string|null $sTable  The table to use defaults to $this->table
     * @param string|null $sColumn The column to use, defaults to $this->tableTokenColumn
     *
     * @return string
     * @throws ModelException
     */
    protected function generateToken($sMask = null, $sTable = null, $sColumn = null)
    {
        if (is_null($sMask)) {
            $sMask = $this->sTokenMask;
        }

        if (is_null($sTable)) {
            $sTable = $this->getTableName();
        }

        if (is_null($sColumn)) {
            if (!$this->tableTokenColumn) {
                throw new ModelException(static::class . '::generateToken() Token variable not set', 1);
            }
            $sColumn = $this->tableTokenColumn;
        }

        $oDb = Factory::service('Database');

        do {
            $sToken = generateToken($sMask);
            $oDb->where($sColumn, $sToken);
        } while ($oDb->count_all_results($sTable));

        return $sToken;
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
                case Form::FIELD_NUMBER:
                    $aIntegers[] = $oField->key;
                    break;
                case Form::FIELD_BOOLEAN:
                    $aBools[] = $oField->key;
                    break;
                case Form::FIELD_DATE:
                    $aDates[] = $oField->key;
                    break;
                case Form::FIELD_DATETIME:
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
        //  @todo (Pablo - 2019-04-15) - Deprecate $this->>tableAlias
        $sOut = static::TABLE_ALIAS ?? $this->tableAlias ?? '';

        if (empty($sOut)) {

            //  work it out
            $sTable = strtolower($this->getTableName());
            $sTable = preg_replace('/[^a-z_]/', '', $sTable);
            $sTable = preg_replace('/_/', ' ', $sTable);
            $aTable = explode(' ', $sTable);
            foreach ($aTable as $sWord) {
                $sOut .= $sWord[0];
            }
        }

        if (!empty($sOut) && $bIncludeSeparator) {
            $sOut .= '.';
        }

        return $sOut;
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
    protected function addExpandableField(array $aOptions)
    {
        //  Validation
        if (!array_key_exists('trigger', $aOptions)) {
            throw new ModelException('Expandable fields must define a "trigger".');
        }

        if (!array_key_exists('type', $aOptions)) {
            $aOptions['type'] = static::EXPANDABLE_TYPE_SINGLE;
        }

        if ($aOptions['type'] == static::EXPANDABLE_TYPE_SINGLE && !empty($aOptions['auto_save'])) {
            throw new ModelException(
                'Auto saving an expandable field is incompatible with type static::EXPANDABLE_TYPE_SINGLE'
            );
        } elseif ($aOptions['type'] == static::EXPANDABLE_TYPE_MANY) {
            $bAutoSave = array_key_exists('auto_save', $aOptions) ? !empty($aOptions['auto_save']) : true;
        } else {
            $bAutoSave = false;
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
            'data'        => ArrayHelper::getFromArray('data', $aOptions, []),

            /**
             * The ID column to use; for EXPANDABLE_TYPE_SINGLE this is property of the
             * parent object which contains the ID, for EXPANDABLE_TYPE_MANY, this is the
             * property of the child object which contains the parent's ID.
             */
            'id_column'   => $aOptions['id_column'],

            //  Whether the field is expanded by default
            'auto_expand' => ArrayHelper::getFromArray('auto_expand', $aOptions, false),

            //  Whether to automatically save expanded objects when the trigger is
            //  passed as a key to the create or update methods
            'auto_save'   => $bAutoSave,
        ];

        return $this;
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
     */
    protected function autoSaveExpandableFieldsSave($iId, array $aExpandableFields)
    {
        foreach ($aExpandableFields as $oField) {
            $aData = array_filter((array) $oField->data);
            $this->saveAssociatedItems(
                $iId,
                $aData,
                $oField->id_column,
                $oField->model,
                $oField->provider
            );
        }
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
     * Returns whether this model automatically generates timestamps or not
     *
     * @return bool
     */
    public function isAutoSetTimestamps()
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->tableAutoSetTimestamps
        return $this->tableAutoSetTimestamps ?? static::AUTO_SET_TIMESTAMP;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether this model automatically sets created/modified users or not
     *
     * @return bool
     */
    public function isAutoSetUsers()
    {
        return static::AUTO_SET_USER;
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
     * @return string
     */
    public function getColumn($sColumn, $sDefault = null)
    {
        $sColumn = ucfirst(trim($sColumn));
        if (property_exists($this, 'table' . $sColumn . 'Column')) {
            return $this->{'table' . $sColumn . 'Column'};
        }
        return $sDefault;
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

            $oTemp             = Factory::factory('ModelField');
            $oTemp->key        = 'locale';
            $oTemp->label      = 'Locale';
            $oTemp->type       = Form::FIELD_DROPDOWN;
            $oTemp->allow_null = false;
            $oTemp->validation = [
                FormValidation::RULE_REQUIRED,
                FormValidation::RULE_SUPPORTED_LOCALE,
            ];
            $oTemp->options    = $aOptions;
            $oTemp->class      = 'select2';
            $oTemp->info       = 'This field specifies what language the item is written in.';
            $oTemp->default    = $oLocale->getDefautLocale();

            $aFields['locale'] = $oTemp;
        }

        foreach ($aResult as $oField) {

            $oTemp             = Factory::factory('ModelField');
            $oTemp->key        = $oField->Field;
            $oTemp->label      = $this->describeFieldsPrepareLabel($oField->Field);
            $oTemp->allow_null = $oField->Null === 'YES';
            $oTemp->validation = [];

            //  Guess the field's type and some basic validation
            $this->describeFieldsGuessType($oTemp, $oField->Type);
            $this->describeFieldsGuessValidation($oTemp, $oField->Type);

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
    protected function describeFieldsPrepareLabel($sLabel)
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
     * @param \stdClass $oField The field object
     * @param string    $sType  The database type
     */
    protected function describeFieldsGuessType(&$oField, $sType)
    {
        preg_match('/^(.*?)(\((.+?)\)(.*))?$/', $sType, $aMatches);

        $sType       = ArrayHelper::getFromArray(1, $aMatches, 'text');
        $sTypeConfig = trim(ArrayHelper::getFromArray(3, $aMatches));
        $iLength     = is_numeric($sTypeConfig) ? (int) $sTypeConfig : null;

        switch ($sType) {

            /**
             * Numeric
             */
            case 'int':
            case 'mediumint':
            case 'bigint':
                //  @todo (Pablo - 2019-11-28) - This is only number to match the form type, and could be misleading
                $oField->type = Form::FIELD_NUMBER;
                break;

            /**
             * Boolean
             * Nails convention uses tinyint(1) as a boolean; if not (1) then treat as integer
             */
            case 'tinyint':
            case 'bool':
            case 'boolean':
                $oField->type = $iLength == 1 ? Form::FIELD_BOOLEAN : Form::FIELD_NUMBER;
                break;

            /**
             * String
             */
            case 'varchar':
                $oField->type       = Form::FIELD_TEXT;
                $oField->max_length = $iLength ?: null;
                break;
            case 'tinytext':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                $oField->type = Form::FIELD_TEXTAREA;
                break;

            /**
             * Date and time
             */
            case 'date':
                $oField->type = Form::FIELD_DATE;
                break;
            case 'datetime':
                $oField->type = Form::FIELD_DATETIME;
                break;
            case 'time':
                $oField->type = Form::FIELD_TIME;
                break;

            /**
             * ENUM
             */
            case 'enum':
            case 'set':
                if ($sType === 'enum') {
                    $oField->type = Form::FIELD_DROPDOWN;
                } else {
                    $oField->type = Form::FIELD_DROPDOWN_MULTIPLE;
                    $oField->key  .= '[]';
                }
                $oField->class   = 'select2';
                $aOptions        = explode("','", substr($sTypeConfig, 1, -1));
                $aLabels         = array_map('strtolower', $aOptions);
                $aLabels         = array_map([$this, 'describeFieldsPrepareLabel'], $aLabels);
                $oField->options = array_combine($aOptions, $aLabels);
                break;

            /**
             * Default to basic string
             */
            default:
                $oField->type       = Form::FIELD_TEXT;
                $oField->max_length = $iLength ?: null;
                break;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Guesses the field's validation rules based on it's type
     *
     * @param \stdClass $oField The field object
     * @param string    $sType  The database type
     */
    protected function describeFieldsGuessValidation(&$oField, $sType)
    {
        preg_match('/^(.*?)(\((.+?)\)(.*))?$/', $sType, $aMatches);

        $sType   = ArrayHelper::getFromArray(1, $aMatches, 'text');
        $iLength = ArrayHelper::getFromArray(3, $aMatches);
        $sExtra  = trim(strtolower(ArrayHelper::getFromArray(4, $aMatches)));

        switch ($sType) {

            /**
             * Numeric
             */
            case 'int':
                $oField->validation[] = FormValidation::RULE_INTEGER;

                if ($sExtra === 'unsigned') {
                    //  @todo (Pablo - 2019-12-18) - Use FormValidation::rule when CI is no longer a dependency
                    $oField->validation[] = sprintf('%s[%s]', FormValidation::RULE_GREATER_THAN, -1);
                }
                break;

            case 'tinyint':
                if ($oField->type === 'boolean') {
                    $oField->validation[] = FormValidation::RULE_IS_BOOL;
                } else {
                    $oField->validation[] = FormValidation::RULE_INTEGER;
                }
                break;

            /**
             * String
             */
            case 'varchar':
                if ($iLength) {
                    //  @todo (Pablo - 2019-12-18) - Use FormValidation::rule when CI is no longer a dependency
                    $oField->validation[] = sprintf('%s[%s]', FormValidation::RULE_MAX_LENGTH, $iLength);
                }
                break;

            /**
             * Date and time
             */
            case 'date':
                $oField->validation[] = FormValidation::RULE_VALID_DATE;
                break;

            case 'datetime':
                $oField->validation[] = FormValidation::RULE_VALID_DATETIME;
                break;

            /**
             * ENUM
             */
            case 'enum':
            case 'set':
                //  @todo (Pablo - 2019-12-18) - Use FormValidation::rule when CI is no longer a dependency
                $oField->validation[] = sprintf('%s[%s]', FormValidation::RULE_IN_LIST, implode(',', array_keys($oField->options)));
                break;

            /**
             * Default to basic string
             */
            default:
                if ($iLength) {
                    //  @todo (Pablo - 2019-12-18) - Use FormValidation::rule when CI is no longer a dependency
                    $oField->validation[] = sprintf('%s[%s]', FormValidation::RULE_MAX_LENGTH, $iLength);
                }
                break;
        }
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
    protected function triggerEvent($sEvent, array $aData)
    {
        if ($sEvent) {
            Factory::service('Event')
                ->trigger(
                    $sEvent,
                    static::getEventNamespace(),
                    $aData
                );
        }
    }
}
