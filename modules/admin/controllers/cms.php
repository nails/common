<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Name:			Admin : Basic CMS
*
* Description:	A basic CMS for applications
* 
*/

require_once NAILS_PATH . 'modules/admin/controllers/_admin.php';

class Cms extends Admin_Controller {

	/**
	 * Announces this module's details to those in the know.
	 *
	 * @access	static
	 * @param none
	 * @return	void
	 **/
	static function announce()
	{
		$d = new stdClass();
		
		// --------------------------------------------------------------------------
		
		//	Configurations
		$d->name				= 'Content Management';					//	Display name.
		
		// --------------------------------------------------------------------------
		
		//	Navigation options
		$d->funcs['pages']		= 'Manage Pages';					//	Sub-nav function.
		$d->funcs['blocks']		= 'Manage Blocks';					//	Sub-nav function.
		
		// --------------------------------------------------------------------------
		
		//	Only announce the controller if the user has permission to know about it
		return self::_can_access( $d, __FILE__ );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function __construct()
	{
		parent::__construct();
		
		// --------------------------------------------------------------------------
		
		//	Load helpers
		$this->load->helper( 'cms' );
		
		// --------------------------------------------------------------------------
		
		//	Load the CKEditor librar
		$this->asset->library( 'ckeditor' );
	}
	
	
	// --------------------------------------------------------------------------
	
	/* ! PAGES */
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Route requests for 'block' editing
	 *
	 * @access public
	 * @param none
	 * @return	void
	 **/
	public function pages()
	{
		//	Load common blocks items
		$this->load->model( 'cms_page_model', 'cms_page' );
		$this->asset->load( 'mustache.min.js', TRUE );
		$this->asset->load( 'nails.admin.cms.pages.min.js', TRUE );
		
		// --------------------------------------------------------------------------
		
		$_method = $this->uri->segment( 4 ) ? $this->uri->segment( 4 ) : 'index';
		
		if ( method_exists( $this, '_pages_' . $_method ) ) :
		
			$this->{'_pages_' . $_method}();
		
		else :
		
			show_404();
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Manage pages of content
	 *
	 * @access public
	 * @param none
	 * @return	void
	 **/
	private function _pages_index()
	{
		//	Page Title
		$this->data['page']->title = 'Manage Pages';
		
		// --------------------------------------------------------------------------
		
		//	Fetch all the pages in the DB
		$this->data['pages'] = $this->cms_page->get_all();
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/cms/pages/index',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Administration homepage / dashboard
	 *
	 * @access public
	 * @param none
	 * @return	void
	 **/
	private function _pages_edit()
	{
	}
	
	
	// --------------------------------------------------------------------------
	
	/* ! BLOCKS */
	
	// --------------------------------------------------------------------------
	
	
	/**
	 * Route requests for 'block' editing
	 *
	 * @access public
	 * @param none
	 * @return	void
	 **/
	public function blocks()
	{
		//	Load common blocks items
		$this->load->model( 'cms_block_model', 'cms_block' );
		$this->asset->load( 'mustache.min.js', TRUE );
		$this->asset->load( 'nails.admin.cms.blocks.min.js', TRUE );
		
		// --------------------------------------------------------------------------
		
		//	Define block types; block types allow for proper validation
		$this->data['block_types']				= array();
		$this->data['block_types']['plaintext']	= 'Plain Text';
		$this->data['block_types']['richtext']	= 'Rich Text';
		//$this->data['block_types']['image']		= 'Image (*.jpg, *.png, *.gif)';
		//$this->data['block_types']['file']		= 'File (*.*)';
		//$this->data['block_types']['number']	= 'Number';
		//$this->data['block_types']['url']		= 'URL';
		
		// --------------------------------------------------------------------------
		
		$_method = $this->uri->segment( 4 ) ? $this->uri->segment( 4 ) : 'index';
		
		if ( method_exists( $this, '_blocks_' . $_method ) ) :
		
			$this->{'_blocks_' . $_method}();
		
		else :
		
			show_404();
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _blocks_index()
	{
		//	Set method info
		$this->data['page']->title		= 'Manage Blocks';
		
		// --------------------------------------------------------------------------
		
		$this->data['blocks'] = $this->cms_block->get_all();
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/cms/blocks/index',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _blocks_edit()
	{
		$this->data['block'] = $this->cms_block->get_by_id( $this->uri->segment( 5 ), TRUE );
		
		if ( ! $this->data['block'] ) :
		
			$this->session->set_flashdata( 'error', '<strong>Sorry,</strong> no block found by that ID' );
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
		
			//	Loop through and update translations, keep track of translations which have been updated
			$_updated = array();
			foreach ( $this->input->post( 'translation' ) AS $translation ) :
			
				$this->cms_block->update_translation( $this->data['block']->id, $translation['lang_id'], $translation['value'] );
				$_updated[] = $translation['lang_id'];
			
			endforeach;
			
			//	Delete translations that weren't updated (they have been removed)
			if ( $_updated ) :
			
				$this->db->where( 'block_id', $this->data['block']->id );
				$this->db->where_not_in( 'lang_id', $_updated );
				$this->db->delete( 'cms_block_translation' );
				
			endif;
			
			//	Loop through and add new translations
			foreach ( $this->input->post( 'new_translation' ) AS $translation ) :
			
				$this->cms_block->create_translation( $this->data['block']->id, $translation['lang_id'], $translation['value'] );
			
			endforeach;
			
			// --------------------------------------------------------------------------
			
			//	Send the user on their merry way
			$this->session->set_flashdata( 'success', '<strong>Success!</strong> The block was updated successfully!' );
			redirect( 'admin/cms/blocks' );
			return;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		//	Set method info
		$this->data['page']->title	= 'Edit Block "' . $this->data['block']->title . '"';
		
		// --------------------------------------------------------------------------
		
		//	Fetch data
		$this->data['languages']	= $this->language_model->get_all_flat();
		$this->data['default_id']	= $this->language_model->get_default_id();
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',		$this->data );
		$this->load->view( 'admin/cms/blocks/edit',	$this->data );
		$this->load->view( 'structure/footer',		$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	private function _blocks_create()
	{
		if ( ! $this->user->is_superuser() ) :
		
			show_404();
		
		endif;
		
		// --------------------------------------------------------------------------
		
		if ( $this->input->post() ) :
			
			//	Form Validation
			$this->load->library( 'form_validation' );
			
			$this->form_validation->set_rules( 'slug',			'Slug',			'xss_clean|required|callback__fvcallback_block_slug' );
			$this->form_validation->set_rules( 'title',			'Title',		'xss_clean|required' );
			$this->form_validation->set_rules( 'description',	'Description',	'xss_clean|required' );
			$this->form_validation->set_rules( 'located',		'Located',		'xss_clean|required' );
			$this->form_validation->set_rules( 'type',			'Block Type',	'xss_clean|required|callback__fvcallback_block_type' );
			$this->form_validation->set_rules( 'value',			'Value',		'xss_clean|required' );
			
			$this->form_validation->set_message( 'required',			lang( 'fv_required' ) );
			$this->form_validation->set_message( 'string_to_boolean',	lang( 'fv_required' ) );
			
			if ( $this->form_validation->run( $this ) ) :
			
				$_type	= $this->input->post( 'type' );
				$_slug	= $this->input->post( 'slug' );
				$_title	= $this->input->post( 'title' );
				$_desc	= $this->input->post( 'description' );
				$_loc	= $this->input->post( 'located' );
				$_val	= $this->input->post( 'value' );
				
				if ( $this->cms_block->create_block( $_type, $_slug, $_title, $_desc, $_loc, $_val ) ) :
				
					$this->session->set_flashdata( 'success', '<strong>Success!</strong> Block created successfully.' );
					redirect( 'admin/cms/blocks' );
					return;
				
				else :
				
					$this->data['error'] = '<strong>Sorry,</strong> there was a problem creating the new block.';
				
				endif;
			
			else :
			
				$this->data['error'] = lang( 'fv_there_were_errors' );
			
			endif;
		
		endif;
		
		// --------------------------------------------------------------------------
		
		$this->load->view( 'structure/header',			$this->data );
		$this->load->view( 'admin/cms/blocks/create',	$this->data );
		$this->load->view( 'structure/footer',			$this->data );
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function _fvcallback_block_slug( $slug )
	{
		$slug = trim( $slug );
		
		//	Check slug's characters are ok
		if ( ! preg_match( '/[^a-zA-Z0-9\-\_]/', $slug ) ) :
		
			$_block = $this->cms_block->get_by_slug( $slug );
			
			if ( ! $_block ) :
			
				//	OK!
				return TRUE;
			
			else :
			
				$this->form_validation->set_message( '_fvcallback_block_slug', 'Must be unique' );
				return FALSE;
			
			endif;
		
		else :
		
			$this->form_validation->set_message( '_fvcallback_block_slug', 'Invalid characters' );
			return FALSE;
		
		endif;
	}
	
	
	// --------------------------------------------------------------------------
	
	
	public function _fvcallback_block_type( $type )
	{
		$type = trim( $type );
		
		if ( $type ) :
		
			if ( isset( $this->data['block_types'][$type] ) ) :
			
				return TRUE;
			
			else :
			
				$this->form_validation->set_message( '_fvcallback_block_type', 'Block type not supported.' );
				return FALSE;
			
			endif;
		
		else :
		
			$this->form_validation->set_message( '_fvcallback_block_type', lang( 'fv_required' ) );
			return FALSE;
		
		endif;
	}
}


/* End of file admin.php */
/* Location: ./application/modules/admin/controllers/admin.php */