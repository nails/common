<?php

/**
 * This class brings about uniformity to Nails models.
 *
 * @package     Nails
 * @subpackage  common
 * @category    models
 * @author      Nails Dev Team
 * @link
 */

class CORE_NAILS_Model extends CI_Model
{
    //  Class traits
    use NAILS_COMMON_TRAIT_ERROR_HANDLING;
    use NAILS_COMMON_TRAIT_CACHING;
    use NAILS_COMMON_TRAIT_GETCOUNT_COMMON;

    //  Common data
    protected $data;
    protected $user;
    protected $user_model;

    //  Data/Table structure
    protected $table;
    protected $tablePrefix;

    protected $tableIdColumn;
    protected $tableSlugColumn;
    protected $tableLabelColumn;

    protected $tableAutoSetTimestamps;

    protected $deletedFlag;

    //  Preferences
    protected $destructiveDelete;
    protected $perPage;


    /**
     * --------------------------------------------------------------------------
     *
     * CONSTRUCTOR && DESTRUCTOR
     * The constructor preps common variables and sets the model up for user.
     * The destructor clears
     *
     * --------------------------------------------------------------------------
     */


    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------

        //  Ensure models all have access to the global user_model
        if (function_exists('getUserObject')) {

            $this->user_model = getUserObject();
        }

        // --------------------------------------------------------------------------

