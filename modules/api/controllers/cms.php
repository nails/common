<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Name:		CMS API
 *
 * Description:	This controller handles CMS API calls
 *
 **/

require_once '_api.php';

/**
 * OVERLOADING NAILS' API MODULES
 *
 * Note the name of this class; done like this to allow apps to extend this class.
 * Read full explanation at the bottom of this file.
 *
 **/

class NAILS_Cms extends NAILS_API_Controller
{
	private $_authorised;
	private $_error;


	// --------------------------------------------------------------------------


	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 *
	 **/
	public function __construct()
	{
		parent::__construct();

		// --------------------------------------------------------------------------

		$this->_authorised	= TRUE;
		$this->_error		= '';

		// --------------------------------------------------------------------------

		if ( ! module_is_enabled( 'cms' ) ) :

			//	Cancel execution, module isn't enabled
			show_404();

		endif;

		// --------------------------------------------------------------------------

		//	Only logged in users
		if ( ! $this->user_model->is_logged_in() ) :


			$this->_authorised	= FALSE;
			$this->_error		= lang( 'auth_require_session' );

		endif;

		// --------------------------------------------------------------------------

		//	Only admins
		if ( ! $this->user_model->is_admin() ) :

			$this->_authorised	= FALSE;
			$this->_error		= lang( 'auth_require_administrator' );

		endif;
	}


	// --------------------------------------------------------------------------


