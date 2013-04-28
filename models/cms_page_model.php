<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cms_page_model extends NAILS_Model
{
	private $_routes_dir;
	private $_available_widgets;
	private $_nails_widgets_dir;
	private $_app_widgets_dir;
	private $_nails_prefix;
	private $_app_prefix;
	
	
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
		if ( isset( $data->widgets ) ) :
		
			$_widgets = $data->widgets;
			unset( $data->widgets );
		
		endif;
		
		//	Next, check the slug is unique, encode it to be safe
		if ( isset( $data->slug ) ) :
		
			$data->slug = trim( url_title( $data->slug ) );
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
			if ( isset( $_widgets ) && is_array( $_widgets ) ) :
			
				//	Loop through the $_widgets array, update any `old-` widgets, add `new-` widgets,
				//	remove widgets which aren't provided and then save the order.
				
				$_order				= 0;
				$_processed_widgets	= array();
				
				foreach ( $_widgets AS $key => $widget ) :
				
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
				$this->db->where_not_in( 'id', $_processed_widgets );
				$this->db->delete( 'cms_page_widget' );
			
			endif;
			
			// --------------------------------------------------------------------------
			
			//	Update the routes file
			if ( $this->_write_routes() ) :
			
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
		$this->db->select( 'p.id,p.slug,p.title,p.seo_description,p.seo_keywords,p.created,p.modified,p.modified_by,p.is_deleted' );
		$this->db->select( 'u.email, um.first_name, um.last_name, um.profile_img, um.gender' );
		
		$this->db->join( 'user u', 'u.id = p.modified_by' );
		$this->db->join( 'user_meta um', 'um.user_id = p.modified_by' );
		
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
				
				$this->db->select();
				$this->db->where( 'page_id', $page->id );
				$this->db->order_by( 'order' );
				$page->widgets = $this->db->get( 'cms_page_widget' )->result();
				
			endif;
			
			// --------------------------------------------------------------------------
		
		endforeach;
		
		return $_pages;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _format_page_object( &$page )
	{
		$page->id			= (int) $page->id;
		$page->is_deleted	= (bool) $page->is_deleted;
		
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
		
		return $_widgets;
		
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function render( $page )
	{
		if ( ! isset( $page->widgets ) || ! $page->widgets ) :
		
			return '';
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Loop through all the widgets, instanciate the appropriate widget and execute
		//	it's render function, append the result to the $_out variable and spit that back
		
		$_out		= '';
		
		foreach ( $page->widgets AS $key => $widget ) :
		
			$_out .= $this->_call_widget_method( $widget->widget_class, $widget->widget_data, 'render' );
		
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
	
	
	private function _call_widget_method( $widget, $data, $method, $params = array() )
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
			
				$this->_set_error( 'The route config exists, but is not writeable.<small>Located at: ' . $this->_routes_dir . 'routes_cms_page.php</small>' );
				return FALSE;
			
			endif;
			
		elseif ( is_writable( $this->_routes_dir ) ) :
		
			return TRUE;
		
		else :
		
			$this->_set_error( 'The route directory is not writeable.<small>' . $this->_routes_dir . '</small>' );
			return FALSE;
		
		endif;
	}
	
	// --------------------------------------------------------------------------
	
	
	private function _write_routes()
	{
		if ( ! $this->can_write_routes() ) :
		
			return FALSE;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Routes are writeable, apparently, give it a bash
		$_data = '<?php  if ( ! defined(\'BASEPATH\')) exit(\'No direct script access allowed\');' . "\n\n";
		
		$_pages = $this->get_all();
		
		foreach ( $_pages AS $page ) :
		
			$_data .= '$route[\'' . $page->slug . '\'] = \'cms/render/page\';';
		
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


/* End of file cms_page_model.php */
/* Location: ./models/cms_page_model.php */