        //  Define defaults
        $this->clear_errors();
        $this->destructiveDelete      = true;
        $this->tableIdColumn          = 'id';
        $this->tableSlugColumn        = 'slug';
        $this->tableLabelColumn       = 'label';
        $this->tableAutoSetTimestamps = true;
        $this->deletedFlag            = 'is_deleted';
        $this->perPage                = 50;
    }

    // --------------------------------------------------------------------------


    /**
     * Destruct the model
     * @return void
     */
    public function __destruct()
    {
        /**
         * @TODO: decide whether this is necessary; should caches be persistent;
         * gut says yes.
         */

        //  Clear cache's
        if (isset($this->_cache_keys) && $this->_cache_keys) {

            foreach ($this->_cache_keys as $key) {

                $this->_unset_cache($key);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Inject the user object, private by convention - only really used by a few core Nails classes
     * @param object $user The user object
     * @return void
     */
    public function setUserObject(&$user)
    {
        $this->user = $user;
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
     * @TODO: link to docs
     *
     * --------------------------------------------------------------------------
     */


    /**
     * Creates a new object
     * @param  array   $aData         The data to create the object with
     * @param  boolean $bReturnObject Whether to return just the new ID or the full object
     * @return mixed
     */
    public function create($aData = array(), $bReturnObject = false)
    {
        if (!$this->table) {

            show_error(get_called_class() . '::create() Table variable not set');
            return;
        }

        // --------------------------------------------------------------------------

        if ($this->tableAutoSetTimestamps) {

            if (empty($aData['created'])) {
                $aData['created'] = date('Y-m-d H:i:s');
            }
            if (empty($aData['modified'])) {
                $aData['modified'] = date('Y-m-d H:i:s');
            }

            if ($this->user_model->isLoggedIn()) {

                if (empty($aData['created_by'])) {
                    $aData['created_by'] = activeUser('id');
                }
                if (empty($aData['modified_by'])) {
                    $aData['modified_by'] = activeUser('id');
                }

            } else {

                if (empty($aData['created_by'])) {
                    $this->db->set('created_by', null);
                    $aData['created_by'] = null;
                }
                if (empty($aData['modified_by'])) {
                    $aData['modified_by'] = null;
                }
            }

        }

        if (!empty($aData)) {

            $this->db->set($aData);

        }

        $this->db->insert($this->table);

        if ($this->db->affected_rows()) {

            $iId = $this->db->insert_id();

            // --------------------------------------------------------------------------

            if ($bReturnObject) {

                return $this->get_by_id($iId);

            } else {

                return $iId;
            }

        } else {

            return false;
        }
    }

    // --------------------------------------------------------------------------


    /**
     * Updates an existing object
     * @param  integer $iId   The ID of the object to update
     * @param  array   $aData The data to update the object with
     * @return boolean
     */
    public function update($iId, $aData = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::update() Table variable not set');
            return;

        } else {

            $sPrefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
            $sTable  = $this->tablePrefix ? $this->table . ' ' . $this->tablePrefix : $this->table;
        }

        // --------------------------------------------------------------------------

        if ($this->tableAutoSetTimestamps) {

            if (empty($aData['modified'])) {
                $aData[$sPrefix . 'modified'] = date('Y-m-d H:i:s');
            }

            if ($this->user_model->isLoggedIn()) {

                if (empty($aData['modified_by'])) {
                    $aData[$sPrefix . 'modified_by'] = activeUser('id');
                }

            } else {

                if (empty($aData['modified_by'])) {
                    $aData[$sPrefix . 'modified_by'] = null;
                }
            }

        }

        if (!empty($aData)) {

            $this->db->set($aData);

        }

        // --------------------------------------------------------------------------

        $this->db->where($sPrefix . 'id', $iId);
        return $this->db->update($sTable);
    }

    // --------------------------------------------------------------------------

    /**
     * Marks an object as deleted
     *
     * If destructive deletion is enabled then this method will permanently
     * destroy the object. If Non-destructive deletion is enabled then the
     * $this->deletedFlag field will be set to true.
     *
     * @param int      $id The ID of the object to mark as deleted
     * @return boolean
     */
    public function delete($id)
    {
        //  Perform this check here so the error message is more easily traced.
        if (!$this->table) {

            show_error(get_called_class() . '::delete() Table variable not set');
            return;
        }

        // --------------------------------------------------------------------------

        if ($this->destructiveDelete) {

            //  Destructive delete; nuke that row.
            return $this->destroy($id);

        } else {

            //  Non-destructive delete, update the flag
            $data = array(
                $this->deletedFlag => true
            );

            return $this->update($id, $data);

        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unmarks an object as deleted
     *
     * If destructive deletion is enabled then this method will return false.
     * If Non-destructive deletion is enabled then the $this->deletedFlag
     * field will be set to false.
     *
     * @param int      $id The ID of the object to restore
     * @return boolean
     */
    public function restore($id)
    {
        //  Perform this check here so the error message is more easily traced.
        if (!$this->table) {

            show_error(get_called_class() . '::restore() Table variable not set');
            return;
        }

        // --------------------------------------------------------------------------

        if ($this->destructiveDelete) {

            //  Destructive delete; can't be resurrecting the dead.
            return false;

        } else {

            //  Non-destructive delete, update the flag
            $data = array(
                $this->deletedFlag => false
            );

            return $this->update($id, $data);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Permanently deletes an object
     *
     * This method will attempt to delete the row from the table, regardless of whether
     * destructive deletion is enabled or not.
     *
     * @param int      $id The ID of the object to destroy
     * @return boolean
     */
    public function destroy($id)
    {
        //  Perform this check here so the error message is more easily traced.
        if (!$this->table) {

            show_error(get_called_class() . '::destroy() Table variable not set');
            return;
        }

        // --------------------------------------------------------------------------

        $this->db->where('id', $id);
        $this->db->delete($this->table);

        return (bool) $this->db->affected_rows();
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
     * Fetches all objects, optionally paginated.
     * @param int    $page           The page number of the results, if null then no pagination
     * @param int    $perPage        How many items per page of paginated results
     * @param mixed  $data           Any data to pass to _getcount_common()
     * @param bool   $includeDeleted If non-destructive delete is enabled then this flag allows you to include deleted items
     * @param string $_caller        Internal flag to pass to _getcount_common(), contains the calling method
     * @return array
     */
    public function get_all($page = null, $perPage = null, $data = array(), $includeDeleted = false, $_caller = 'GET_ALL')
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_all() Table variable not set');
            return;

        } else {

            $table = $this->tablePrefix ? $this->table . ' ' . $this->tablePrefix : $this->table;
        }

        // --------------------------------------------------------------------------

        //  Apply common items; pass $data
        $this->_getcount_common($data, $_caller);

        // --------------------------------------------------------------------------

        //  Facilitate pagination
        if (!is_null($page)) {

            /**
             * Adjust the page variable, reduce by one so that the offset is calculated
             * correctly. Make sure we don't go into negative numbers
             */

            $page--;
            $page = $page < 0 ? 0 : $page;

            //  Work out what the offset should be
            $perPage = is_null($perPage) ? $this->perPage : (int) $perPage;
            $offset   = $page * $perPage;

            $this->db->limit($perPage, $offset);
        }

        // --------------------------------------------------------------------------

        //  If non-destructive delete is enabled then apply the delete query
        if (!$this->destructiveDelete && !$includeDeleted) {

            $prefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
            $this->db->where($prefix . $this->deletedFlag, false);
        }

        // --------------------------------------------------------------------------

        /**
         * How are we handling execution? If $data['RETURN_QUERY_OBJECT'] is truthy,
         * then simply return the raw query object - leave it up to the caller to
         * process/iterate as required
         */

        if (empty($data['RETURN_QUERY_OBJECT'])) {

            $results    = $this->db->get($table)->result();
            $numResults = count($results);

            for ($i = 0; $i < $numResults; $i++) {

                $this->_format_object($results[$i], $data);
            }

            return $results;

        } else {

            return $this->db->get($table);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Fetches all objects as a flat array
     * @param  int     $page           The page number of the results
     * @param  int     $perPage        The number of items per page
     * @param  array   $data           Any data to pass to _getcount_common()
     * @param  boolean $includeDeleted Whether or not to include deleted items
     * @param  string  $_caller        Passed to _getcount_common() as an easy means of identifying errors
     * @return array
     */
    public function get_all_flat($page = null, $perPage = null, $data = array(), $includeDeleted = false, $_caller = 'GET_ALL_FLAT')
    {
        $items = $this->get_all($page, $perPage, $data, $includeDeleted, $_caller);
        $out   = array();

        //  Nothing returned? Skip the rest of this method, it's pointless.
        if (!$items) {

            return array();
        }

        // --------------------------------------------------------------------------

        //  Test columns
        $_test = reset($items);

        if (!isset($_test->{$this->tableLabelColumn})) {

            show_error(get_called_class() . '::get_all_flat() "' . $this->tableLabelColumn . '" is not a valid label column.');
            return;
        }

        if (!isset($_test->{$this->tableIdColumn})) {

            show_error(get_called_class() . '::get_all_flat() "' . $this->tableIdColumn . '" is not a valid id column.');
            return;
        }

        unset($_test);

        // --------------------------------------------------------------------------

        foreach ($items as $item) {

            $out[$item->{$this->tableIdColumn}] = $item->{$this->tableLabelColumn};
        }

        return $out;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by it's ID
     * @param  int      $id   The ID of the object to fetch
     * @param  mixed    $data Any data to pass to _getcount_common()
     * @return stdClass
     */
    public function get_by_id($id, $data = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_by_id() Table variable not set');
            return;

        } else {

            $prefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
        }

        // --------------------------------------------------------------------------

        if (empty($id)) {
            return false;
        }

        // --------------------------------------------------------------------------

        if (!isset($data['where'])) {

            $data['where'] = array();
        }

        $data['where'][] = array($prefix . $this->tableIdColumn, $id);

        // --------------------------------------------------------------------------

        $result = $this->get_all(null, null, $data, false, 'GET_BY_ID');

        // --------------------------------------------------------------------------

        if (!$result) {

            return false;
        }

        // --------------------------------------------------------------------------

        return $result[0];
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by their IDs
     * @param  array $id   An array of IDs to fetch
     * @param  mixed $data Any data to pass to _getcount_common()
     * @return array
     */
    public function get_by_ids($ids, $data = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_by_ids() Table variable not set');
            return;

        } else {

            $prefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
        }

        // --------------------------------------------------------------------------

        if (empty($ids)) {
            return array();
        }

        // --------------------------------------------------------------------------

        if (!isset($data['where_in'])) {

            $data['where_in'] = array();
        }

        $data['where_in'][] = array($prefix . $this->tableIdColumn, $ids);

        // --------------------------------------------------------------------------

        return $this->get_all(null, null, $data, false, 'GET_BY_IDS');
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by it's slug
     * @param  int      $slug The slug of the object to fetch
     * @param  mixed    $data Any data to pass to _getcount_common()
     * @return stdClass
     */
    public function get_by_slug($slug, $data = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_by_slug() Table variable not set');
            return;

        } else {

            $prefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
        }

        // --------------------------------------------------------------------------

        if (empty($slug)) {
            return false;
        }

        // --------------------------------------------------------------------------

        if (!isset($data['where'])) {

            $data['where'] = array();
        }

        $data['where'][] = array($prefix . $this->tableSlugColumn, $slug);

        // --------------------------------------------------------------------------

        $result = $this->get_all(null, null, $data, false, 'GET_BY_SLUG');

        // --------------------------------------------------------------------------

        if (!$result) {

            return false;
        }

        // --------------------------------------------------------------------------

        return $result[0];
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by their slugs
     * @param  array $slug An array of slugs to fetch
     * @param  mixed $data Any data to pass to _getcount_common()
     * @return array
     */
    public function get_by_slugs($slugs, $data = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_by_slug() Table variable not set');
            return;

        } else {

            $prefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
        }

        // --------------------------------------------------------------------------

        if (empty($slugs)) {
            return array();
        }

        // --------------------------------------------------------------------------

        if (!isset($data['where_in'])) {

            $data['where_in'] = array();
        }

        $data['where_in'][] = array($prefix . $this->tableSlugColumn, $slugs);

        // --------------------------------------------------------------------------

        return $this->get_all(null, null, $data, false, 'GET_BY_SLUGS');
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
     * @param  mixed    $idSlug The ID or slug of the object to fetch
     * @param  mixed    $data   Any data to pass to _getcount_common()
     * @return stdClass
     */
    public function get_by_id_or_slug($idSlug, $data = array())
    {
        if (is_numeric($idSlug)) {

            return $this->get_by_id($idSlug, $data);

        } else {

            return $this->get_by_slug($idSlug, $data);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Counts all objects
     * @param  array   $data           An array of data to pass to _Getcount_common()
     * @param  boolean $includeDeleted Whetehr to include deleted objects or not
     * @return integer
     */
    public function count_all($data = array(), $includeDeleted = false)
    {
        if (!$this->table) {

            show_error(get_called_class() . '::count_all() Table variable not set');
            return;

        } else {

            $table  = $this->tablePrefix ? $this->table . ' ' . $this->tablePrefix : $this->table;
        }

        // --------------------------------------------------------------------------

        //  Apply common items
        $this->_getcount_common($data, 'COUNT_ALL');

        // --------------------------------------------------------------------------

        //  If non-destructive delete is enabled then apply the delete query
        if (!$this->destructiveDelete && !$includeDeleted) {

            $prefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
            $this->db->where($prefix . $this->deletedFlag, false);
        }

        // --------------------------------------------------------------------------

        return $this->db->count_all_results($table);
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
     * This method provides the functionality to generate a unique slug for an item in the database.
     * @param string $label    The label from which to generate a slug
     * @param string $prefix   Any prefix to add to the slug
     * @param string $suffix   Any suffix to add to the slug
     * @param string $table    The table to use defaults to $this->table
     * @param string $column   The column to use, defaults to $this->tableSlugColumn
     * @param int    $ignoreId An ID to ignore when searching
     * @param string $idColumn The column to use for the ID, defaults to $this->tableIdColumn
     * @return string
     */
    protected function _generate_slug($label, $prefix = '', $suffix = '', $table = null, $column = null, $ignoreId = null, $idColumn = null)
    {
        //  Perform this check here so the error message is more easily traced.
        if (is_null($table)) {

            if (!$this->table) {

                show_error(get_called_class() . '::_generate_slug() Table variable not set');
                return;
            }

            $table = $this->table;

        } else {

            $table = $table;
        }

        if (is_null($column)) {

            if (!$this->tableSlugColumn) {

                show_error(get_called_class() . '::_generate_slug() Column variable not set');
                return;
            }

            $column = $this->tableSlugColumn;

        } else {

            $column = $column;
        }

        // --------------------------------------------------------------------------

        $counter = 0;

        do {

            $slug = url_title(str_replace('/', '-', $label), 'dash', true);

            if ($counter) {

                $slugTest = $prefix . $slug . $suffix . '-' . $counter;

            } else {

                $slugTest = $prefix . $slug . $suffix;
            }

            if ($ignoreId) {

                $_id_column = $idColumn ? $idColumn : $this->tableIdColumn;
                $this->db->where($_id_column . ' !=', $ignoreId);
            }

            $this->db->where($column, $slugTest);
            $counter++;

        } while ($this->db->count_all_results($table));

        return $slugTest;
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The get_all() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param  object $obj      A reference to the object being formatted.
     * @param  array  $data     The same data array which is passed to _getcount_common, for reference if needed
     * @param  array  $integers Fields which should be cast as integers if numerical and not null
     * @param  array  $bools    Fields which should be cast as booleans if not null
     * @param  array  $floats   Fields which should be cast as floats if not null
     * @return void
     */
    protected function _format_object(&$obj, $data = array(), $integers = array(), $bools = array(), $floats = array())
    {
        $integers   = (array) $integers;
        $integers[] = $this->tableIdColumn;
        $integers[] = 'parent_id';
        $integers[] = 'parentId';
        $integers[] = 'user_id';
        $integers[] = 'userId';
        $integers[] = 'created_by';
        $integers[] = 'createdBy';
        $integers[] = 'modified_by';
        $integers[] = 'modifiedBy';
        $integers[] = 'order';

        foreach ($integers as $property) {

            if (property_exists($obj, $property) && is_numeric($obj->{$property}) && !is_null($obj->{$property})) {

                $obj->{$property} = (int) $obj->{$property};
            }
        }

        // --------------------------------------------------------------------------

        $bools   = (array) $bools;
        $bools[] = 'is_active';
        $bools[] = 'isActive';
        $bools[] = 'is_deleted';
        $bools[] = 'isDeleted';
        $bools[] = 'is_published';
        $bools[] = 'isPublished';

        foreach ($bools as $property) {

            if (property_exists($obj, $property) && !is_null($obj->{$property})) {

                $obj->{$property} = (bool) $obj->{$property};
            }
        }

        // --------------------------------------------------------------------------

        $floats = (array) $floats;

        foreach ($floats as $property) {

            if (property_exists($obj, $property) && is_numeric($obj->{$property}) && !is_null($obj->{$property})) {

                $obj->{$property} = (float) $obj->{$property};
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $table
     * @return string
     */
    public function getTableName()
    {
        return $this->table;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $tablePrefix
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }
}
