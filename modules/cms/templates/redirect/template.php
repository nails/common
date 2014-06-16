<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class NAILS_CMS_Template_redirect extends Nails_CMS_Template
{
	static function details()
	{
		//	Base object
		$_d = parent::_details();

		//	Basic details; describe the template for the user
		$_d->label			= 'Redirect';
		$_d->description	= 'Redirects to another URL.';

		// --------------------------------------------------------------------------

		//	Additional fields
		$_d->additional_fields					= array();
		$_d->additional_fields[0]				= array();
		$_d->additional_fields[0]['type']		= 'dropdown';
		$_d->additional_fields[0]['key']		= 'redirect_page_id';
		$_d->additional_fields[0]['label']		= 'Redirect To Page';
		$_d->additional_fields[0]['class']		= 'chosen';
		$_d->additional_fields[0]['options']	= array( 'None' ) + get_instance()->cms_page_model->get_all_nested_flat();

		$_d->additional_fields[1]					= array();
		$_d->additional_fields[1]['type']			= 'text';
		$_d->additional_fields[1]['key']			= 'redirect_url';
		$_d->additional_fields[1]['label']			= 'Redirect To URL';
		$_d->additional_fields[1]['placeholder']	= 'Manually set the URL to redirect to, this will override any option set above.';
		$_d->additional_fields[1]['tip']			= 'URLs which do not begin with http(s):// will automatically be prefixed with ' . site_url();

		$_d->additional_fields[2]				= array();
		$_d->additional_fields[2]['type']		= 'dropdown';
		$_d->additional_fields[2]['key']		= 'redirect_code';
		$_d->additional_fields[2]['label']		= 'Redirect Type';
		$_d->additional_fields[2]['class']		= 'chosen';
		$_d->additional_fields[2]['options']	= array(

			'302'	=> '302 Moved Temporarily',
			'301'	=> '301 Moved Permanently'

		);

		// --------------------------------------------------------------------------

		return $_d;
	}


	// --------------------------------------------------------------------------


	public function render( $_tpl_widgets = array(), $_tpl_additional_fields = array() )
	{
		$_url = '';

		if ( ! empty( $_tpl_additional_fields['redirect_url'] ) ) :

			$_url = $_tpl_additional_fields['redirect_url'];

		elseif(  ! empty( $_tpl_additional_fields['redirect_page_id'] ) ) :

			$_page = get_instance()->cms_page_model->get_by_id( $_tpl_additional_fields['redirect_page_id'] );

			if ( $_page && ! $_page->is_deleted && $_page->is_published ) :

				$_url = $_page->published->url;

			endif;

		endif;

		// --------------------------------------------------------------------------

		$_code = ! empty( $_tpl_additional_fields['redirect_code'] ) ? $_tpl_additional_fields['redirect_code'] : '';

		if ( $_url ) :

			redirect( $_url, 'location', $_code );

		else :

			show_404();

		endif;
	}
}

/* End of file template.php */
/* Location: ./modules/cms/templates/redirect/template.php */