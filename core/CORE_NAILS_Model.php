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
    protected $_table;
    protected $_table_prefix;

    protected $_table_id_column;
    protected $_table_slug_column;
    protected $_table_label_column;

    protected $_table_auto_set_timestamps;

    protected $_deleted_flag;

    //  Preferences
    protected $_destructive_delete;
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
        if (function_exists('get_userobject')) {

            $this->user_model = get_userobject();
            $this->user       = get_userobject();
        }

        // --------------------------------------------------------------------------

        //  Define defaults
        $this->clear_errors();
        $this->_destructive_delete        = true;
        $this->_table_id_column           = 'id';
        $this->_table_slug_column         = 'slug';
        $this->_table_label_column        = 'label';
        $this->_table_auto_set_timestamps = true;
        $this->_deleted_flag              = 'is_deleted';
        $this->perPage                  = 50;
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
     * @param  array   $data         The data to create the object with
     * @param  boolean $returnObject Whether to return just the new ID or the full object
     * @return mixed
     */
    public function create($data = array(), $returnObject = false)
    {
        if (!$this->_table) {

            show_error(get_called_class() . '::create() Table variable not set');
        }

        // --------------------------------------------------------------------------

        if ($this->_table_auto_set_timestamps) {

            $this->db->set('created', 'NOW()', false);
            $this->db->set('modified', 'NOW()', false);

            if ($this->user_model->isLoggedIn()) {

                $this->db->set('created_by', activeUser('id'));
                $this->db->set('modified_by', activeUser('id'));

            } else {

                $this->db->set('created_by', null);
                $this->db->set('modified_by', null);
            }

        } elseif (!$data) {

            $this->_set_error('No data to insert.');
            return false;
        }

        if ($data) {

            $this->db->set($data);
        }

        $this->db->insert($this->_table);

        if ($this->db->affected_rows()) {

            $id = $this->db->insert_id();

            // --------------------------------------------------------------------------

            if ($returnObject) {

                return $this->get_by_id($id);

            } else {

                return $id;
            }

        } else {

            return false;
        }
    }

    // --------------------------------------------------------------------------


    /**
     * Updates an existing object
     * @param int      $id   The ID of the object to update
     * @param array    $data The data to update the object with
     * @return boolean
     */
    public function update($id, $data = array())
    {
        if (!$this->_table) {

            show_error(get_called_class() . '::update() Table variable not set');

        } else {

            $prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
            $table  = $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;
        }

        // --------------------------------------------------------------------------

        if ($this->_table_auto_set_timestamps) {

            $this->db->set($prefix . 'modified', 'NOW()', false);

            if ($this->user_model->isLoggedIn()) {

                $this->db->set($prefix . 'modified_by', activeUser('id'));

            } else {

                $this->db->set($prefix . 'modified_by', null);
            }

        } elseif (!$data) {

            $this->_set_error('No data to update.');
            return false;
        }

        if ($data) {

            $this->db->set($data);
        }

        // --------------------------------------------------------------------------

        $this->db->where($prefix . 'id', $id);
        return $this->db->update($table);
    }

    // --------------------------------------------------------------------------

    /**
     * Marks an object as deleted
     *
     * If destructive deletion is enabled then this method will permanently
     * destroy the object. If Non-destructive deletion is enabled then the
     * $this->_deleted_flag field will be set to true.
     *
     * @param int      $id The ID of the object to mark as deleted
     * @return boolean
     */
    public function delete($id)
    {
        //  Perform this check here so the error message is more easily traced.
        if (!$this->_table) {

            show_error(get_called_class() . '::delete() Table variable not set');
        }

        // --------------------------------------------------------------------------

        if ($this->_destructive_delete) {

            //  Destructive delete; nuke that row.
            return $this->destroy($id);

        } else {

            //  Non-destructive delete, update the flag
            $data = array(
                $this->_deleted_flag => true
            );

            return $this->update($id, $data);

        }
    }

    // --------------------------------------------------------------------------

    /**
     * Unmarks an object as deleted
     *
     * If destructive deletion is enabled then this method will return false.
     * If Non-destructive deletion is enabled then the $this->_deleted_flag
     * field will be set to false.
     *
     * @param int      $id The ID of the object to restore
     * @return boolean
     */
    public function restore($id)
    {
        //  Perform this check here so the error message is more easily traced.
        if (!$this->_table) {

            show_error(get_called_class() . '::restore() Table variable not set');
        }

        // --------------------------------------------------------------------------

        if ($this->_destructive_delete) {

            //  Destructive delete; can't be resurrecting the dead.
            return false;

        } else {

            //  Non-destructive delete, update the flag
            $data = array(
                $this->_deleted_flag => false
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
        if (!$this->_table) {

            show_error(get_called_class() . '::destroy() Table variable not set');
        }

        // --------------------------------------------------------------------------

        $this->db->where('id', $id);
        $this->db->delete($this->_table);

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
        if (!$this->_table) {

            show_error(get_called_class() . '::get_all() Table variable not set');

        } else {

            $table = $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;
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
        if (!$this->_destructive_delete && !$includeDeleted) {

            $prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
            $this->db->where($prefix . $this->_deleted_flag, false);
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

                $this->_format_object($results[$i]);
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

        if (!isset($_test->{$this->_table_label_column})) {

            show_error(get_called_class() . '::get_all_flat() "' . $this->_table_label_column . '" is not a valid label column.');
        }

        if (!isset($_test->{$this->_table_id_column})) {

            show_error(get_called_class() . '::get_all_flat() "' . $this->_table_id_column . '" is not a valid id column.');
        }

        unset($_test);

        // --------------------------------------------------------------------------

        foreach ($items as $item) {

            $out[$item->{$this->_table_id_column}] = $item->{$this->_table_label_column};
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
        if (!$this->_table) {

            show_error(get_called_class() . '::get_by_id() Table variable not set');

        } else {

            $prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
        }

        // --------------------------------------------------------------------------

        if (!isset($data['where'])) {

            $data['where'] = array();
        }

        $data['where'][] = array($prefix . $this->_table_id_column, $id);

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
        if (!$this->_table) {

            show_error(get_called_class() . '::get_by_ids() Table variable not set');

        } else {

            $prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
        }

        // --------------------------------------------------------------------------

        if (!isset($data['where_in'])) {

            $data['where_in'] = array();
        }

        $data['where_in'][] = array($prefix . $this->_table_id_column, $ids);

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
        if (!$this->_table) {

            show_error(get_called_class() . '::get_by_slug() Table variable not set');

        } else {

            $prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
        }

        // --------------------------------------------------------------------------

        if (!isset($data['where'])) {

            $data['where'] = array();
        }

        $data['where'][] = array($prefix . $this->_table_slug_column, $slug);

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
        if (!$this->_table) {

            show_error(get_called_class() . '::get_by_slug() Table variable not set');

        } else {

            $prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
        }

        // --------------------------------------------------------------------------

        if (!isset($data['where_in'])) {

            $data['where_in'] = array();
        }

        $data['where_in'][] = array($prefix . $this->_table_slug_column, $slugs);

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
        if (!$this->_table) {

            show_error(get_called_class() . '::count_all() Table variable not set');

        } else {

            $table  = $this->_table_prefix ? $this->_table . ' ' . $this->_table_prefix : $this->_table;
        }

        // --------------------------------------------------------------------------

        //  Apply common items
        $this->_getcount_common($data, 'COUNT_ALL');

        // --------------------------------------------------------------------------

        //  If non-destructive delete is enabled then apply the delete query
        if (!$this->_destructive_delete && !$includeDeleted) {

            $prefix = $this->_table_prefix ? $this->_table_prefix . '.' : '';
            $this->db->where($prefix . $this->_deleted_flag, false);
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
     * @param string $table    The table to use defaults to $this->_table
     * @param string $column   The column to use, defaults to $this->_table_slug_column
     * @param int    $ignoreId An ID to ignore when searching
     * @param string $idColumn The column to use for the ID, defaults to $this->_table_id_column
     * @return string
     */
    protected function _generate_slug($label, $prefix = '', $suffix = '', $table = null, $column = null, $ignoreId = null, $idColumn = null)
    {
        //  Perform this check here so the error message is more easily traced.
        if (is_null($table)) {

            if (!$this->_table) {

                show_error(get_called_class() . '::_generate_slug() Table variable not set');
            }

            $table = $this->_table;

        } else {

            $table = $table;
        }

        if (is_null($column)) {

            if (!$this->_table_slug_column) {

                show_error(get_called_class() . '::_generate_slug() Column variable not set');
            }

            $column = $this->_table_slug_column;

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

                $_id_column = $idColumn ? $idColumn : $this->_table_id_column;
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
     * correctly format the output. Use this to typecast ID's and/or organise data into objects.
     *
     * @param object $obj A reference to the object being formatted.
     * @return void
     */
    protected function _format_object(&$obj)
    {
        //  extended this method to format the returned objects

        // --------------------------------------------------------------------------

        //  Some common items
        if ($this->_table_id_column) {

            if (!empty($obj->{$this->_table_id_column}) && is_numeric($obj->{$this->_table_id_column})) {

                $obj->{$this->_table_id_column} = (int) $obj->{$this->_table_id_column};
            }
        }

        if (!empty($obj->parent_id) && is_numeric($obj->parent_id)) {

            $obj->parent_id = (int) $obj->parent_id;
        }

        if (!empty($obj->user_id) && is_numeric($obj->user_id)) {

            $obj->user_id = (int) $obj->user_id;
        }

        if (!empty($obj->created_by) && is_numeric($obj->created_by)) {

            $obj->created_by = (int) $obj->created_by;
        }

        if (!empty($obj->modified_by) && is_numeric($obj->modified_by)) {

            $obj->modified_by = (int) $obj->modified_by;
        }

        if (!empty($obj->order) && is_numeric($obj->order)) {

            $obj->order = (int) $obj->order;
        }

        if (isset($obj->is_active)) {

            $obj->is_active = (bool) $obj->is_active;
        }

        if (isset($obj->is_deleted)) {

            $obj->is_deleted = (bool) $obj->is_deleted;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $_table
     * @return string
     */
    public function getTableName()
    {
        return $this->_table;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $_table_prefix
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->_table_prefix;
    }
}
