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

		$this->_destructive_delete	= FALSE;

		// --------------------------------------------------------------------------

		//	Load the generic template & widget
		include_once $this->_nails_templates_dir . '_template.php';
		include_once $this->_nails_widgets_dir . '_widget.php';
	}


	// --------------------------------------------------------------------------


	public function create( $data )
	{
		//	Some basic sanity testing
		//	Check the data
		if ( empty( $data->data->template ) ) :

			$this->_set_error( '"data.template" is a required field.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->trans_begin();

		//	Create a new blank row to work with
		$_id = parent::create();

		if ( ! $_id ) :

			$this->_set_error( 'Unable to create base page object.' );
			$this->db->trans_rollback();
			return FALSE;

		endif;

		//	Try and update it depending on how the update went, commit & update or rollback
		if ( $this->update( $_id, $data ) ) :

			$this->db->trans_commit();
			return $_id;

		else :

			$this->db->trans_rollback();
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function update( $page_id, $data )
	{
		//	Check the data
		if ( empty( $data->data->template ) ) :

			$this->_set_error( '"data.template" is a required field.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Fetch the current version of this page, for reference.
		$_current = $this->get_by_id( $page_id );

		if ( ! $_current ) :

			$this->_set_error( 'Invalid Page ID' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Clone the data object so we can mutate it without worry. Unset id and
		//	hash as we don't need to store them

		$_clone = clone $data;
		unset( $_clone->id );
		unset( $_clone->hash );

		// --------------------------------------------------------------------------

		//	Start the transaction
		$this->db->trans_begin();

		// --------------------------------------------------------------------------

		//	Start prepping the data which doesn't require much thinking
		$_data = new stdClass();

		$_data->draft_parent_id			= ! empty( $_clone->data->parent_id )		? (int) $_clone->data->parent_id			: NULL;
		$_data->draft_title				= ! empty( $_clone->data->title )			? trim( $_clone->data->title )				: 'Untitled';
		$_data->draft_seo_title			= ! empty( $_clone->data->seo_title )		? trim( $_clone->data->seo_title )			: '';
		$_data->draft_seo_description	= ! empty( $_clone->data->seo_description )	? trim( $_clone->data->seo_description )	: '';
		$_data->draft_seo_keywords		= ! empty( $_clone->data->seo_keywords )	? trim( $_clone->data->seo_keywords )		: '';

		$_data->draft_template			= $_clone->data->template;

		$_data->draft_template_data		= json_encode( $_clone, JSON_UNESCAPED_SLASHES );
		$_data->draft_hash				= md5( $_data->draft_template_data );

		// --------------------------------------------------------------------------

		//	Additional sanitising; encode HTML entities. Also encode the pipe character
		//	in the title, so that it doesn't break our explode

		$_data->draft_title				= htmlentities( str_replace( '|', '&#124;', $_data->draft_title ), ENT_COMPAT | ENT_HTML401, 'UTF-8', FALSE );
		$_data->draft_seo_title			= htmlentities( $_data->draft_seo_title, ENT_COMPAT | ENT_HTML401, 'UTF-8', FALSE );
		$_data->draft_seo_description	= htmlentities( $_data->draft_seo_description, ENT_COMPAT | ENT_HTML401, 'UTF-8', FALSE );
		$_data->draft_seo_keywords		= htmlentities( $_data->draft_seo_keywords, ENT_COMPAT | ENT_HTML401, 'UTF-8', FALSE );

		// --------------------------------------------------------------------------

		//	Prep data which requires a little more intensive processing

		// --------------------------------------------------------------------------

		//	Work out the slug
		if ( $_data->draft_parent_id ) :

			//	There is a parent, so set it's slug as the prefix
			$_parent = $this->get_by_id( $_data->draft_parent_id );

			if ( ! $_parent ) :

				$this->_set_error( 'Invalid Parent ID.' );
				$this->db->trans_rollback();
				return FALSE;

			endif;

			$_prefix = $_parent->draft->slug . '/';

		else :

			//	No parent, no need for a prefix
			$_prefix = '';

		endif;

		$_data->draft_slug		= $this->_generate_slug( $_data->draft_title, $_prefix, '', NULL, 'draft_slug', $_current->id );
		$_data->draft_slug_end	= end( explode('/', $_data->draft_slug ) );

		// --------------------------------------------------------------------------

		//	Generate the breadcrumbs
		$_data->draft_breadcrumbs = array();

		if ( $_data->draft_parent_id ) :

			//	There is a parent, use it's breadcrumbs array as the starting point.
			//	No need to fetch the parent again.

			$_data->draft_breadcrumbs = $_parent->draft->breadcrumbs;

		endif;

		$_temp			= new stdClass();
		$_temp->id		= $_current->id;
		$_temp->title	= $_data->draft_title;
		$_temp->slug	= $_data->draft_slug;

		$_data->draft_breadcrumbs[] = $_temp;
		unset( $_temp );

		//	Encode the breadcrumbs for the database
		$_data->draft_breadcrumbs = json_encode( $this->_generate_breadcrumbs( $_current->id ) );

		// --------------------------------------------------------------------------

		if ( parent::update( $_current->id, $_data ) ) :

			//	Update was successful, set the breadcrumbs
			$_breadcrumbs = $this->_generate_breadcrumbs( $_current->id );

			$this->db->set( 'draft_breadcrumbs', json_encode( $_breadcrumbs ) );
			$this->db->where( 'id', $_current->id );
			if ( ! $this->db->update( $this->_table ) ) :

				$this->_set_error( 'Failed to generate breadcrumbs.' );
				$this->db->trans_rollback();
				return FALSE;

			endif;

			//	For each child regenerate the breadcrumbs and slugs (only if the title or slug has changed)
			if ( $_current->draft->title != $_data->draft_title || $_current->draft->slug != $_data->draft_slug ) :

				$_children = $this->get_ids_of_children( $_current->id );

				if ( $_children ) :

					//	Loop each child and update it's details
					foreach( $_children AS $child_id ) :

						//	We can assume that the children are in a sensible order, loop them and
						//	process. For nested children, their parent will have been processed by
						//	the time we process it.

						$_child = $this->get_by_id( $child_id );

						if ( ! $_child ) :

							continue;

						endif;

						$_data = new stdClass();

						//	Generate the breadcrumbs
						$_data->draft_breadcrumbs = json_encode( $this->_generate_breadcrumbs( $_child->id ) );

						//	Generate the slug
						if ( $_child->draft->parent_id ) :

							//	Child has a parent, fetch it and use it's slug as the prefix
							$_parent = $this->get_by_id( $_child->draft->parent_id );

							if ( $_parent ) :

								$_data->draft_slug = $_parent->draft->slug . '/' . $_child->draft->slug_end;

							else :

								//	Parent is bad, make this a parent page. Poor wee orphan.
								$_data->draft_parent_id	= NULL;
								$_data->draft_slug		= $_child->draft->slug_end;

							endif;

						else :

							//	Would be weird if this happened, but ho hum handle it anyway
							$_data->draft_parent_id	= NULL;
							$_data->draft_slug		= $_child->draft->slug_end;

						endif;

						//	Update the child and move on
						if ( ! parent::update( $_child->id, $_data ) ) :

							$this->_set_error( 'Failed to update breadcrumbs and/or slug of child page.' );
							$this->db->trans_rollback();
							return FALSE;

						endif;

					endforeach;

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Finish up.
			$this->db->trans_commit();
			return TRUE;

		else :

		 	$this->_set_error( 'Failed to update page object.' );
			$this->db->trans_rollback();
		 	return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _generate_breadcrumbs( $id )
	{
		$_page = $this->get_by_id( $id );

		if ( ! $_page ) :

			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$_breadcrumbs = array();

		if ( $_page->draft->parent_id ) :

			$_breadcrumbs = array_merge( $_breadcrumbs, $this->_generate_breadcrumbs( $_page->draft->parent_id ) );

		endif;

		$_temp					= new stdClass();
		$_temp->id				= $_page->id;
		$_temp->title			= $_page->draft->title;

		$_breadcrumbs[] = $_temp;
		unset( $_temp );

		return $_breadcrumbs;
	}


	// --------------------------------------------------------------------------


	public function render_template( $template, $widgets = array(), $additional_fields = array() )
	{
		$_template = $this->get_template( $template, 'RENDER' );

		if ( ! $_template ) :

			$this->_set_error( '"' . $template .'" is not a valid template.' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Look for manual config items
		if ( ! empty( $additional_fields->manual_config->assets_render ) ) :

			if ( ! is_array( $additional_fields->manual_config->assets_render ) ) :

				$additional_fields->manual_config->assets_render = (array) $additional_fields->manual_config->assets_render;

			endif;

			$this->_load_assets( $additional_fields->manual_config->assets_render );

		endif;

		// --------------------------------------------------------------------------

		//	Attempt to instantiate and render the template
		try
		{
			require_once $_template->path . 'template.php';

			$TEMPLATE = new $_template->iam();

			try
			{
				return $TEMPLATE->render( (array) $widgets, (array) $additional_fields );
			}
			catch( Exception $e )
			{
				$this->_set_error( 'Could not render template "' . $template . '".' );
				return FALSE;
			}
		}
		catch( Exception $e )
		{
			$this->_set_error( 'Could not instantiate template "' . $template . '".' );
			return FALSE;
		}
	}


	// --------------------------------------------------------------------------


	public function publish( $id )
	{
		//	Check the page is valid
		$_page = $this->get_by_id( $id );

		if ( ! $_page ) :

			$this->_set_message( 'Invalid Page ID' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Start the transaction
		$this->db->trans_begin();

		// --------------------------------------------------------------------------

		//	If the slug has changed add an entry to the slug history page
		$_slug_history = array();
		if ( $_page->published->slug && $_page->published->slug != $_page->draft->slug ) :

			$_slug_history[] = array(
				'slug'		=> $_page->published->slug,
				'page_id'	=> $id
			);

		endif;

		// --------------------------------------------------------------------------

		//	Update the published_* columns to be the same as the draft columns
		$this->db->set( 'published_hash',				'draft_hash',				FALSE );
		$this->db->set( 'published_parent_id',			'draft_parent_id',			FALSE );
		$this->db->set( 'published_slug',				'draft_slug',				FALSE );
		$this->db->set( 'published_slug_end',			'draft_slug_end',			FALSE );
		$this->db->set( 'published_template',			'draft_template',			FALSE );
		$this->db->set( 'published_template_data',		'draft_template_data',		FALSE );
		$this->db->set( 'published_title',				'draft_title',				FALSE );
		$this->db->set( 'published_breadcrumbs',		'draft_breadcrumbs',		FALSE );
		$this->db->set( 'published_seo_title',			'draft_seo_title',			FALSE );
		$this->db->set( 'published_seo_description',	'draft_seo_description',	FALSE );
		$this->db->set( 'published_seo_keywords',		'draft_seo_keywords',		FALSE );

		$this->db->set( 'is_published',	TRUE );
		$this->db->set( 'modified',		date('Y-m-d H:i:s') );

		if ( $this->user_model->is_logged_in() ) :

			$this->db->set( 'modified_by',	active_user( 'id' ) );

		endif;

		$this->db->where( 'id', $_page->id );

		if ( $this->db->update( $this->_table ) ) :

			//	Fetch the children, returning the data we need for the updates
			$_children = $this->get_ids_of_children( $_page->id );

			if ( $_children ) :

				//	Loop each child and update it's published details, but only
				//	if they've changed.

				foreach( $_children AS $child_id ) :

					$_child = $this->get_by_id( $child_id );

					if ( ! $_child ) :

						continue;

					endif;

					if ( $_child->published->title == $_child->draft->title && $_child->published->slug == $_child->draft->slug ) :

						continue;

					endif;

					//	First make a note of the old slug
					if ( $_child->is_published ) :

						$_slug_history[] = array(
							'slug'		=> $_child->draft->slug,
							'page_id'	=> $_child->id
						);

					endif;

					//	Next we set the appropriate fields
					$this->db->set( 'published_slug',			$_child->draft->slug );
					$this->db->set( 'published_slug_end',		$_child->draft->slug_end );
					$this->db->set( 'published_breadcrumbs',	json_encode( $_child->draft->breadcrumbs ) );
					$this->db->set( 'modified',					date('Y-m-d H:i:s') );

					$this->db->where( 'id', $_child->id );

					if ( ! $this->db->update( $this->_table ) ) :

						$this->_set_error( 'Failed to update a child page\'s data.' );
						$this->db->trans_rollback();
						return FALSE;

					endif;

				endforeach;

			endif;

			//	Add any slug_history thingmys
			foreach ( $_slug_history AS $item ) :

				$this->db->set( 'hash',		md5( $item['slug'] . $item['page_id'] ) );
				$this->db->set( 'slug',		$item['slug'] );
				$this->db->set( 'page_id',	$item['page_id'] );
				$this->db->set( 'created',	'NOW()', FALSE );
				$this->db->replace( NAILS_DB_PREFIX . 'cms_page_slug_history' );

			endforeach;

			// --------------------------------------------------------------------------

			//	Rewrite routes
			$this->load->model( 'system/routes_model' );
			$this->routes_model->update( 'cms' );

			// --------------------------------------------------------------------------

			//	Regenerate sitemap
			if ( module_is_enabled( 'sitemap' ) ) :

				$this->load->model( 'sitemap/sitemap_model' );
				$this->sitemap_model->generate();

			endif;

			$this->db->trans_commit();

			//	TODO: Kill caches for this page and all children

			return TRUE;

		else :

			$this->db->trans_rollback();
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

		$this->db->order_by( $this->_table_prefix . '.draft_slug' );
	}


	// --------------------------------------------------------------------------


	public function get_all_nested( $use_draft = TRUE )
	{
		return $this->_nest_pages( $this->get_all(), NULL, $use_draft );
	}


	// --------------------------------------------------------------------------


	/**
	 *	Hat tip to Timur; http://stackoverflow.com/a/9224696/789224
	 **/
	protected function _nest_pages( &$list, $parent_id = NULL, $use_draft = TRUE )
	{
		$result = array();

		for ( $i = 0, $c = count( $list ); $i < $c; $i++ ) :

			$_parent_id = $use_draft ? $list[$i]->draft->parent_id : $list[$i]->published->parent_id;

			if ( $_parent_id == $parent_id ) :

				$list[$i]->children	= $this->_nest_pages( $list, $list[$i]->id, $use_draft );
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
				if ( $_parent->draft->parent_id ) :

					//	Yes it does, repeat!
					$_return = $this->_find_parents( $_parent->draft->parent_id, $source, $separator );

					return $_return ? $_return . $_parent->draft->title . $separator : $_parent->draft->title;

				else :

					//	Nope, end of the line mademoiselle
					return $_parent->draft->title . $separator;

				endif;


			else :

				//	Did not find parent, give up.
				return '';

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_ids_of_children( $page_id, $format = 'ID' )
	{
		$_out = array();

		$this->db->select( 'id,draft_slug,draft_title,is_published' );
		$this->db->where( 'draft_parent_id', $page_id );
		$_children = $this->db->get( NAILS_DB_PREFIX . 'cms_page' )->result();

		if ( $_children ) :

			foreach ( $_children AS $child ) :

				switch( $format ) :

					case 'ID'						: $_out[] = $child->id;	break;
					case 'ID_SLUG'					: $_out[] = array( 'id' => $child->id, 'slug' => $child->draft_slug );	break;
					case 'ID_SLUG_TITLE'			: $_out[] = array( 'id' => $child->id, 'slug' => $child->draft_slug, 'title' => $child->draft_title );	break;
					case 'ID_SLUG_TITLE_PUBLISHED'	: $_out[] = array( 'id' => $child->id, 'slug' => $child->draft_slug, 'title' => $child->draft_title, 'is_published' => (bool) $child->is_published );	break;

				endswitch;

				$_out	= array_merge( $_out, $this->get_ids_of_children( $child->id, $format ) );

			endforeach;

			return $_out;

		else :

			return $_out;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_all_flat( $use_draft = TRUE )
	{
		$_out	= array();
		$_pages	= $this->get_all();

		foreach( $_pages AS $page ) :

			if ( $use_draft ) :

				$_out[$page->id] = $page->draft->title;

			else :

				$_out[$page->id] = $page->published->title;

			endif;

		endforeach;

		return $_out;
	}


	// --------------------------------------------------------------------------


	public function get_top_level( $use_draft = TRUE )
	{
		if ( $use_draft ) :

			$this->db->where( 'draft_parent_id', NULL );

		else :

			$this->db->where( 'published_parent_id', NULL );

		endif;

		return $this->get_all();
	}


	// --------------------------------------------------------------------------


	public function get_siblings( $id, $use_draft = TRUE )
	{
		$_page = $this->get_by_id( $id );

		if ( ! $_page ) :

			return array();

		endif;

		if ( $use_draft ) :

			$this->db->where( 'draft_parent_id', $_page->draft->parent_id );

		else :

			$this->db->where( 'published_parent_id', $_page->published->parent_id );

		endif;

		return $this->get_all();
	}


	// --------------------------------------------------------------------------


	public function get_homepage()
	{
		$this->db->where( $this->_table_prefix . '.is_homepage', TRUE );
		$_page	= $this->get_all();

		if ( ! $_page ) :

			return FALSE;

		endif;

		return $_page[0];
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
		$page->published->breadcrumbs	= json_decode( $page->published->breadcrumbs );
		$page->draft->breadcrumbs		= json_decode( $page->draft->breadcrumbs );

		//	Unpublished changes?
		$page->has_unpublished_changes = $page->is_published && $page->draft->hash != $page->published->hash;

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

		// --------------------------------------------------------------------------

		//	SEO Title
		//	If not set then fallback to the page title

		if ( empty( $page->seo_title ) && ! empty( $page->title ) ) :

			$page->seo_title = $page->title;

		endif;
	}


	// --------------------------------------------------------------------------


	public function get_available_widgets( $load_assets = FALSE )
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

			// --------------------------------------------------------------------------

			//	Load the widget's assets if requested
			if ( $load_assets ) :

				//	What type of assets do we want to load, editor or render assets?
				switch( $load_assets ) :

					case 'EDITOR' :

						$_assets = $w->assets_editor;

					break;

					case 'RENDER' :

						$_assets = $w->assets_render;

					break;

					default :

						$_assets = array();

					break;

				endswitch;

				$this->_load_assets( $_assets );

			endif;

		endforeach;

		//	Sort non-generic widgets into alphabetical order
		foreach( $_out AS $o ) :

			usort( $o->widgets, array( $this, '_sort_widgets' ) );

		endforeach;

		//	Sort generic
		usort( $_generic_widgets[md5( $_generic_widget_grouping )]->widgets, array( $this, '_sort_widgets' ) );

		//	Sort the non-generic groupings
		//	TODO: Future Pabs, explain in comment why you're not using the _sort_widgets method. I'm sure
		//	there's a valid reason you handsome chap, you.

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


	public function get_widget( $slug, $load_assets = FALSE )
	{
		$_widgets = $this->get_available_widgets();

		foreach ( $_widgets AS $widget_group ) :

			foreach ( $widget_group->widgets AS $widget ) :

				if ( $slug == $widget->slug ) :

					if ( $load_assets ) :

						switch( $load_assets ) :

							case 'EDITOR' :

								$_assets = $widget->assets_editor;

							break;

							case 'RENDER' :

								$_assets = $widget->assets_render;

							break;

							default :

								$_assets = array();

							break;

						endswitch;

						$this->_load_assets( $_assets );

					endif;

					return $widget;

				endif;

			endforeach;

		endforeach;

		return FALSE;
	}


	// --------------------------------------------------------------------------


	public function get_available_templates( $load_assets = FALSE )
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

			else :

				//	This template returned no details, ignore it.
				log_message( 'warning', 'Static method "details()"" of Nails template "' . $template . '" returned empty data.' );

			endif;

			// --------------------------------------------------------------------------

			//	Load the template's assets if requested
			if ( $load_assets ) :

				//	What type of assets do we want to load, editor or render assets?
				switch( $load_assets ) :

					case 'EDITOR' :

						$_assets = $_templates[$template]->assets_editor;

					break;

					case 'RENDER' :

						$_assets = $_templates[$template]->assets_render;

					break;

					default :

						$_assets = array();

					break;

				endswitch;

				$this->_load_assets( $_assets );

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

			$_details = $_class::details();

			if ( $_details ) :

				$_templates[$template] = $_class::details();

			else :

				//	This template returned no details, ignore this template. Don't log anything
				//	as it's likely a developer override to hide a default template.

				continue;

			endif;

			// --------------------------------------------------------------------------

			//	Load the template's assets if requested
			if ( $load_assets ) :

				switch( $load_assets ) :

					case 'EDITOR' :

						$_assets = $_templates[$template]->assets_editor;

					break;

					case 'RENDER' :

						$_assets = $_templates[$template]->assets_render;

					break;

					default :

						$_assets = array();

					break;

				endswitch;

				$this->_load_assets( $_assets );

			endif;

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


	public function get_template( $slug, $load_assets = FALSE )
	{
		$_templates = $this->get_available_templates();

		foreach ( $_templates AS $template ) :

			if ( $slug == $template->slug ) :

				if ( $load_assets ) :

					switch( $load_assets ) :

						case 'EDITOR' :

							$_assets = $template->assets_editor;

						break;

						case 'RENDER' :

							$_assets = $template->assets_render;

						break;

						default :

							$_assets = array();

						break;

					endswitch;

					$this->_load_assets( $_assets );

				endif;

				return $template;

			endif;

		endforeach;

		return FALSE;
	}


	// --------------------------------------------------------------------------


	protected function _load_assets( $assets = array() )
	{
		foreach ( $assets AS $asset ) :

			if ( is_array( $asset ) ) :

				if ( ! empty( $asset[1] ) ) :

					$_is_nails = $asset[1];

				else:

					$_is_nails = FALSE;

				endif;

				$this->asset->load( $asset[0], $_is_nails );

			elseif ( is_string( $asset ) ) :

				$this->asset->load( $asset );

			endif;

		endforeach;
	}


	// --------------------------------------------------------------------------


	public function delete( $id )
	{
		$_page = $this->get_by_id( $id );

		if ( ! $_page ) :

			$this->_set_error( 'Invalid page ID' );
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		$this->db->trans_begin();

		$this->db->where( 'id', $id );
		$this->db->set( 'is_deleted', TRUE );
		$this->db->set( 'modified', 'NOW()', FALSE );

		if ( $this->user_model->is_logged_in() ) :

			$this->db->set( 'modified_by', active_user( 'id' ) );

		endif;

		if ( $this->db->update( $this->_table ) ) :

			//	Success, update children
			$_children = $this->get_ids_of_children( $id );

			if ( $_children ) :

				$this->db->where_in( 'id', $_children );
				$this->db->set( 'is_deleted', TRUE );
				$this->db->set( 'modified', 'NOW()', FALSE );

				if ( $this->user_model->is_logged_in() ) :

					$this->db->set( 'modified_by', active_user( 'id' ) );

				endif;

				if ( ! $this->db->update( $this->_table ) ) :

					$this->_set_error( 'Unable to delete children pages' );
					$this->db->trans_rollback();
					return FALSE;

				endif;

			endif;

			// --------------------------------------------------------------------------

			//	Rewrite routes
			$this->load->model( 'system/routes_model' );
			$this->routes_model->update( 'cms' );

			// --------------------------------------------------------------------------

			//	Regenerate sitemap
			if ( module_is_enabled( 'sitemap' ) ) :

				$this->load->model( 'sitemap/sitemap_model' );
				$this->sitemap_model->generate();

			endif;

			// --------------------------------------------------------------------------

			$this->db->trans_commit();
			return TRUE;

		else :

			//	Failed
			$this->db->trans_rollback();
			return FALSE;

		endif;
	}


	// --------------------------------------------------------------------------


	public function destroy( $id )
	{
		//	TODO: implement this?
		$this->_set_error( 'It is not possible to destroy pages using this system.' );
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