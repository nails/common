<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Nails_CMS_Widget_image extends Nails_CMS_Widget
{
	static function details()
	{
		$_d			= parent::details();
		$_d->name	= 'Image';
		$_d->slug	= 'Widget_image';
		$_d->iam	= 'Nails_CMS_Widget_image';
		$_d->info	= 'A single image.';

		return $_d;
	}

	// --------------------------------------------------------------------------

	private $_key;
	private $_body;

	// --------------------------------------------------------------------------

	public function __construct()
	{
		$this->_key		= 'image';
		$this->_image	= '';
		$this->_align	= 'center';
	}


	// --------------------------------------------------------------------------


	public function setup( $data )
	{
		if ( isset( $data['image'] ) ) :

			$this->_image = $data['image'];

		endif;

		// --------------------------------------------------------------------------

		if ( isset( $data['align'] ) ) :

			$this->_align = $data['align'];

		endif;

		// --------------------------------------------------------------------------

		if ( isset( $data['key'] ) && ! is_null( $data['key'] ) ) :

			$this->_key = $data['key'];

		endif;
	}

	// --------------------------------------------------------------------------

	public function render()
	{
		$_img = array();
		$_img['src']	= $this->_image;
		$_img['class']	= 'scale-with-grid ' . $this->_align;

		return '<div class="row">' . img( $_img ) . '</div>';
	}

	// --------------------------------------------------------------------------

	public function get_editor_html()
	{
		$_details = self::details();

		get_instance()->load->library( 'cdn' );

		//	Include the slug as a hidden field, required for form rebuilding
		$_out = form_hidden( $this->_key . '[slug]', $_details->slug );

		// --------------------------------------------------------------------------

		//	Return editor HTML
		$_nonce		= uniqid();
		$_bucket	= urlencode( get_instance()->encrypt->encode( 'cms|' . $_nonce , APP_PRIVATE_KEY ) );
		$_hash		= md5( 'cms|' . $_nonce . '|' . APP_PRIVATE_KEY );
		$_url		= site_url( 'cdn/manager/browse/image' ) . '?is_fancybox=true&callback=callback_' . md5( $this->_key . $_nonce ) . '&bucket=' . $_bucket . '&hash=' . $_hash;


		$_out .= '<label class="image-chooser">';
		$_out .= '<span class="label">Image URL:</span>';
		$_out .= form_input( $this->_key . '[image]', set_value( $this->_key . '[image]', $this->_image ), 'id="image-url-' . $_nonce . '"' );
		$_out .= '<a href="' . $_url . '" data-fancybox-type="iframe" class="fancybox-' . $_nonce .' awesome green">Media Library</a>';
		$_out .= '<div class="clear"></div>';
		$_out .= '</label>';

		//	Alignment
		$_options = array(
			'left' => 'Left',
			'center' => 'Centre',
			'right' => 'Right',
		);

		$_out .= '<label>';
		$_out .= '<span class="label">Image Alignment:</span>';
		$_out .= form_dropdown( $this->_key . '[align]', $_options, set_value( $this->_key . '[align]', $this->_align ) );
		$_out .= '<div class="clear"></div>';
		$_out .= '</label>';

		// --------------------------------------------------------------------------

		//	Callback
		$_url_scheme = get_instance()->cdn->url_serve_scheme();
		$_url_scheme = str_replace( '{{bucket}}',		'cms',				$_url_scheme );
		$_url_scheme = str_replace( '{{filename}}',		'[[filename]]',		$_url_scheme );
		$_url_scheme = str_replace( '{{extension}}',	'[[extension]]',	$_url_scheme );

		$_out .= '<script type="text/javascript">';
		$_out .= '$(\'a.fancybox-' . $_nonce . '\').fancybox();';
		$_out .= 'function callback_' . md5( $this->_key . $_nonce ) . '( file ) {';
		$_out .= 'var _url = \'' . $_url_scheme . '\';';
		$_out .= 'var _file = file.split( \'.\' );';
		$_out .= '_url = _url.replace( \'[[filename]]\', _file[0] );';
		$_out .= '_url = _url.replace( \'[[extension]]\', \'.\' + _file[1] );';
		$_out .= '$(\'#image-url-' . $_nonce . '\').val(_url);';
		$_out .= '}';
		$_out .= '</script>';

		// --------------------------------------------------------------------------

		return $_out;
	}
}