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
		$this->_nails_widgets_dir	= NAILS_PATH . 'modules/cms/widgets/';
		$this->_app_widgets_dir		= FCPATH . APPPATH . 'modules/cms/widgets/';
		
		$this->_nails_prefix		= 'NAILS_CMS_';
		$this->_app_prefix			= 'CMS_';
		
		// --------------------------------------------------------------------------
		
		//	Load the generic widget
		include_once $this->_nails_widgets_dir . '_widget.php';
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function create()
	{
		//	TODO Create a new blank page
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
			
			if ( $this->db->count_all_results( 'cms_page' ) ) :
			
				$this->_set_error( 'Slug must be unique.' );
				return FALSE;
			
			endif;
		
		endif;
		
		//	Then update the page's meta data if needed
		$this->db->set( $data );
		$this->db->set( 'modified', 'NOW()', FALSE );
		
		if ( active_user( 'id' ) ) :
		
			$this->db->set( 'modified_by', active_user( 'id' ) );
		
		endif;
		
		$this->db->where( 'id', $page_id );
		
		if ( $this->db->update( 'cms_page' ) ) :
		
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
							
							$this->db->update( 'cms_page_widget' );
							
							$_processed_widgets[] = $key[1];
						
						elseif ( $key[0] == 'new' ) :
						
							//	New widget, insert
							$this->db->set( 'page_id', $page_id );
							$this->db->set( 'widget_class', $_type );
							$this->db->set( 'widget_area', $area );
							$this->db->set( 'created', 'NOW()', FALSE );
							
							if ( active_user( 'id' ) ) :
							
								$this->db->set( 'created_by', active_user( 'id' ) );
							
							endif;
							
							$this->db->insert( 'cms_page_widget' );
							
							$_processed_widgets[] = $this->db->insert_id();
						
						else :
						
							//	Que?
						
						endif;
					
						$_order++;
						
					endforeach;
					
					// --------------------------------------------------------------------------
					
					//	Remove old widgets (i.e widgets which were not processed)
					$this->db->where( 'page_id', $page_id );
					$this->db->where( 'widget_area', $area );
					$this->db->where_not_in( 'id', $_processed_widgets );
					$this->db->delete( 'cms_page_widget' );
				
				endif;

			endforeach;
			
			// --------------------------------------------------------------------------
			
			//	Update the routes file
			if ( $this->write_routes() ) :
			
				return TRUE;
			
			else :
			
				return FALSE;
			
			endif;
		
		else :
		
			$this->_set_error( 'Could not update page.' );
			return FALSE;
		
		endif;
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	
	public function delete()
	{
		//	TODO Delete a page
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_all( $include_widgets = FALSE, $include_deleted = FALSE )
	{
		$this->db->select( 'p.id,p.slug,p.title,p.layout,p.sidebar_width,p.seo_description,p.seo_keywords,p.created,p.modified,p.modified_by,p.is_deleted' );
		$this->db->select( 'u.email, u.first_name, u.last_name, u.profile_img, u.gender' );
		
		$this->db->join( 'user u', 'u.id = p.modified_by', 'LEFT' );
		
		if ( ! $include_deleted ) :
		
			$this->db->where( 'p.is_deleted', FALSE );
		
		endif;
		
		$this->db->order_by( 'p.title' );
		$_pages = $this->db->get( 'cms_page p' )->result();
		
		foreach ( $_pages AS $page ) :
		
			//	Format the page object
			$this->_format_page_object( $page );
			
			// --------------------------------------------------------------------------
			
			//	Fetch widgets
			if ( $include_widgets ) :
				
				$this->db->where( 'page_id', $page->id );
				$this->db->where( 'widget_area', 'hero' );
				$this->db->order_by( 'order' );
				$page->widgets_hero = $this->db->get( 'cms_page_widget' )->result();

				$this->db->where( 'page_id', $page->id );
				$this->db->where( 'widget_area', 'body' );
				$this->db->order_by( 'order' );
				$page->widgets_body = $this->db->get( 'cms_page_widget' )->result();

				$this->db->where( 'page_id', $page->id );
				$this->db->where( 'widget_area', 'sidebar' );
				$this->db->order_by( 'order' );
				$page->widgets_sidebar = $this->db->get( 'cms_page_widget' )->result();
				
			endif;
			
			// --------------------------------------------------------------------------
		
		endforeach;
		
		return $_pages;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	protected function _format_page_object( &$page )
	{
		$page->id				= (int) $page->id;
		$page->sidebar_width	= (int) $page->sidebar_width;
		$page->is_deleted		= (bool) $page->is_deleted;
		
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
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's ID
	 * 
	 * @access public
	 * @param int $id The ID of the object to fetch
	 * @param bool $include_revisions Whether to include translation revisions
	 * @return stdClass
	 **/
	public function get_by_id( $id, $include_widgets = FALSE )
	{
		$this->db->where( 'p.id', $id );
		$_result = $this->get_all( $include_widgets, TRUE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Fetch an object by it's slug
	 * 
	 * @access public
	 * @param string $slug The slug of the object to fetch
	 * @param bool $include_revisions Whether to include translation revisions
	 * @return stdClass
	 **/
	public function get_by_slug( $slug, $include_widgets = FALSE )
	{
		$this->db->where( 'p.slug', $slug );
		$_result = $this->get_all( $include_widgets, TRUE );
		
		// --------------------------------------------------------------------------
		
		if ( ! $_result )
			return FALSE;
		
		// --------------------------------------------------------------------------
		
		return $_result[0];
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function get_available_widgets()
	{
		//	Have we done this already? Don't do it again.
		if ( ! is_null( $this->_available_widgets ) ) :
		
			return $this->_avilable_widgets;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Search the Nails. widget folder, and then the App's widget folder.
		//	Widgets in the app folder trump widgets in the Nails folder
		
		$this->load->helper( 'directory' );
		
		//	Look for nails widgets
		$_nails_widgets = directory_map( $this->_nails_widgets_dir );
		
		//	Look for app widgets
		$_app_widgets = directory_map( $this->_app_widgets_dir );
		
		// --------------------------------------------------------------------------
		
		//	Sanitise
		if ( $_nails_widgets === FALSE ) :
		
			$_nails_widgets = array();
		
		endif;
		
		if ( $_app_widgets === FALSE ) :
		
			$_app_widgets = array();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Test and merge widgets
		$_widgets = array();
		foreach( $_nails_widgets AS $widget ) :
		
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

		return $_widgets;
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function render( $page )
	{
		//	Loop through all the widgets, instanciate the appropriate widget and execute
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
		if ( ! is_null( $key ) ) :
		
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
		if ( ! is_null( $key ) ) :
		
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
		
			if ( is_writable( $this->_routes_dir . 'routes_cms_page.php' ) ) :
			
				return TRUE;
			
			else :
			
				//	Attempt to chmod the file
				if ( @chmod( $this->_routes_dir . 'routes_cms_page.php', FILE_WRITE_MODE ) ) :

					return TRUE;

				else :

					$this->_set_error( 'The route config exists, but is not writeable.<small>Located at: ' . $this->_routes_dir . 'routes_cms_page.php</small>' );
					return FALSE;

				endif;
			
			endif;
		
		elseif ( is_writable( $this->_routes_dir ) ) :
		
			return TRUE;
		
		else :
		
			//	Attempt to chmod the directory
			if ( @chmod( $this->_routes_dir, DIR_WRITE_MODE ) ) :

				return TRUE;

			else :

				$this->_set_error( 'The route directory is not writeable.<small>' . $this->_routes_dir . '</small>' );
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
		
			$_data .= '$route[\'' . $page->slug . '\'] = \'cms/render/page\';' . "\n";
		
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
 * CodeIgniter  instanciate a class with the same name as the file, therefore
 * when we try to extend the parent class we get 'cannot redeclre class X' errors
 * and if we call our overloading class something else it will never get instanciated.
 * 
 * We solve this by prefixing the main class with NAILS_ and then conditionally
 * declaring this helper class below; the helper gets instanciated et voila.
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