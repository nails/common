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

class Base
{
    use \Nails\Common\Traits\ErrorHandling;
    use \Nails\Common\Traits\Caching;
    use \Nails\Common\Traits\GetCountCommon;

    // --------------------------------------------------------------------------

    //  Common data
    protected $data;
    protected $user;
    protected $user_model;

    //  Data/Table structure
    protected $table;
    protected $tablePrefix;

    //  Column names
    protected $tableIdColumn;
    protected $tableSlugColumn;
    protected $tableLabelColumn;
    protected $tableCreatedColumn;
    protected $tableCreatedByColumn;
    protected $tableModifiedColumn;
    protected $tableModifiedByColumn;
    protected $tableDeletedColumn;

    protected $tableAutoSetTimestamps;
    protected $tableAutoSetSlugs;

    //  Preferences
    protected $destructiveDelete;
    protected $perPage;

    // --------------------------------------------------------------------------

    /**
     * @todo : this is copied directly from CodeIgniter - consider removing.
     * __get
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param   string
     * @access private
     */
    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

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
        $this->tableCreatedColumn     = 'created';
        $this->tableCreatedByColumn   = 'created_by';
        $this->tableModifiedColumn    = 'modified';
        $this->tableModifiedByColumn  = 'modified_by';
        $this->tableDeletedColumn     = 'is_deleted';
        $this->tableAutoSetTimestamps = true;
        $this->tableAutoSetSlugs      = false;
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
         * @todo: decide whether this is necessary; should caches be persistent;
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

            if (empty($aData[$this->tableCreatedColumn])) {
                $aData[$this->tableCreatedColumn] = date('Y-m-d H:i:s');
            }
            if (empty($aData[$this->tableModifiedColumn])) {
                $aData[$this->tableModifiedColumn] = date('Y-m-d H:i:s');
            }