	public function pages()
	{
		if ( ! $this->_authorised ) :

			$this->_out( array( 'status' => 401, 'error' => $this->_error ) );
			return;

		endif;

		// --------------------------------------------------------------------------

		switch( $this->uri->segment( 4 ) ) :

			case 'widget'	: $this->_pages_widget();								break;
			case 'save'		: $this->_pages_save();									break;
			default			: $this->_method_not_found( $this->uri->segment( 4 ) );	break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _pages_widget()
	{
		switch( $this->uri->segment( 5 ) ) :

			case 'get_editor'	: $this->_pages_widget_get_editor();					break;
			default				: $this->_method_not_found( $this->uri->segment( 5 ) );	break;

		endswitch;
	}


	// --------------------------------------------------------------------------


	protected function _pages_widget_get_editor()
	{
		$_out		= array();
		$_widget	= $this->input->get_post( 'widget' );
		parse_str( $this->input->get_post( 'data' ), $_data );
		$_template	= $this->input->get_post( 'template' );

		if ( $_widget ) :

			$this->load->model( 'cms/cms_page_model' );

			$_widget = $this->cms_page_model->get_widget( $_widget );

			if ( $_widget ) :

				//	Instantiate the widget
				include_once $_widget->path . 'widget.php';

				try
				{

					$WIDGET		= new $_widget->iam();
					$_editor	= $WIDGET->get_editor( $_data );

					if ( ! empty( $_editor ) ) :

						$_out['HTML'] = $_editor;

					else :

						$_out['HTML'] = '<p class="static">This widget has no configurable options.</p>';

					endif;

				}
				catch ( Exception $e)
				{
					$_out['status']	= 500;
					$_out['error']	= 'This widget has not been configured correctly. Please contact the developer quoting this error message: <strong>"#3:' . $_widget->iam . ':GetEditor"</strong>';
				}

			else :

				$_out['status']	= 400;
				$_out['error']	= 'Invalid Widget - Error number 2';

			endif;

		else :

			$_out['status']	= 400;
			$_out['error']	= 'Widget slug must be specified - Error number 1';

		endif;

		$this->_out( $_out );
	}


	// --------------------------------------------------------------------------


	protected function _pages_save()
	{
		$_page_data_raw		= $this->input->get_post( 'page_data' );
		$_publish_action	= $this->input->get_post( 'publish_action' );

		if ( ! $_page_data_raw ) :

			$this->_out(array(
				'status'	=> 400,
				'error'		=> '"page_data" is a required parameter.'
			));
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Decode and check
		$_page_data = json_decode( $_page_data_raw );

		if ( NULL === $_page_data ) :

			$this->_out(array(
				'status'	=> 400,
				'error'		=> '"page_data" is a required parameter.'
			));
			log_message( 'error', 'API: cms/pages/save - Error decoding JSON: ' . $_page_data_raw );
			return;

		endif;

		if ( empty( $_page_data->hash ) ) :

			$this->_out(array(
				'status'	=> 400,
				'error'		=> '"hash" is a required parameter.'
			));
			log_message( 'error', 'API: cms/pages/save - Empty hash supplied.' );
			return;

		endif;

		//	A template must be defined
		if ( empty( $_page_data->data->template ) ) :

			$this->_out(array(
				'status'	=> 400,
				'error'		=> '"data.template" is a required parameter.'
			));
			return;

		endif;

		// --------------------------------------------------------------------------

		//	Validate data
		//	JSON.stringify doesn't seem to escape forward slashes like PHP does
		//	Check both in case this is a cross browser issue.

		$_hash						= $_page_data->hash;
		$_check_obj					= new stdClass();
		$_check_obj->data			= $_page_data->data;
		$_check_obj->widget_areas	= $_page_data->widget_areas;

		$_check_hash1		= md5( json_encode( $_check_obj, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE ) );

		if ( $_hash !== $_check_hash1 ) :

			$_check_hash2 = md5( json_encode( $_check_obj ) );

			if ( $_hash !== $_check_hash2 ) :

				$this->_out(array(
					'status'	=> 400,
					'error'		=> 'Data failed hash validation. Data might have been modified in transit.'
				));
				log_message( 'error', 'API: cms/pages/save - Failed to verify hashes. Posted JSON: ' . 	$_page_data_raw  );
				return;

			endif;

		endif;

		$_page_data->hash = $_hash;

		// --------------------------------------------------------------------------

		//	All seems good, let's process this mofo'ing data. Same format as supplied,
		//	just manually specifying things for supreme consistency. Multi-pass?

		$_data							= new stdClass();
		$_data->hash					= $_page_data->hash;
		$_data->id						= ! empty( $_page_data->id )						? (int) $_page_data->id					: NULL;
		$_data->data					= new stdClass();
		$_data->data->title				= ! empty( $_page_data->data->title )				? $_page_data->data->title				: '';
		$_data->data->parent_id			= ! empty( $_page_data->data->parent_id )			? (int) $_page_data->data->parent_id	: '';
		$_data->data->seo_title			= ! empty( $_page_data->data->seo_title )			? $_page_data->data->seo_title			: '';
		$_data->data->seo_description	= ! empty( $_page_data->data->seo_description )		? $_page_data->data->seo_description	: '';
		$_data->data->seo_keywords		= ! empty( $_page_data->data->seo_keywords )		? $_page_data->data->seo_keywords		: '';
		$_data->data->template			= $_page_data->data->template;
		$_data->data->additional_fields	= ! empty( $_page_data->data->additional_fields )	? $_page_data->data->additional_fields	: '';
		$_data->widget_areas			= ! empty( $_page_data->widget_areas )				? $_page_data->widget_areas				: new stdClass;

		if ( $_data->data->additional_fields ) :

			parse_str( $_data->data->additional_fields, $_additional_fields );
			$_data->data->additional_fields = ! empty( $_additional_fields['additional_field'] ) ? $_additional_fields['additional_field'] : array();

			//	We're going to encode then decode the additional fields, so they're consistent with the save objects
			$_data->data->additional_fields = json_decode( json_encode( $_data->data->additional_fields ) );

		endif;

		// --------------------------------------------------------------------------

		/**
		 * Data is set, determine whether we're saving or creating
		 *
		 * If an ID is missing then we're creating a new page otherwise we're updating.
		 *
		 **/

		$this->load->model( 'cms/cms_page_model' );

		if ( ! $_data->id ) :

			if ( ! user_has_permission( 'admin.cms.can_create_page' ) ) :

				$this->_out(array(
					'status'	=> 400,
					'error'		=> 'You do not have permission to create CMS Pages.'
				));
				return;

			endif;

			$_id = $this->cms_page_model->create( $_data );

			if ( ! $_id ) :

				$this->_out(array(
					'status'	=> 500,
					'error'		=> 'There was a problem saving the page. ' . $this->cms_page_model->last_error()
				));
				return;

			endif;

		else :

			if ( ! user_has_permission( 'admin.cms.can_edit_page' ) ) :

				$this->_out(array(
					'status'	=> 400,
					'error'		=> 'You do not have permission to edit CMS Pages.'
				));
				return;

			endif;

			if ( $this->cms_page_model->update( $_data->id, $_data, $this->data ) ) :

				$_id = $_data->id;

			else :

				$this->_out(array(
					'status'	=> 500,
					'error'		=> 'There was a problem saving the page. ' . $this->cms_page_model->last_error()
				));
				return;

			endif;

		endif;

		// --------------------------------------------------------------------------

		/**
		 * Page has been saved! Any further steps?
		 *
		 * If is_published is defined then we need to consider it's published status.
		 * If is_published is NULL then we're leaving it as it is.
		 *
		 **/

		$_out		= array();
		$_out['id']	= $_id;

		switch( $_publish_action ) :

			case 'PUBLISH' :

				$this->cms_page_model->publish( $_id );

			break;

			case 'NONE' :
			default :

				//	Do nothing, absolutely nothing. Go have a margarita.

			break;

		endswitch;

		// --------------------------------------------------------------------------

		//	Return
		$this->_out( $_out );
	}
}


// --------------------------------------------------------------------------


/**
 * OVERLOADING NAILS' API MODULES
 *
 * The following block of code makes it simple to extend one of the core API
 * controllers. Some might argue it's a little hacky but it's a simple 'fix'
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
 * If/when we want to extend the main class we simply define NAILS_ALLOW_EXTENSION_CLASSNAME
 * before including this PHP file and extend as normal (i.e in the same way as below);
 * the helper won't be declared so we can declare our own one, app specific.
 *
 **/

if ( ! defined( 'NAILS_ALLOW_EXTENSION_CMS' ) ) :

	class Cms extends NAILS_Cms
	{
	}

endif;

/* End of file cms.php */
/* Location: ./modules/api/controllers/cms.php */