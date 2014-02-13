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

		$this->_nails_templates_dir	= NAILS_PATH . 'modules/cms/templates/';
		$this->_app_templates_dir	= FCPATH . APPPATH . 'modules/cms/templates/';

		$this->_nails_widgets_dir	= NAILS_PATH . 'modules/cms/widgets/';
		$this->_app_widgets_dir		= FCPATH . APPPATH . 'modules/cms/widgets/';

		$this->_nails_prefix		= 'NAILS_CMS_';
		$this->_app_prefix			= 'CMS_';

		$this->_table				= NAILS_DB_PREFIX . 'cms_page';
		$this->_table_prefix		= 'p';

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

				$_data->slug			= $this->_generate_slug( $data->title, $_parent->slug . '/' );

				//	Do it like this as _generate_slug() may have added some numbers or something after (i.e. can't use url_title())
				$_data->slug_end		= preg_replace( '#^' . str_replace( '-', '\-', $_parent->slug ) . '/#', '', $_data->slug );
				$_data->title_nested	= $_parent->title_nested . '|' . $_data->title;

			else :

				//	No parent, slug is just the title
				$_data->slug			= $this->_generate_slug( $data->title );
				$_data->slug_end		= $_data->slug;
				$_data->title_nested	= $_data->title;

			endif;

		else :

			//	No parent, slug is just the title
			$_data->slug			= $this->_generate_slug( $data->title );
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
			$this->load->model( 'system/routes_model' );
			if ( $this->routes_model->update( 'cms' ) ) :

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
			$this->load->model( 'system/routes_model' );
			if ( $this->db->trans_status() !== FALSE && ! $_rollback && $this->routes_model->update( 'cms' ) ) :

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


	public function _getcount_common( $data = array(), $_caller = NULL )
	{
		$this->db->select( $this->_table_prefix . '.*' );
		$this->db->select( 'ue.email, u.first_name, u.last_name, u.profile_img, u.gender' );

		$this->db->join( NAILS_DB_PREFIX . 'user u', 'u.id = ' . $this->_table_prefix . '.modified_by', 'LEFT' );
		$this->db->join( NAILS_DB_PREFIX . 'user_email ue', 'ue.user_id = u.id AND ue.is_primary = 1', 'LEFT' );

		$this->db->order_by( $this->_table_prefix . '.published_slug' );
		$this->db->order_by( $this->_table_prefix . '.draft_slug' );
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

			$_out[$page->id] = $this->_find_parents( $page->draft->parent_id, $_pages, $separator ) . $page->draft->title;

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


	protected function _format_object( &$page )
	{
		parent::_format_object( $page );

		$page->is_published		= (bool) $page->is_published;
		$page->is_deleted		= (bool) $page->is_deleted;


		//	Loop properties and sort into published data and draft data
		$page->published	= new stdClass();
		$page->draft		= new stdClass();

		foreach ( $page AS $property => $value ) :

			preg_match( '/^(published|draft)_(.*)$/', $property, $_match );

			if ( ! empty( $_match[1] ) && ! empty( $_match[2]) && $_match[1] == 'published' ) :

				$page->published->{$_match[2]} = $value;
				unset($page->{$property});

			elseif ( ! empty( $_match[1] ) && ! empty( $_match[2]) && $_match[1] == 'draft' ) :

				$page->draft->{$_match[2]} = $value;
				unset($page->{$property});

			endif;

		endforeach;


		//	Other data
		$page->published->depth		= count( explode( '/', $page->published->slug ) ) - 1;
		$page->published->url		= site_url( $page->published->slug );
		$page->draft->depth			= count( explode( '/', $page->draft->slug ) ) - 1;
		$page->draft->url			= site_url( $page->draft->slug );

		//	Decode JSON
		$page->published->template_data	= json_decode( $page->published->template_data );
		$page->draft->template_data		= json_decode( $page->draft->template_data );

		// --------------------------------------------------------------------------

		//	Owner
		$_modified_by					= (int) $page->modified_by;
		$page->modified_by				= new stdClass();
		$page->modified_by->id			= $_modified_by;
		$page->modified_by->first_name	= $page->first_name;
		$page->modified_by->last_name	= $page->last_name;
		$page->modified_by->email		= $page->email;
		$page->modified_by->profile_img	= $page->profile_img;
		$page->modified_by->gender		= $page->gender;

		unset( $page->first_name );
		unset( $page->last_name );
		unset( $page->email );
		unset( $page->profile_img );
		unset( $page->gender );
		unset( $page->template_data );
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

		$_nails_widgets	= array();
		$_app_widgets	= array();

		//	Look for nails widgets
		$_nails_widgets = directory_map( $this->_nails_widgets_dir );

		//	Look for app widgets
		if ( is_dir( $this->_app_widgets_dir ) ) :

			$_app_widgets = directory_map( $this->_app_widgets_dir );

		endif;

		// --------------------------------------------------------------------------

		//	Test and merge widgets
		$_widgets = array();
		foreach( $_nails_widgets AS $widget => $details ) :

			//	Ignore base template
			if ( $details == '_widget.php' ) :

				continue;

			endif;

			//	Ignore malformed widgets
			if ( ! is_array( $details ) || array_search( 'widget.php', $details ) === FALSE ) :

				log_message( 'error', 'Ignoring malformed NAILS CMS Widget "' . $widget . '"' );
				continue;

			endif;

			//	Ignore widgets which have an app override
			if ( isset( $_app_widgets[$widget] ) && is_array( $_app_widgets[$widget] ) ) :

				continue;

			endif;

			// --------------------------------------------------------------------------

			include_once $this->_nails_widgets_dir . $widget . '/widget.php';

			//	Can we call the static details method?
			$_class = $this->_nails_prefix . 'Widget_' . $widget;

			if ( ! class_exists( $_class ) || ! method_exists( $_class, 'details' ) ) :

				log_message( 'error', 'Cannot call static method "details()" on  NAILS CMS Widget: "' . $widget . '"' );
				continue;

			endif;

			$_details = $_class::details();

			if ( $_details ) :

				$_widgets[$widget] = $_class::details();

			endif;

		endforeach;

		//	Now test app widgets
		foreach( $_app_widgets AS $widget => $details ) :

			//	Ignore malformed widgets
			if ( ! is_array( $details ) || array_search( 'widget.php', $details ) === FALSE ) :

				log_message( 'error', 'Ignoring malformed APP CMS Widget "' . $widget . '"' );
				continue;

			endif;

			// --------------------------------------------------------------------------

			include_once $this->_app_widgets_dir . $widget . '/widget.php';

			//	Can we call the static details method?
			$_class	= $this->_app_prefix . 'Widget_' . $widget;

			if ( ! class_exists( $_class ) || ! method_exists( $_class, 'details' ) ) :

				log_message( 'error', 'Cannot call static method "details()" on  APP CMS Widget: "' . $widget . '"' );
				continue;

			endif;

			$_widgets[$widget] = $_class::details();

		endforeach;

		// --------------------------------------------------------------------------

		//	Sort the widgets into their sub groupings and then alphabetically
		$_out						= array();
		$_generic_widgets			= array();
		$_generic_widget_grouping	= 'Generic';

		foreach ( $_widgets AS $w ) :

			if ( $w->grouping ) :

				$_key = md5( $w->grouping );

				if ( ! isset( $_out[$_key] ) ) :

					$_out[$_key]			= new stdClass();
					$_out[$_key]->label		= $w->grouping;
					$_out[$_key]->widgets	= array();

				endif;

				$_out[$_key]->widgets[] = $w;

			else :

				$_key = md5( $_generic_widget_grouping );

				if ( ! isset( $_generic_widgets[$_key] ) ) :

					$_generic_widgets[$_key]			= new stdClass();
					$_generic_widgets[$_key]->label		= $_generic_widget_grouping;
					$_generic_widgets[$_key]->widgets	= array();

				endif;

				$_generic_widgets[$_key]->widgets[] = $w;

			endif;

		endforeach;

		//	Sort non-generic widgets into alphabetical order
		foreach( $_out AS $o ) :

			usort( $o->widgets, array( $this, '_sort_widgets' ) );

		endforeach;

		//	Sort generic
		usort( $_generic_widgets[md5( $_generic_widget_grouping )]->widgets, array( $this, '_sort_widgets' ) );

		//	Sort the non-generic groupings
		usort( $_out, function( $a, $b ) use ( $_generic_widget_grouping )
		{
			//	Equal?
			if ( trim( $a->label ) == trim( $b->label ) ) :

				return 0;

			endif;

			//	Not equal, work out which takes precedence
			$_sort = array( $a->label, $b->label );
			sort( $_sort );

			return $_sort[0] == $a->label ? -1 : 1;

		});

		//	Glue generic groupings to the beginning of the array
		$_out = array_merge( $_generic_widgets, $_out );

		// --------------------------------------------------------------------------

		//	Save to the cache
		$this->_set_cache( $_key, $_widgets );

		// --------------------------------------------------------------------------

		return array_values( $_out );
	}


	// --------------------------------------------------------------------------


	protected function _sort_widgets( $a, $b )
	{
		//	Equal?
		if ( trim( $a->label ) == trim( $b->label ) ) :

			return 0;

		endif;

		//	Not equal, work out which takes precedence
		$_sort = array( $a->label, $b->label );
		sort( $_sort );

		return $_sort[0] == $a->label ? -1 : 1;
	}


	// --------------------------------------------------------------------------


	public function get_widget( $slug )
	{
		$_widgets = $this->get_available_widgets();

		foreach ( $_widgets AS $widget_group ) :

			foreach ( $widget_group->widgets AS $widget ) :

				if ( $slug == $widget->slug ) :

					return $widget;

				endif;

			endforeach;

		endforeach;

		return FALSE;
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

		$_nails_templates	= array();
		$_app_templates		= array();

		//	Look for nails widgets
		$_nails_templates = directory_map( $this->_nails_templates_dir );

		//	Look for app widgets
		if ( is_dir( $this->_app_templates_dir ) ) :

			$_app_templates = directory_map( $this->_app_templates_dir );

		endif;

		// --------------------------------------------------------------------------

		//	Test and merge templates
		$_templates = array();
		foreach( $_nails_templates AS $template => $details ) :

			//	Ignore base template
			if ( $details == '_template.php' ) :

				continue;

			endif;

			//	Ignore malformed templates
			if ( ! is_array( $details ) || array_search( 'template.php', $details ) === FALSE ) :

				log_message( 'error', 'Ignoring malformed NAILS CMS Template "' . $template . '"' );
				continue;

			endif;

			//	Ignore templates which have an app override
			if ( isset( $_app_templates[$template] ) && is_array( $_app_templates[$template] ) ) :

				continue;

			endif;

			// --------------------------------------------------------------------------

			include_once $this->_nails_templates_dir . $template . '/template.php';

			//	Can we call the static details method?
			$_class = $this->_nails_prefix . 'Template_' . $template;

			if ( ! class_exists( $_class ) || ! method_exists( $_class, 'details' ) ) :

				log_message( 'error', 'Cannot call static method "details()" on  NAILS CMS Template: "' . $template . '"' );
				continue;

			endif;

			$_details = $_class::details();

			if ( $_details ) :

				$_templates[$template] = $_class::details();

			endif;

		endforeach;

		//	Now test app templates
		foreach( $_app_templates AS $template => $details ) :

			//	Ignore malformed templates
			if ( ! is_array( $details ) || array_search( 'template.php', $details ) === FALSE ) :

				log_message( 'error', 'Ignoring malformed APP CMS Template "' . $template . '"' );
				continue;

			endif;

			// --------------------------------------------------------------------------

			include_once $this->_app_templates_dir . $template . '/template.php';

			//	Can we call the static details method?
			$_class = $this->_app_prefix . 'Template_' . $template;

			if ( ! class_exists( $_class ) || ! method_exists( $_class, 'details' ) ) :

				log_message( 'error', 'Cannot call static method "details()" on  NAILS CMS Template: "' . $template . '"' );
				continue;

			endif;

			$_templates[$template] = $_class::details();

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


	public function get_template( $slug )
	{
		$_templates = $this->get_available_templates();

		foreach ( $_templates AS $template ) :

			if ( $slug == $template->slug ) :

				return $widget;

			endif;

		endforeach;

		return FALSE;
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