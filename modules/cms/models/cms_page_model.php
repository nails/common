<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:			cms_page_model.php
 *
 * Description:		This model handles everything to do with CMS pages
 *
 **/

/**
 * OVERLOADING NAILS' MODELS
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Cms_page_model extends NAILS_Model
{
	protected $_routes_dir;
	protected $_available_widgets;
	protected $_nails_templates_dir;
	protected $_app_templates_dir;
	protected $_nails_widgets_dir;
	protected $_app_widgets_dir;
	protected $_nails_prefix;
	protected $_app_prefix;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_routes_dir			= FCPATH . APPPATH . 'config/';

		$this->_nails_templates_dir	= NAILS_PATH . 'modules/cms/templates/';
		$this->_app_templates_dir		= FCPATH . APPPATH . 'modules/cms/templates/';

		$this->_nails_widgets_dir	= NAILS_PATH . 'modules/cms/widgets/';
		$this->_app_widgets_dir		= FCPATH . APPPATH . 'modules/cms/widgets/';

		$this->_nails_prefix		= 'NAILS_CMS_';
		$this->_app_prefix			= 'CMS_';

		$this->_table				= NAILS_DB_PREFIX . 'cms_page';

		// --------------------------------------------------------------------------

		//	Load the generic template & widget
		include_once $this->_nails_templates_dir . '_template.php';
		include_once $this->_nails_widgets_dir . '_widget.php';
	}


	// --------------------------------------------------------------------------


	public function create( $data, $return_obj = FALSE )
	{
		$_data = new stdClass();

		if ( isset( $data->title ) ) :

			$_data->title = strip_tags( $data->title );

		else :

			$this->_set_error( 'Title is required.' );
			return FALSE;

		endif;

		if ( isset( $data->parent_id ) ) :

			$_data->parent_id = ! empty( $data->parent_id ) ? (int) $data->parent_id : NULL;

			//	Work out the slug, prefix it with nested parents
			$_parent = $this->get_by_id( $_data->parent_id );

			if ( $_data->parent_id && ! $_parent ) :

				$this->_set_error( 'Invalid Parent ID.' );
				return FALSE;

			endif;

			if ( $_data->parent_id ) :

				$_data->slug			= $this->_generate_slug( $data->title, $this->_table, 'slug', $_parent->slug . '/' );

				//	Do it like this as _generate_slug() may have added some numbers or something after (i.e. can't use url_title())
				$_data->slug_end		= preg_replace( '#^' . str_replace( '-', '\-', $_parent->slug ) . '/#', '', $_data->slug );
				$_data->title_nested	= $_parent->title_nested . '|' . $_data->title;

			else :

				//	No parent, slug is just the title
				$_data->slug			= $this->_generate_slug( $data->title, $this->_table, 'slug' );
				$_data->slug_end		= $_data->slug;
				$_data->title_nested	= $_data->title;

			endif;

		else :

			//	No parent, slug is just the title
			$_data->slug			= $this->_generate_slug( $data->title, $this->_table, 'slug' );
			$_data->title_nested	= $_data->title;

		endif;

		if ( isset( $data->seo_description ) ) :

			$_data->seo_description = strip_tags( $data->seo_description );

		endif;

		if ( isset( $data->seo_keywords ) ) :

			$_data->seo_keywords = strip_tags( $data->seo_keywords );

		endif;

		// --------------------------------------------------------------------------

		$_return = parent::create( $_data, $return_obj );

		if ( $_return ) :

			//	Rewrite the routes file
			if ( $this->write_routes() ) :

				return $_return;

			else :

				$_id = $return_obj ? $_return->id : $_return;
				$this->destroy( $_id );
				return FALSE;

			endif;

		else :

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function update( $page_id, $data )
	{
		//	Firstly, remove and remember the widgets, if any.
		$_areas = array( 'hero', 'body', 'sidebar' );

		foreach ( $_areas AS $area ) :

			if ( isset( $data->{'widgets_' . $area} ) ) :

				${'_widgets_' . $area} = $data->{'widgets_' . $area};
				unset( $data->{'widgets_' . $area} );

			endif;

		endforeach;

		//	Next, check the slug is unique, encode it to be safe
		if ( isset( $data->slug ) ) :

			$data->slug = explode( '/', trim( $data->slug ) );
			foreach ( $data->slug AS &$value ) :

				$value = url_title( $value, 'dash', TRUE );

			endforeach;
			$data->slug = implode( '/', $data->slug );

			$this->db->where( 'id !=', $page_id );
			$this->db->where( 'slug', $data->slug );

			if ( $this->db->count_all_results( NAILS_DB_PREFIX . 'cms_page' ) ) :

				$this->_set_error( 'Slug must be unique.' );
				return FALSE;

			endif;

		endif;

		// --------------------------------------------------------------------------

		//	Start the transaction
		$this->db->trans_begin();
		$_rollback = FALSE;

		//	Turn off DB Errors
		$_previous = $this->db->db_debug;
		$this->db->db_debug = FALSE;

		//	Update the page's meta data if needed
		$this->db->set( $data );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( active_user( 'id' ) ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		$this->db->where( 'id', $page_id );

		if ( $this->db->update( NAILS_DB_PREFIX . 'cms_page' ) ) :

			//	Are there any widgets which need updating? If not then we're done
			foreach ( $_areas AS $area ) :

				if ( isset( ${'_widgets_' . $area} ) && is_array( ${'_widgets_' . $area} ) ) :

					//	Loop through the $_widgets array, update any `old-` widgets, add `new-` widgets,
					//	remove widgets which aren't provided and then save the order.

					$_order				= 0;
					$_processed_widgets	= array();

					foreach ( ${'_widgets_' . $area} AS $key => $widget ) :

						//	Prepare and set data
						$_type = $widget['slug'];
						unset($widget['slug']);

						$this->db->set( 'order', $_order );
						$this->db->set( 'widget_data', serialize( $widget ) );
						$this->db->set( 'modified', 'NOW()', FALSE );

						if ( active_user( 'id' ) ) :

							$this->db->set( 'modified_by', active_user( 'id' ) );

						endif;

						// --------------------------------------------------------------------------

						//	Old or new?
						$key = explode( '-', $key );

						if ( $key[0] == 'old' ) :

							//	Old widget, update
							$this->db->where( 'id', $key[1] );

							if ( $this->db->update( NAILS_DB_PREFIX . 'cms_page_widget' ) ) :

								$_processed_widgets[] = $key[1];

							else :

								$_rollback = TRUE;
								$this->_set_error( 'Unable to update widget ID:' . $key[1] );
								break;

							endif;

						elseif ( $key[0] == 'new' ) :

							//	New widget, insert
							$this->db->set( 'page_id', $page_id );
							$this->db->set( 'widget_class', $_type );
							$this->db->set( 'widget_area', $area );
							$this->db->set( 'created', 'NOW()', FALSE );

							if ( active_user( 'id' ) ) :

								$this->db->set( 'created_by', active_user( 'id' ) );

							endif;

							if ( $this->db->insert( NAILS_DB_PREFIX . 'cms_page_widget' ) ) :

								$_processed_widgets[] = $this->db->insert_id();

							else :

								$_rollback = TRUE;
								$this->_set_error( 'Unable to create widget TYPE:' . $_type );
								break;

							endif;

						else :

							//	Que?
							$this->_set_error( 'An unknown error occurred while processing widgets.' );
							$_rollback = TRUE;
							break;

						endif;

						$_order++;

					endforeach;

					// --------------------------------------------------------------------------

					//	Remove old widgets (i.e widgets which were not processed)
					$this->db->where( 'page_id', $page_id );
					$this->db->where( 'widget_area', $area );
					$this->db->where_not_in( 'id', $_processed_widgets );
					if ( ! $this->db->delete( NAILS_DB_PREFIX . 'cms_page_widget' ) ) :

						$this->_set_error( 'Unable to remove old widgets.' );
						$_rollback = TRUE;
						break;

					endif;

				endif;

			endforeach;

			// --------------------------------------------------------------------------

			//	Update the routes file
			if ( $this->db->trans_status() !== FALSE && ! $_rollback && $this->write_routes() ) :

				//	Commit changes
				$this->db->trans_commit();

				//	Put DB errors back as they were
				$this->db->db_debug = $_previous;

				return TRUE;

			else :

				//	Rollback changes
				$this->db->trans_rollback();

				//	Put DB errors back as they were
				$this->db->db_debug = $_previous;

				return FALSE;

			endif;

		else :

			//	Rollback changes
			$this->_set_error( 'Could not update page.' );
			$this->db->trans_rollback();

			//	Put DB errors back as they were
			$this->db->db_debug = $_previous;

			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------



	public function delete( $id )
	{
		$_data = array( 'is_deleted' => TRUE );
		return $this->update( $id, $_data );
	}


	// --------------------------------------------------------------------------


	public function restore( $id )
	{
		$_data = array( 'is_deleted' => FALSE );
		return $this->update( $id, $_data );
	}


	// --------------------------------------------------------------------------


	public function destroy( $id )
	{
		$this->db->where( 'id', $id );
		$this->db->delete( $this->_table );

		return (bool) $this->db->affected_rows();
	}


	// --------------------------------------------------------------------------


	public function get_all( $include_deleted = FALSE )
	{
		$this->db->select( 'p.id,p.slug,p.slug_end,p.parent_id,p.template,p.template_data,p.title,p.title_nested,p.is_published,p.is_deleted,p.seo_description,p.seo_keywords,p.created,p.modified,p.modified_by' );
		$this->db->select( 'ue.email, u.first_name, u.last_name, u.profile_img, u.gender' );

		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = p.modified_by', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );

		if ( ! $include_deleted ) :

			$this->db->where( 'p.is_deleted', FALSE );

		endif;

		$this->db->order_by( 'p.slug' );
		$_pages = $this->db->get( NAILS_DB_PREFIX . 'cms_page p' )->result();

		foreach ( $_pages AS $page ) :

			//	Format the page object
			$this->_format_page_object( $page );

		endforeach;

		return $_pages;
	}


	// --------------------------------------------------------------------------


	public function get_all_nested()
	{
		return $this->_nest_pages( $this->get_all() );
	}


	// --------------------------------------------------------------------------


	/**
	 *	Hat tip to Timur; http://stackoverflow.com/a/9224696/789224
	 **/
	protected function _nest_pages( &$list, $parent = NULL )
	{
		$result = array();

		for ( $i = 0, $c = count( $list ); $i < $c; $i++ ) :

			if ( $list[$i]->parent_id == $parent ) :

				$list[$i]->children	= $this->_nest_pages( $list, $list[$i]->id );
				$result[]			= $list[$i];

			endif;

		endfor;

		return $result;
	}


	// --------------------------------------------------------------------------


	public function get_all_nested_flat( $separator = ' &rsaquo; ', $murder_parents_of_children = TRUE )
	{
		$_out	= array();
		$_pages	= $this->get_all();

		foreach ( $_pages AS $page ) :

			$_out[$page->id] = $this->_find_parents( $page->parent_id, $_pages, $separator ) . $page->title;

		endforeach;

		asort( $_out );

		// --------------------------------------------------------------------------

		//	Remove parents from the array if they have any children
		if ( $murder_parents_of_children ) :

			foreach( $_out AS $key => &$page ) :

				$_found		= FALSE;
				$_needle	= $page . $separator;

				//	Hat tip - http://uk3.php.net/manual/en/function.array-search.php#90711
				foreach ( $_out as $item ) :

					if ( strpos( $item, $_needle ) !== FALSE ) :

						$_found = TRUE;
						break;

					endif;

				endforeach;

				if ( $_found ) :

					unset( $_out[$key] );

				endif;

			endforeach;

		endif;

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _find_parents( $parent_id, &$source, $separator )
	{
		if ( ! $parent_id ) :

			//	No parent ID, end of the line señor!
			return '';

		else :

			//	There is a parent, look for it
			foreach ( $source AS $src ) :

				if ( $src->id == $parent_id ) :

					$_parent = $src;

				endif;

			endforeach;

			if ( isset( $_parent ) && $_parent ) :

				//	Parent was found, does it have any parents?
				if ( $_parent->parent_id ) :

					//	Yes it does, repeat!
					$_return = $this->_find_parents( $_parent->parent_id, $source, $separator );

					return $_return ? $_return . $_parent->title . $separator : $_parent->title;

				else :

					//	Nope, end of the line mademoiselle
					return $_parent->title . $separator;

				endif;


			else :

				//	Did not find parent, give up.
				return '';

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_all_flat()
	{
		$_out	= array();
		$_pages	= $this->get_all();

		foreach( $_pages AS $page ) :

			$_out[$page->id] = $page->title;

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	protected function _format_page_object( &$page )
	{
		$page->id				= (int) $page->id;
		$page->is_published		= (bool) $page->is_published;
		$page->is_deleted		= (bool) $page->is_deleted;
		$page->depth			= count( explode( '/', $page->slug ) ) - 1;

		$_template_slug			= $page->template;
		$page->template			= new stdClass();
		$page->template->slug	= $_template_slug;
		$page->template->data	= unserialize( $page->template_data );

		// --------------------------------------------------------------------------

		//	Owner
		$page->user					= new stdClass();
		$page->user->id				= (int) $page->modified_by;
		$page->user->first_name		= $page->first_name;
		$page->user->last_name		= $page->last_name;
		$page->user->email			= $page->email;
		$page->user->profile_img	= $page->profile_img;
		$page->user->gender			= $page->gender;

		unset( $page->modified_by );
		unset( $page->first_name );
		unset( $page->last_name );
		unset( $page->email );
		unset( $page->profile_img );
		unset( $page->gender );
		unset( $page->template_data );
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's ID
	 *
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @return stdClass
	 **/
	public function get_by_id( $id )
	{
		$this->db->where( 'p.id', $id );
		$_result = $this->get_all( TRUE );

		// --------------------------------------------------------------------------

		if ( ! $_result ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return $_result[0];
	}


	// --------------------------------------------------------------------------


	/**
	 * Fetch an object by it's slug
	 *
	 * @access public
	 * @param string $slug The slug of the object to fetch
	 * @return stdClass
	 **/
	public function get_by_slug( $slug )
	{
		$this->db->where( 'p.slug', $slug );
		$_result = $this->get_all( TRUE );

		// --------------------------------------------------------------------------

		if ( ! $_result ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		return $_result[0];
	}


	// --------------------------------------------------------------------------


	public function get_available_widgets()
	{
		//	Have we done this already? Don't do it again.
		$_key	= 'cms-page-available-widgets';
		$_cache	= $this->_get_cache( $_key );

		if ( $_cache ) :

			return $_cache;

		endif;

		// --------------------------------------------------------------------------

		//	Search the Nails. widget folder, and then the App's widget folder.
		//	Widgets in the app folder trump widgets in the Nails folder

		$this->load->helper( 'directory' );

		//	Look for nails widgets
		$_nails_widgets = directory_map( $this->_nails_widgets_dir );

		//	Look for app widgets
		if ( is_dir( $this->_app_widgets_dir ) ) :

			$_app_widgets = directory_map( $this->_app_widgets_dir );

		endif;

		// --------------------------------------------------------------------------

		//	Sanitise
		if ( empty( $_nails_widgets ) ) :

			$_nails_widgets = array();

		endif;

		if ( empty( $_app_widgets ) ) :

			$_app_widgets = array();

		endif;

		// --------------------------------------------------------------------------

		//	Test and merge widgets
		$_widgets = array();
		foreach( $_nails_widgets AS $widget ) :

			//	Ignore the base widget
			if ( $widget == '_widget.php' ) :

				continue;

			endif;

			// --------------------------------------------------------------------------

			include_once $this->_nails_widgets_dir . $widget;

			//	Can we call the static details method?
			$_widget	= ucfirst( substr( $widget, 0, strrpos( $widget, '.' ) ) );
			$_class		= $this->_nails_prefix . $_widget;

			if ( ! method_exists( $_class, 'details' ) ) :

				continue;

			endif;

			$_details = $_class::details();

			if ( $_details ) :

				$_widgets[$_widget] = $_class::details();

			endif;

		endforeach;

		//	Now test app widgets
		foreach( $_app_widgets AS $widget ) :

			include_once $this->_app_widgets_dir . $widget;

			//	Can we call the static details method?
			$_widget	= ucfirst( substr( $widget, 0, strrpos( $widget, '.' ) ) );
			$_class		= $this->_app_prefix . $_widget;

			if ( ! method_exists( $_class, 'details' ) ) :

				continue;

			endif;

			$_widgets[$_widget] = $_class::details();

		endforeach;

		// --------------------------------------------------------------------------

		//	Sort into some alphabetical order
		ksort( $_widgets );

		// --------------------------------------------------------------------------

		//	Save to the cache
		$this->_set_cache( $_key, $_widgets );

		// --------------------------------------------------------------------------

		return $_widgets;
	}


	// --------------------------------------------------------------------------


	public function get_available_templates()
	{
		//	Have we done this already? Don't do it again.
		$_key	= 'cms-page-available-templates';
		$_cache	= $this->_get_cache( $_key );

		if ( $_cache ) :

			return $_cache;

		endif;

		// --------------------------------------------------------------------------

		//	Search the Nails. widget folder, and then the App's widget folder.
		//	Widgets in the app folder trump widgets in the Nails folder

		$this->load->helper( 'directory' );

		//	Look for nails widgets
		$_nails_templates = directory_map( $this->_nails_templates_dir );

		//	Look for app widgets
		if ( is_dir( $this->_app_templates_dir ) ) :

			$_app_templates = directory_map( $this->_app_templates_dir );

		endif;

		// --------------------------------------------------------------------------

		//	Sanitise
		if ( empty( $_nails_templates ) ) :

			$_nails_templates = array();

		endif;

		if ( empty( $_app_templates ) ) :

			$_app_templates = array();

		endif;

		// --------------------------------------------------------------------------

		//	Test and merge templates
		$_templates = array();
		foreach( $_nails_templates AS $template ) :

			//	Ignore the base template
			if ( $template == '_template.php' ) :

				continue;

			endif;

			// --------------------------------------------------------------------------

			include_once $this->_nails_templates_dir . $template;

			//	Can we call the static details method?
			$_template	= ucfirst( substr( $template, 0, strrpos( $template, '.' ) ) );
			$_class		= $this->_nails_prefix . $_template;

			if ( ! method_exists( $_class, 'details' ) ) :

				continue;

			endif;

			$_details = $_class::details();

			if ( $_details ) :

				$_templates[$_template] = $_class::details();

			endif;

		endforeach;

		//	Now test app templates
		foreach( $_app_templates AS $template ) :

			include_once $this->_app_templates_dir . $template;

			//	Can we call the static details method?
			$_template	= ucfirst( substr( $template, 0, strrpos( $template, '.' ) ) );
			$_class		= $this->_app_prefix . $template;

			if ( ! method_exists( $_class, 'details' ) ) :

				continue;

			endif;

			$_templates[$_template] = $_class::details();

		endforeach;

		// --------------------------------------------------------------------------

		//	Sort into some alphabetical order
		ksort( $_templates );

		// --------------------------------------------------------------------------

		//	Save to the cache
		$this->_set_cache( $_key, $_templates );

		// --------------------------------------------------------------------------

		return $_templates;
	}


	// --------------------------------------------------------------------------


	public function render( $page )
	{
		//	Loop through all the widgets, instantiate the appropriate widget and execute
		//	it's render function, append the result to the $_out variable and spit that back

		$_out	= array( 'hero' => '', 'body' => '', 'sidebar' => '' );
		$_area	= array( 'hero', 'body', 'sidebar' );

		foreach ($_area AS $area ) :

			foreach ( $page->{'widgets_' . $area} AS $key => $widget ) :

				$_out[$area] .= '<div class="widget ' . $widget->widget_class . '">';
				$_out[$area] .= $this->_call_widget_method( $widget->widget_class, $widget->widget_data, 'render' );
				$_out[$area] .= '</div>';

			endforeach;

		endforeach;

		// --------------------------------------------------------------------------

		return $_out;
	}


	// --------------------------------------------------------------------------


	public function get_widget_editor( $widget, $data = NULL, $key = NULL )
	{
		if ( NULL !== $key ) :

			$data = unserialize( $data );

			if ( ! $data ) :

				$data = array();

			else :

				$_data = (array) $data;

			endif;

			$data['key'] = $key;
			$data = serialize( $data );

		endif;

		// --------------------------------------------------------------------------

		return $this->_call_widget_method( $widget, $data, 'get_editor_html' );
	}


	// --------------------------------------------------------------------------


	public function get_widget_editor_functions( $widget, $data = NULL, $key = NULL )
	{
		if ( NULL !== $key ) :

			$data = unserialize( $data );

			if ( ! $data ) :

				$data = array();

			else :

				$_data = (array) $data;

			endif;

			$data['key'] = $key;
			$data = serialize( $data );

		endif;

		// --------------------------------------------------------------------------

		return $this->_call_widget_method( $widget, $data, 'get_editor_functions' );
	}


	// --------------------------------------------------------------------------


	public function get_widget_validation_rules( $widget, $field )
	{
		return $this->_call_widget_method( $widget, NULL, 'get_validation_rules', array( 'field' => $field ) );
	}


	// --------------------------------------------------------------------------


	protected function _call_widget_method( $widget, $data, $method, $params = array() )
	{
		//	Load up widget classes
		$_class		= strtolower( $widget );
		$_has_nails	= FALSE;
		$_has_app	= FALSE;

		//	Nails
		if ( file_exists( $this->_nails_widgets_dir . $_class . '.php' ) ) :

			include_once $this->_nails_widgets_dir . $_class . '.php';
			$_has_nails = TRUE;

		endif;

		//	App
		if ( file_exists( $this->_app_widgets_dir . $_class . '.php' ) ) :

			include_once $this->_app_widgets_dir . $_class . '.php';
			$_has_app = TRUE;

		endif;

		// --------------------------------------------------------------------------

		//	Instanciate the widget
		if ( $_has_app && class_exists( $this->_app_prefix . $_class ) ) :

			$_class = $this->_app_prefix . $_class;

		elseif( $_has_nails && class_exists( $this->_nails_prefix . $_class ) ) :

			$_class = $this->_nails_prefix . $_class;

		else :

			$_class = NULL;

		endif;

		if ( $_class && method_exists( $_class, $method ) ) :

			$_temp = new $_class();
			$_temp->setup( unserialize( $data ) );
			$_result = call_user_func_array( array( $_temp, $method ), $params );
			unset( $_temp );

			return $_result;

		endif;
	}

	// --------------------------------------------------------------------------


	public function can_write_routes()
	{
		//	First, test if file exists, if it does is it writable?
		if ( file_exists( $this->_routes_dir . 'routes_cms_page.php' ) ) :

			if ( is_really_writable( $this->_routes_dir . 'routes_cms_page.php' ) ) :

				return TRUE;

			else :

				//	Attempt to chmod the file
				if ( @chmod( $this->_routes_dir . 'routes_cms_page.php', FILE_WRITE_MODE ) ) :

					return TRUE;

				else :

					$this->_set_error( 'The route config exists, but is not writeable. <small>Located at: ' . $this->_routes_dir . 'routes_cms_page.php</small>' );
					return FALSE;

				endif;

			endif;

		elseif ( is_really_writable( $this->_routes_dir ) ) :

			return TRUE;

		else :

			//	Attempt to chmod the directory
			if ( @chmod( $this->_routes_dir, DIR_WRITE_MODE ) ) :

				return TRUE;

			else :

				$this->_set_error( 'The route directory is not writeable. <small>' . $this->_routes_dir . '</small>' );
				return FALSE;

			endif;

		endif;
	}

	// --------------------------------------------------------------------------


	public function write_routes()
	{
		if ( ! $this->can_write_routes() ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Routes are writeable, apparently, give it a bash
		$_data = '<?php  if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . "\n\n";
		$_data .= '//	THIS FILE IS CREATED/MODIFIED AUTOMATICALLY, ANY MANUAL EDITS WILL BE OVERWRITTEN'."\n\n";

		$_pages = $this->get_all();

		foreach ( $_pages AS $page ) :

			$_data .= '$route[\'' . $page->slug . '\'] = \'cms/render/page/' . $page->id . '\';' . "\n";

		endforeach;

		$_fh = @fopen( $this->_routes_dir . 'routes_cms_page.php', 'w' );

		if ( ! $_fh ) :

			$this->_set_error( 'Unable to open routes file for writing.<small>Located at: ' . $this->_routes_dir . 'routes_cms_page.php</small>' );
			return FALSE;

		endif;

		if ( ! fwrite( $_fh, $_data ) ) :

			fclose( $_fh );
			$this->_set_error( 'Unable to write data to routes file.<small>Located at: ' . $this->_routes_dir . 'routes_cms_page.php</small>' );
			return FALSE;

		endif;

		fclose( $_fh );

		return TRUE;
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' MODELS
 *
 * The following block of code makes it simple to extend one of the core Nails
 * models. Some might argue it's a little hacky but it's a simple 'fix'
 * which negates the need to massively extend the CodeIgniter Loader class
 * even further (in all honesty I just can't face understanding the whole
 * Loader class well enough to change it 'properly').
 *
 * Here's how it works:
 *
 * CodeIgniter instantiate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclare class X' errors
 * and if we call our overloading class something else it will never get instantiated.
 *
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instantiated et voila.
 *
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CMS_PAGE_MODEL' ) ) :

	class Cms_page_model extends NAILS_Cms_page_model
	{
	}

endif;


/* End of file cms_page_model.php */
/* Location: ./models/cms_page_model.php */