            if ($this->user_model->isLoggedIn()) {

                if (empty($aData[$this->tableCreatedByColumn])) {
                    $aData[$this->tableCreatedByColumn] = activeUser('id');
                }
                if (empty($aData[$this->tableModifiedByColumn])) {
                    $aData[$this->tableModifiedByColumn] = activeUser('id');
                }

            } else {

                if (empty($aData[$this->tableCreatedByColumn])) {
                    $this->db->set($this->tableCreatedByColumn, null);
                    $aData[$this->tableCreatedByColumn] = null;
                }
                if (empty($aData[$this->tableModifiedByColumn])) {
                    $aData[$this->tableModifiedByColumn] = null;
                }
            }

        }

        if (!empty($this->tableAutoSetSlugs) && empty($aData[$this->tableSlugColumn])) {

            if (empty($this->tableSlugColumn)) {
                show_error(get_called_class() . '::create() Slug column variable not set');
                return;
            }

            if (empty($this->tableLabelColumn)) {
                show_error(get_called_class() . '::create() Label column variable not set');
                return;
            }

            if (empty($aData[$this->tableLabelColumn])) {

                show_error(
                    get_called_class() . '::create() "' . $this->tableLabelColumn .
                    '" is required when automatically generting slugs.'
                );
                return false;
            }

            $aData[$this->tableSlugColumn] = $this->_generate_slug($aData[$this->tableLabelColumn]);
        }

        if (!empty($aData)) {

            unset($aData['id']);
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

            if (empty($aData[$this->tableModifiedColumn])) {
                $aData[$sPrefix . $this->tableModifiedColumn] = date('Y-m-d H:i:s');
            }

            if ($this->user_model->isLoggedIn()) {

                if (empty($aData[$this->tableModifiedByColumn])) {
                    $aData[$sPrefix . $this->tableModifiedByColumn] = activeUser('id');
                }

            } else {

                if (empty($aData[$this->tableModifiedByColumn])) {
                    $aData[$sPrefix . $this->tableModifiedByColumn] = null;
                }
            }
        }

        if (!empty($this->tableAutoSetSlugs) && empty($aData[$this->tableSlugColumn])) {

            if (empty($this->tableSlugColumn)) {
                show_error(get_called_class() . '::update() Slug column variable not set');
                return;
            }

            if (empty($this->tableLabelColumn)) {
                show_error(get_called_class() . '::update() Label column variable not set');
                return;
            }

            /**
             *  We only need to re-generate the slug field if there's a label being passed. If
             *  no label, assume slug is unchanged.
             */
            if (!empty($aData[$this->tableLabelColumn])) {

                $aData[$sPrefix . $this->tableSlugColumn] = $this->_generate_slug(
                    $aData[$this->tableLabelColumn],
                    '',
                    '',
                    null,
                    null,
                    $iId
                );
            }
        }

        if (!empty($aData)) {

            unset($aData['id']);
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
     * $this->tableDeletedColumn field will be set to true.
     *
     * @param  int     $iId The ID of the object to mark as deleted
     * @return boolean
     */
    public function delete($iId)
    {
        //  Perform this check here so the error message is more easily traced.
        if (!$this->table) {

            show_error(get_called_class() . '::delete() Table variable not set');
            return;
        }

        // --------------------------------------------------------------------------

        if ($this->destructiveDelete) {

            //  Destructive delete; nuke that row.
            return $this->destroy($iId);

        } else {

            //  Non-destructive delete, update the flag
            $aData = array(
                $this->tableDeletedColumn => true
            );

            return $this->update($iId, $aData);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unmarks an object as deleted
     *
     * If destructive deletion is enabled then this method will return false.
     * If Non-destructive deletion is enabled then the $this->tableDeletedColumn
     * field will be set to false.
     *
     * @param  int     $iId The ID of the object to restore
     * @return boolean
     */
    public function restore($iId)
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
            $aData = array(
                $this->tableDeletedColumn => false
            );

            return $this->update($iId, $aData);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Permanently deletes an object
     *
     * This method will attempt to delete the row from the table, regardless of whether
     * destructive deletion is enabled or not.
     *
     * @param  int     $iId The ID of the object to destroy
     * @return boolean
     */
    public function destroy($iId)
    {
        //  Perform this check here so the error message is more easily traced.
        if (!$this->table) {

            show_error(get_called_class() . '::destroy() Table variable not set');
            return;
        }

        // --------------------------------------------------------------------------

        $this->db->where('id', $iId);
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

            $sPrefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
            $this->db->where($sPrefix . $this->tableDeletedColumn, false);
        }

        // --------------------------------------------------------------------------

        /**
         * How are we handling execution? If $data['RETURN_QUERY_OBJECT'] is truthy,
         * then simply return the raw query object - leave it up to the caller to
         * process/iterate as required
         */

        if (empty($data['RETURN_QUERY_OBJECT'])) {

            $aResults   = $this->db->get($table)->result();
            $numResults = count($aResults);

            for ($i = 0; $i < $numResults; $i++) {

                $this->_format_object($aResults[$i], $data);
            }

            return $aResults;

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

            show_error(
                get_called_class() . '::get_all_flat() "' . $this->tableLabelColumn . '" is not a valid label column.'
            );
            return;
        }

        if (!isset($_test->{$this->tableIdColumn})) {

            show_error(
                get_called_class() . '::get_all_flat() "' . $this->tableIdColumn . '" is not a valid id column.'
            );
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
     * @param  int      $iId   The ID of the object to fetch
     * @param  mixed    $aData Any data to pass to _getcount_common()
     * @return mixed           stdClass on success, false on failure
     */
    public function get_by_id($iId, $aData = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_by_id() Table variable not set');
            return;

        }

        // --------------------------------------------------------------------------

        $sPrefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';

        // --------------------------------------------------------------------------

        if (empty($iId)) {
            return false;
        }

        // --------------------------------------------------------------------------

        if (!isset($aData['where'])) {

            $aData['where'] = array();
        }

        $aData['where'][] = array($sPrefix . $this->tableIdColumn, $iId);

        // --------------------------------------------------------------------------

        $aResult = $this->get_all(null, null, $aData, false, 'GET_BY_ID');

        // --------------------------------------------------------------------------

        if (empty($aResult)) {

            return false;
        }

        // --------------------------------------------------------------------------

        return $aResult[0];
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by their IDs
     * @param  array $aIds  An array of IDs to fetch
     * @param  mixed $aData Any data to pass to _getcount_common()
     * @return array
     */
    public function get_by_ids($aIds, $aData = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_by_ids() Table variable not set');
            return;

        }

        // --------------------------------------------------------------------------

        $sPrefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';

        // --------------------------------------------------------------------------

        if (empty($aIds)) {
            return array();
        }

        // --------------------------------------------------------------------------

        if (!isset($aData['where_in'])) {

            $aData['where_in'] = array();
        }

        $aData['where_in'][] = array($sPrefix . $this->tableIdColumn, $aIds);

        // --------------------------------------------------------------------------

        return $this->get_all(null, null, $aData, false, 'GET_BY_IDS');
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by it's slug
     * @param  string   $sSlug The slug of the object to fetch
     * @param  mixed    $data Any data to pass to _getcount_common()
     * @return stdClass
     */
    public function get_by_slug($sSlug, $aData = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_by_slug() Table variable not set');
            return;
        }

        // --------------------------------------------------------------------------

        $sPrefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';

        // --------------------------------------------------------------------------

        if (empty($sSlug)) {
            return false;
        }

        // --------------------------------------------------------------------------

        if (!isset($aData['where'])) {

            $aData['where'] = array();
        }

        $aData['where'][] = array($sPrefix . $this->tableSlugColumn, $sSlug);

        // --------------------------------------------------------------------------

        $aResult = $this->get_all(null, null, $aData, false, 'GET_BY_SLUG');

        // --------------------------------------------------------------------------

        if (empty($aResult)) {

            return false;
        }

        // --------------------------------------------------------------------------

        return $aResult[0];
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by their slugs
     * @param  array $aSlugs An array of slugs to fetch
     * @param  mixed $aData  Any data to pass to _getcount_common()
     * @return array
     */
    public function get_by_slugs($aSlugs, $aData = array())
    {
        if (!$this->table) {

            show_error(get_called_class() . '::get_by_slug() Table variable not set');
            return;
        }

        // --------------------------------------------------------------------------

        $sPrefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';

        // --------------------------------------------------------------------------

        if (empty($aSlugs)) {
            return array();
        }

        // --------------------------------------------------------------------------

        if (!isset($aData['where_in'])) {

            $aData['where_in'] = array();
        }

        $aData['where_in'][] = array($sPrefix . $this->tableSlugColumn, $aSlugs);

        // --------------------------------------------------------------------------

        return $this->get_all(null, null, $aData, false, 'GET_BY_SLUGS');
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
     * @param  mixed    $mIdSlug The ID or slug of the object to fetch
     * @param  array    $aData   Any data to pass to _getcount_common()
     * @return stdClass
     */
    public function get_by_id_or_slug($mIdSlug, $aData = array())
    {
        if (is_numeric($mIdSlug)) {

            return $this->get_by_id($mIdSlug, $aData);

        } else {

            return $this->get_by_slug($mIdSlug, $aData);
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

            $sPrefix = $this->tablePrefix ? $this->tablePrefix . '.' : '';
            $this->db->where($sPrefix . $this->tableDeletedColumn, false);
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
        $integers[] = $this->tableCreatedByColumn;
        $integers[] = $this->tableModifiedByColumn;
        $integers[] = 'parent_id';
        $integers[] = 'user_id';
        $integers[] = 'order';

        foreach ($integers as $property) {

            if (property_exists($obj, $property) && is_numeric($obj->{$property}) && !is_null($obj->{$property})) {

                $obj->{$property} = (int) $obj->{$property};
            }
        }

        // --------------------------------------------------------------------------

        $bools   = (array) $bools;
        $bools[] = $this->tableDeletedColumn;
        $bools[] = 'is_active';
        $bools[] = 'is_published';

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
