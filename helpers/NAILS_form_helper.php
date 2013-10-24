<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * form_field
 *
 * Generates a form field (of type text, password or textarea)
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field' ) )
{
	function form_field( $field, $help = '' )
	{
		//	Set var defaults
		$_field					= array();
		$_field['id']			= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['readonly']		= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['error']		= isset( $field['error'] ) ? $field['error'] : FALSE;
		$_field['bucket']		= isset( $field['bucket'] ) ? $field['bucket'] : FALSE;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;
		$_field['data']			= isset( $field['data'] ) ? $field['data'] : array();

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error			= form_error( $_field['key'] ) || $_field['error'] ? 'error' : '';
		$_readonly		= $_field['readonly'] ? 'readonly="readonly"' : '';
		$_readonly_cls	= $_field['readonly'] ? 'readonly' : '';

		// --------------------------------------------------------------------------

		$_out  = '<div class="field ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . ' ' . $_field['type'] . '">';
		$_out .= '<label>';

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		//	Does the field have an id?
		$_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

		//	Any data attributes?
		$_data = '';
		foreach( $_field['data'] AS $attr => $value ) :

			$_data .= ' data-' . $attr . '="' . $value . '"';

		endforeach;

		//	Field
		$_withtip = $_help['title'] ? 'with-tip' : '';
		$_out .= '<span class="input ' . $_withtip . '">';

		switch ( $_field['type'] ) :

			case 'password' :

				$_out .= form_password( $_field['key'], set_value( $_field['key'], $_field['default'] ), $_field['id'] . 'class="' . $_field['class'] . '" placeholder="' . $_field['placeholder'] . '" ' . $_readonly . $_data );

			break;

			case 'textarea' :

				$_out .= form_textarea( $_field['key'], set_value( $_field['key'], $_field['default'] ), $_field['id'] . 'class="' . $_field['class'] . '" placeholder="' . $_field['placeholder'] . '" ' . $_readonly );

			break;

			case 'upload' :
			case 'file' :

				$_out .= form_upload( $_field['key'] );

			break;

			case 'text' :
			default :

				$_out .= form_input( $_field['key'], set_value( $_field['key'], $_field['default'] ), $_field['id'] . 'class="' . $_field['class'] . '" placeholder="' . $_field['placeholder'] . '" ' . $_readonly );

			break;

		endswitch;

		//	Tip
		$_out .= $_withtip ? img( $_help ) : '';

		//	Download original file, if type is file and original is available
		if ( ( $_field['type'] == 'file' || $_field['type'] == 'upload' ) && $_field['default'] ) :

			$_out .= '<span class="file-download">';

			$_ext = end( explode( '.', $_field['default'] ) );

			switch ( $_ext ) :

				case 'jpg' :
				case 'png' :
				case 'gif' :

					$_out .= 'Download: ' . anchor( cdn_serve( $_field['default'] ), img( cdn_thumb( $_field['default'], 35, 35 ) ), 'class="fancybox"' );

				break;

				// --------------------------------------------------------------------------

				default :

					$_out .= anchor( cdn_serve( $_field['default'], TRUE ), 'Download', 'class="awesome small" target="_blank"' );

				break;

			endswitch;

			$_out .= '</span>';

		endif;

		//	Error
		$_out .= '<span class="error">';
		if ( $_field['error'] ) :

			$_out .= $_field['error'] ;

		else :

			$_out .= form_error( $_field['key'], ' ', ' ' );

		endif;
		$_out .= '</span>';

		//	End .input
		$_out .= '</span>';

		$_out .= '</label>';
		$_out .= '</div>';

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_mm
 *
 * Generates a form field which uses the media manager to select a file
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_mm' ) )
{
	function form_field_mm( $field, $help = '' )
	{
		//	Set var defaults
		$_field					= array();
		$_field['id']			= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['readonly']		= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['error']		= isset( $field['error'] ) ? $field['error'] : FALSE;
		$_field['bucket']		= isset( $field['bucket'] ) ? $field['bucket'] : FALSE;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error			= form_error( $_field['key'] ) || $_field['error'] ? 'error' : '';
		$_readonly		= $_field['readonly'] ? 'readonly="readonly"' : '';
		$_readonly_cls	= $_field['readonly'] ? 'readonly' : '';

		// --------------------------------------------------------------------------

		//	Generate a unique ID for this field
		$_id = 'field_mm_' . md5( microtime() );

		// --------------------------------------------------------------------------

		$_out  = '<div class="field mm-file ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . ' ' . $_field['type'] . '" id="' . $_id . '">';
		$_out .= '<label>';

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		//	Does the field have an id?
		$_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

		//	Field

		//	Choose image button
		if ( $_field['bucket'] ) :

			$_nonce		= time();
			$_bucket	= urlencode( get_instance()->encrypt->encode( $_field['bucket'] . '|' . $_nonce , APP_PRIVATE_KEY ) );
			$_hash		= md5( $_field['bucket'] . '|' . $_nonce . '|' . APP_PRIVATE_KEY );

			$_url		= site_url( 'cdn/manager/browse' ) . '?callback=callback_' . $_id . '&bucket=' . $_bucket . '&hash=' . $_hash;

		else :

			$_url		= site_url( 'cdn/manager/browse' );

		endif;

		//	Is the site running on SSL? If so then change the protocol so as to avoid 'protocols don't match' errors
		if ( isset( $_SERVER['HTTPS'] ) && strtoupper( $_SERVER['HTTPS'] ) == 'ON' ) :

			$_url = str_replace( 'http://', 'https://', $_url );

		endif;

		$_out .= '<div class="mm-file-container">';

		$_out .= '<a href="' . $_url . '" data-fancybox-type="iframe" data-width="80%" data-height="80%" class="fancybox awesome" id="' . $_id . '-choose">' . lang( 'action_choose' ) . '</a>';

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		//	If there's post data, use that value instead
		$_field['default'] = set_value( $_field['key'], $_field['default'] );

		//	Remove button
		$_display = $_field['default'] ? 'inline-block' : 'none';

		$_out .= '<br /><a href="#" class="awesome small red mm-file-remove" id="' . $_id . '-remove" style="display:' . $_display . '" onclick="return remove_' . $_id . '();">' . lang( 'action_remove' ) . '</a>';

		//	If a default has been specified then show a download link
		$_out .= '<span id="' . $_id . '-preview" class="mm-file-download">';
		if ( $_field['default'] ) :

			$_out .= anchor( cdn_serve( $_field['default'], TRUE ), 'Download File' );

		endif;
		$_out .= '</span>';

		//	The actual field which is submitted
		$_out .= '<input type="hidden" name="' . $_field['key'] . '" id="' . $_id . '-field" value="' . $_field['default'] . '" />';

		//	Error
		if ( $_error && $_field['error'] ) :

			$_out .= '<span class="error">' . $_field['error'] . '</span>';

		elseif( $_error ) :

			$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		endif;

		$_out .= '</div>';

		$_out .= '</label>';
		$_out .= '</div>';

		// --------------------------------------------------------------------------

		//	Quick script to instanciate the field, not indented due to heredoc syntax
		get_instance()->load->library( 'cdn' );
		$_scheme = get_instance()->cdn->url_serve_scheme( TRUE );

		$_scheme = str_replace( '{{bucket}}', $_field['bucket'], $_scheme );

		//	Replace the Mustache style syntax; this could/does get used in mustache templates
		//	so these fields get stripped out

		$_scheme = str_replace( '{{filename}}', '{[filename]}', $_scheme );
		$_scheme = str_replace( '{{extension}}', '{[extension]}', $_scheme );

$_out .= <<<EOT

	<script type="text/javascript">

		$( '#$_id-choose' ).on( 'click', function()
		{
			var _href = $(this).attr( 'href' );
			_href += _href.indexOf( '?' ) >= 0 ? '&is_fancybox=1' : '?is_fancybox=1';

			if ( $.fancybox.isOpen && $.fancybox.opts.type != 'iframe' )
			{
				_href += '&reopen_fancybox=' + encodeURIComponent( $.fancybox.opts.href );
				$.fancybox.close();
			}

			var _w = $(this).data( 'width' ) ? $(this).data( 'width' ) : null;
			var _h = $(this).data( 'height' ) ? $(this).data( 'height' ) : null;

			$.fancybox.open({
				href: _href,
				type: 'iframe',
				width: _w,
				height: _h
			});

			return false;
		});

		function callback_$_id( file, id, reopen )
		{
			if ( file.length == 0 )
			{
				remove_$_id();

				// --------------------------------------------------------------------------

				//	Reopen facybox?
				if ( reopen.length )
				{
					$.fancybox.open({
						href:reopen
					});
				}

				return;
			}

			// --------------------------------------------------------------------------

			var _scheme = '$_scheme';
			var _file	= file.split( '.' );

			_scheme = _scheme.replace( '{[filename]}', _file[0] );
			_scheme = _scheme.replace( '{[extension]}', '.' + _file[1] );

			$( '#$_id-preview' ).html( '<a href="' + _scheme + '">Download</a>' );
			$( '#$_id-field' ).val( id );
			$( '#$_id-remove' ).css( 'display', 'inline-block' );

			// --------------------------------------------------------------------------

			//	Reopen facybox?
			if ( reopen.length )
			{
				$.fancybox.open({
					href:reopen
				});
			}
		}

		function remove_$_id()
		{
			$( '#$_id-preview' ).html( '' );
			$( '#$_id-field' ).val( '' );
			$( '#$_id-remove' ).css( 'display', 'none' );

			return false;
		}

	</script>

EOT;

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_image
 *
 * Generates a form field which uses the media manager to select an image
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_mm_image' ) )
{
	function form_field_mm_image( $field, $help = '' )
	{
		//	Set var defaults
		$_field					= array();
		$_field['id']			= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['readonly']		= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['error']		= isset( $field['error'] ) ? $field['error'] : FALSE;
		$_field['bucket']		= isset( $field['bucket'] ) ? $field['bucket'] : FALSE;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error			= form_error( $_field['key'] ) || $_field['error'] ? 'error' : '';
		$_readonly		= $_field['readonly'] ? 'readonly="readonly"' : '';
		$_readonly_cls	= $_field['readonly'] ? 'readonly' : '';

		// --------------------------------------------------------------------------

		//	Generate a unique ID for this field
		$_id = 'field_mm_image_' . md5( microtime() );

		// --------------------------------------------------------------------------

		$_out  = '<div class="field mm-image ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . ' ' . $_field['type'] . '" id="' . $_id . '">';
		$_out .= '<label>';

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		//	Does the field have an id?
		$_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

		//	Field

		//	If there's post data, sue that value instead
		$_field['default'] = set_value( $_field['key'], $_field['default'] );

		//	If a default has been specified then show a preview
		$_out .= '<span id="' . $_id . '-preview" class="mm-image-preview">';
		if ( $_field['default'] ) :

			$_out .= img( cdn_scale( $_field['default'], 100, 100 ) );

		endif;
		$_out .= '</span>';

		//	Choose image button
		if ( $_field['bucket'] ) :

			$_nonce		= time();
			$_bucket	= urlencode( get_instance()->encrypt->encode( $_field['bucket'] . '|' . $_nonce , APP_PRIVATE_KEY ) );
			$_hash		= md5( $_field['bucket'] . '|' . $_nonce . '|' . APP_PRIVATE_KEY );

			$_url		= site_url( 'cdn/manager/browse' ) . '?callback=callback_' . $_id . '&bucket=' . $_bucket . '&hash=' . $_hash;

		else :

			$_url		= site_url( 'cdn/manager/browse' );

		endif;

		//	Is the site running on SSL? If so then change the protocol so as to avoid 'protocols don't match' errors
		if ( isset( $_SERVER['HTTPS'] ) && strtoupper( $_SERVER['HTTPS'] ) == 'ON' ) :

			$_url = str_replace( 'http://', 'https://', $_url );

		endif;

		$_out .= '<a href="' . $_url . '" data-fancybox-type="iframe" data-width="80%" data-height="80%" class="awesome" id="' . $_id . '-choose">' . lang( 'action_choose' ) . '</a>';

		//	Remove button
		$_display = $_field['default'] ? 'inline-block' : 'none';

		$_out .= '<br /><a href="#" class="awesome small red mm-image-remove" id="' . $_id . '-remove" style="display:' . $_display . '" onclick="return remove_' . $_id . '();">' . lang( 'action_remove' ) . '</a>';

		//	The actual field which is submitted
		$_out .= '<input type="hidden" name="' . $_field['key'] . '" id="' . $_id . '-field" value="' . $_field['default'] . '" />';

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		//	Error
		if ( $_error && $_field['error'] ) :

			$_out .= '<span class="error">' . $_field['error'] . '</span>';

		elseif( $_error ) :

			$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		endif;

		$_out .= '</label>';
		$_out .= '</div>';

		// --------------------------------------------------------------------------

		//	Quick script to instanciate the field, not indented due to heredoc syntax
		get_instance()->load->library( 'cdn' );
		$_scheme = get_instance()->cdn->url_scale_scheme();

		$_scheme = str_replace( '{{width}}', 100, $_scheme );
		$_scheme = str_replace( '{{height}}', 100, $_scheme );
		$_scheme = str_replace( '{{bucket}}', $_field['bucket'], $_scheme );

$_out .= <<<EOT

	<script type="text/javascript">

		$( '#$_id-choose' ).on( 'click', function()
		{
			var _href = $(this).attr( 'href' );
			_href += _href.indexOf( '?' ) >= 0 ? '&is_fancybox=1' : '?is_fancybox=1';

			if ( $.fancybox.isOpen && $.fancybox.opts.type != 'iframe' )
			{
				_href += '&reopen_fancybox=' + encodeURIComponent( $.fancybox.opts.href );
				$.fancybox.close();
			}

			var _w = $(this).data( 'width' ) ? $(this).data( 'width' ) : null;
			var _h = $(this).data( 'height' ) ? $(this).data( 'height' ) : null;

			$.fancybox.open({
				href: _href,
				type: 'iframe',
				width: _w,
				height: _h
			});

			return false;
		});

		function callback_$_id( file, id, reopen )
		{
			if ( file.length == 0 )
			{
				remove_$_id();

				// --------------------------------------------------------------------------

				//	Reopen facybox?
				if ( reopen.length )
				{
					$.fancybox.open({
						href:reopen
					});
				}

				return;
			}

			// --------------------------------------------------------------------------

			var _scheme	= '$_scheme';
			var _file	= file.split( '.' );

			_scheme = _scheme.replace( '{{filename}}', _file[0] );
			_scheme = _scheme.replace( '{{extension}}', '.' + _file[1] );

			$( '#$_id-preview' ).html( '<img src="' + _scheme + '" / >' );
			$( '#$_id-field' ).val( id );
			$( '#$_id-remove' ).css( 'display', 'inline-block' );

			// --------------------------------------------------------------------------

			//	Reopen facybox?
			if ( reopen.length )
			{
				$.fancybox.open({
					href:reopen
				});
			}
		}

		function remove_$_id()
		{
			$( '#$_id-preview' ).html( '' );
			$( '#$_id-field' ).val( '' );
			$( '#$_id-remove' ).css( 'display', 'none' );

			return false;
		}

	</script>

EOT;

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------

if ( ! function_exists( 'form_field_multiimage' ) )
{
	function form_field_multiimage( $field, $help = '' )
	{
		//	Set var defaults
		$_field					= array();
		$_field['id']			= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['readonly']		= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['error']		= isset( $field['error'] ) ? $field['error'] : FALSE;
		$_field['bucket']		= isset( $field['bucket'] ) ? $field['bucket'] : FALSE;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error			= form_error( $_field['key'] ) || $_field['error'] ? 'error' : '';
		$_readonly		= $_field['readonly'] ? 'readonly="readonly"' : '';
		$_readonly_cls	= $_field['readonly'] ? 'readonly' : '';

		// --------------------------------------------------------------------------

		//	Sanitize the key
		$_field['key'] .= substr( $_field['key'], -2 ) != '[]' ? '[]' : '';

		// --------------------------------------------------------------------------

		//	Generate a unique ID for this field
		$_id = 'field_multiimage_' . md5( microtime() );

		// --------------------------------------------------------------------------

		$_out  = '<div class="field multiimage ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . ' ' . $_field['type'] . '" id="' . $_id . '">';
		$_out .= '<label>';

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		//	Does the field have an id?
		$_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

		//	Field
		$_out .= '<span class="input">';

		//	Set the default
		$_field['default'] = set_value( $_field['key'], $_field['default'] );

		//	Uploadify not available error
		$_out .= '<p class="system-alert error no-close" id="' . $_id . '-uploadify-not-available">';
		$_out .= '<strong>Configuration Error.</strong> Uploadify is not available.';
		$_out .= '</p>';

		$_out .= '<div id="' . $_id . '-uploadify-available" style="display:none;">';

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		//	Render any defaults
		if ( is_array( $_field['default'] ) ) :

			$_tipclass = $_help['title'] ? 'has-tip' : '';
			$_out .= '<ul id="' . $_id . '-filelist" class="filelist ' . $_tipclass . '">';
			foreach( $_field['default'] AS $file ) :

				$_out .= '<li class="item">';
				$_out .= '<a href="#" class="delete"></a>';
				$_out .= img( cdn_thumb( $file, 92, 92 ) );
				$_out .= form_hidden( $_field['key'], $file );
				$_out .= '</li>';

			endforeach;
			$_out .= '</ul>';

		else :

			$_tipclass = $_help['title'] ? 'has-tip' : '';
			$_out .= '<ul id="' . $_id . '-filelist" class="filelist ' . $_tipclass . '">';
			$_out .= '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>';
			$_out .= '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>';
			$_out .= '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>';
			$_out .= '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>';
			$_out .= '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>';
			$_out .= '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>';
			$_out .= '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>';
			$_out .= '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>';
			$_out .= '<li class="empty">No Images, add some now.</li>';
			$_out .= '</ul>';

		endif;

		//	Show the upload button
		$_out .= '<button id="' . $_id . '-uploadify">Choose Images</button>';

		$_out .= '</div>';


		//	Error
		if ( $_error && $_field['error'] ) :

			$_out .= '<span class="error">' . $_field['error'] . '</span>';

		elseif( $_error ) :

			$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		endif;

		$_out .= '</span>';
		$_out .= '</label>';
		$_out .= '</div>';

		// --------------------------------------------------------------------------

		//	Quick script to instanciate the field, not indented due to heredoc syntax
		get_instance()->load->library( 'cdn' );

		$_movie_url		= NAILS_URL . 'swf/jquery.uploadify/uploadify.swf';
		$_upload_url	= site_url( 'api/cdnapi/object_create/script.php' );
		$_upload_token	= get_instance()->cdn->generate_api_upload_token();
		$_bucket		= $_field['bucket'];

$_out .= <<<EOT

	<script type="text/javascript">

	if ( typeof( $.fn.uploadify ) === 'function' )
	{
		$( '#$_id-uploadify-not-available' ).hide();
		$( '#$_id-uploadify-available' ).show();

		// --------------------------------------------------------------------------

		$( '#$_id-uploadify' ).uploadify(
		{
			'debug': false,
			'auto': false,
			'swf': '$_movie_url',
			'uploader': '$_upload_url',
			'fileObjName': 'upload',
			'fileTypeExts': '*.gif; *.jpg; *.jpeg; *.png',
			'queueID': '$_id-filelist',
			'formData':
			{
				'token': '$_upload_token',
				'bucket': '$_bucket',
				'return': 'URL|THUMB|100x100,34x34'
			},
			'itemTemplate': '<li class="item"><a href="#" class="delete"></a><div class="progress"></div></li>',
			'onSelect': function()
			{
				if ( $( '#$_id-filelist li' ).length )
				{
					$('#$_id-filelist').removeClass( 'empty' );
				}
			},
		});

		if ( typeof( $.fn.sortable ) === 'function' )
		{
			$('#$_id-filelist').disableSelection().sortable({
				placeholder: 'item placeholder',
				items: "li.item"
			});
		}
	}

	</script>

EOT;

		// --------------------------------------------------------------------------

		return $_out;
	}
}



// --------------------------------------------------------------------------


/**
 * form_field_date
 *
 * Generates a form field (of type select) for dates
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_date' ) )
{
	function form_field_date( $field, $help = '' )
	{
		$_field					= $field;
		$_field['type']			= 'date';
		$_field['class']		= isset( $field['class'] ) ? $field['class'] . ' date' : 'date';
		$_field['placeholder']	= 'YYYY-MM-DD';

		return form_field( $_field, $help );
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_datetime
 *
 * Generates a form field (of type select) for datetime
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_datetime' ) )
{
	function form_field_datetime( $field, $help = '' )
	{
		$_field					= $field;
		$_field['type']			= 'datetime';
		$_field['class']		= isset( $field['class'] ) ? $field['class'] . ' datetime' : 'datetime';
		$_field['placeholder']	= 'YYYY-MM-DD HH:mm:ss';

		return form_field( $_field, $help );
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_dropdown
 *
 * Generates a form field (of type select)
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_dropdown' ) )
{
	function form_field_dropdown( $field, $options, $help = '' )
	{
		//	Set var defaults
		$_field					= array();
		$_field['id']			= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;
		$_field['readonly']		= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['data']			= isset( $field['data'] ) ? $field['data'] : array();

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error			= form_error( $_field['key'] ) ? 'error' : '';
		$_readonly		= $_field['readonly'] ? 'disabled="disabled"' : '';
		$_readonly_cls	= $_field['readonly'] ? 'readonly' : '';

		// --------------------------------------------------------------------------

		$_out  = '<div class="field dropdown ' . $_error . ' ' . $_readonly_cls . ' ' . $_field['oddeven'] . '">';
		$_out .= '<label>';

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		// --------------------------------------------------------------------------

		//	Field
		$_withtip = $_help['title'] ? 'with-tip' : '';
		$_out .= '<span class="input ' . $_withtip . '">';

		//	Does the field have an id?
		$_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

		//	Any data attributes?
		$_data = '';
		foreach( $_field['data'] AS $attr => $value ) :

			$_data .= ' data-' . $attr . '="' . $value . '"';

		endforeach;

		//	Get the selected options
		if ( $_POST ) :

			$_selected = set_value( $_field['key'] );

		else :

			//	Use the 'default' variabel
			$_selected = $_field['default'];

		endif;

		//	Build the select
		$_placeholder = ! is_null( $_field['placeholder'] ) ? 'data-placeholder="' . $_field['placeholder'] . '"' : '';
		$_out .= '<select name="' . $_field['key'] . '" class="' . $_field['class'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';


		foreach ( $options AS $value => $label ) :

			//	Selected?
			$_checked = $value == $_selected ? 'selected="selected"' : '';
			$_out .= '<option value="' . $value . '" ' . $_checked . '>' . $label . '</option>';

		endforeach;
		$_out .= '</select>';

		if ( $_readonly ) :

			$_out .= form_hidden( $_field['key'], $_field['default'] );

		endif;

		// --------------------------------------------------------------------------

		if ( $_readonly ) :

			$_out .= form_hidden( $_field['key'], $_field['default'] );

		endif;

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		$_out .= '</span>';

		$_out .= '</label>';
		$_out .= '</div>';

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_dropdown
 *
 * Generates a form field (of type select)
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_dropdown_multiple' ) )
{
	function form_field_dropdown_multiple( $field, $options, $help = '' )
	{
		//	Set var defaults
		$_field					= array();
		$_field['id']			= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['type']			= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;
		$_field['readonly']		= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['data']			= isset( $field['data'] ) ? $field['data'] : array();

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error			= form_error( $_field['key'] ) ? 'error' : '';
		$_readonly		= $_field['readonly'] ? 'disabled="disabled"' : '';
		$_readonly_cls	= $_field['readonly'] ? 'readonly' : '';

		// --------------------------------------------------------------------------

		$_out  = '<div class="field dropdown ' . $_error . ' ' . $_readonly_cls . ' ' . $_field['oddeven'] . '">';
		$_out .= '<label>';

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		// --------------------------------------------------------------------------

		//	Field
		$_withtip = $_help['title'] ? 'with-tip' : '';
		$_out .= '<span class="input ' . $_withtip . '">';

		//	Does the field have an id?
		$_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

		//	Any data attributes?
		$_data = '';
		foreach( $_field['data'] AS $attr => $value ) :

			$_data .= ' data-' . $attr . '="' . $value . '"';

		endforeach;

		//	Any defaults?
		$_field['default'] = (array) $_field['default'];

		//	Get the selected options
		if ( $_POST ) :

			$_key = str_replace( '[]', '', $_field['key'] );
			$_selected = isset( $_POST[$_key] ) ? $_POST[$_key] : array();

		else :

			//	Use the 'default' variabel
			$_selected = $_field['default'];

		endif;

		//	Build the select
		$_placeholder = ! is_null( $_field['placeholder'] ) ? 'data-placeholder="' . $_field['placeholder'] . '"' : '';
		$_out .= '<select name="' . $_field['key'] . '" multiple="multiple" class="' . $_field['class'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';

		foreach ( $options AS $value => $label ) :

			//	Selected?
			$_checked = array_search( $value, $_selected ) !== FALSE ? 'selected="selected"' : '';
			$_out .= '<option value="' . $value . '" ' . $_checked . '>' . $label . '</option>';

		endforeach;
		$_out .= '</select>';

		if ( $_readonly ) :

			$_out .= form_hidden( $_field['key'], $_field['default'] );

		endif;

		// --------------------------------------------------------------------------

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		$_out .= '</span';

		$_out .= '</label>';
		$_out .= '</div>';

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field_boolean
 *
 * Generates a form field (of type select, with two options: yes and no)
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_boolean' ) )
{
	function form_field_boolean( $field, $help = '' )
	{
		$_ci =& get_instance();

		// --------------------------------------------------------------------------

		//	Set var defaults
		$_field					= array();
		$_field['id']			= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;
		$_field['text_on']		= isset( $field['text_on'] ) ? $field['text_on'] : 'ON';
		$_field['text_off']		= isset( $field['text_off'] ) ? $field['text_off'] : 'OFF';
		$_field['data']			= isset( $field['data'] ) ? $field['data'] : array();
		$_field['readonly']		= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error			= form_error( $_field['key'] ) ? 'error' : '';
		$_readonly		= $_field['readonly'] ? 'disabled="disabled"' : '';
		$_readonly_cls	= $_field['readonly'] ? 'readonly' : '';

		// --------------------------------------------------------------------------

		$_out  = '<div class="field checkbox boolean ' . $_error . ' ' . $_field['oddeven'] . ' ' . $_readonly_cls . '" data-text-on="' . $_field['text_on'] . '" data-text-off="' . $_field['text_off'] . '">';

		//	Does the field have an id?
		$_field['id'] = $_field['id'] ? 'id="' . $_field['id'] . '" ' : '';

		//	Any data attributes?
		$_data = '';
		foreach( $_field['data'] AS $attr => $value ) :

			$_data .= ' data-' . $attr . '="' . $value . '"';

		endforeach;

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		//	Field
		$_out .= '<span class="input">';
		$_selected = set_value( $_field['key'], (bool) $_field['default'] );

		$_out .= '<div class="toggle toggle-modern"></div>';
		$_out .= form_checkbox( $_field['key'], TRUE, $_selected, $_field['id'] . $_data . ' ' . $_readonly );

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		$_out .= '</span>';
		$_out .= '</div>';

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field
 *
 * Generates a form field containing radio buttons
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_radio' ) )
{
	function form_field_radio( $field, $options, $help = '' )
	{
		$_ci =& get_instance();

		// --------------------------------------------------------------------------

		//	Set var defaults
		$_field					= array();
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error = form_error( $_field['key'] ) ? 'error' : '';

		// --------------------------------------------------------------------------

		$_out  = '<div class="field radio ' . $_error . ' ' . $_field['oddeven'] . '">';

		//	First option
		$_out .= '<label>';

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		//	Does the field have an id?
		$_id = isset( $options[0]['id'] ) && $options[0]['id'] ? 'id="' . $options[0]['id'] . '-0" ' : '';

		//	Field
		if ( $_ci->input->post( $_field['key'] ) ) :

			$_selected = $_ci->input->post( $_field['key'] ) == $options[0]['value'] ? TRUE : FALSE;

		else :

			$_selected = isset( $options[0]['selected'] ) ? $options[0]['selected'] : FALSE;

		endif;
		$_out .= form_radio( $_field['key'], $options[0]['value'], $_selected, $_id ) . '<span class="text">' . $options[0]['label'] . '</span>';

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		$_out .= '</label>';


		//	Remaining options
		for ( $i = 1; $i < count( $options ); $i++ ) :

			$_out .= '<label>';

			//	Label
			$_out .= '<span class="label">&nbsp;</span>';

			//	Input
			if ( $_ci->input->post( $_field['key'] ) ) :

				$_selected = $_ci->input->post( $_field['key'] ) == $options[$i]['value'] ? TRUE : FALSE;

			else :

				$_selected = isset( $options[$i]['selected'] ) ? $options[$i]['selected'] : FALSE;

			endif;

			//	Does the field have an ID?
			$_id = isset( $options[$i]['id'] ) && $options[$i]['id'] ? 'id="' . $options[$i]['id'] . '-' . $i . '" ' : '';

			$_out .= form_radio( $_field['key'], $options[$i]['value'], $_selected ) . '<span class="text">' . $options[$i]['label'] . '</span>';

			$_out .= '</label>';

		endfor;

		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		$_out .= '</div>';

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field
 *
 * Generates a form field containing checboxes
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_checkbox' ) )
{
	function form_field_checkbox( $field, $options, $help = '' )
	{
		$_ci =& get_instance();

		// --------------------------------------------------------------------------

		//	Set var defaults
		$_field					= array();
		$_field['id']			= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['oddeven']		= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']			= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']		= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']		= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']	= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']		= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['class']		= isset( $field['class'] ) ? $field['class'] : FALSE;

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy';
		$_help['title']	= is_array( $help ) && isset( $help['title'] ) ? $help['title'] : NULL;
		$_help['title']	= is_string( $help ) ? $help : $_help['title'];

		$_error = form_error( $_field['key'] ) ? 'error' : '';

		// --------------------------------------------------------------------------

		$_out  = '<div class="field checkbox ' . $_error . ' ' . $_field['oddeven'] . '">';

		//	First option
		$_out .= '<label>';

		//	Label
		$_out .= '<span class="label">';
		$_out .= $_field['label'];
		$_out .= $_field['required'] ? '*' : '';
		$_out .= $_field['sub_label'] ? '<small>' . $_field['sub_label'] . '</small>' : '';
		$_out .= '</span>';

		$_out .= '<span class="input">';

		//	Does the field have an id?
		$_id = isset( $options[0]['id'] ) && $options[0]['id'] ? 'id="' . $options[0]['id'] . '-0" ' : '';

		//	Field
		if ( substr( $_field['key'], -2 ) == '[]' ) :

			//	Field is an array, need to look for the value
			$_values		= $_ci->input->post( substr( $_field['key'], 0, -2 ) );
			$_data_selected	= isset( $options[0]['selected'] ) ? $options[0]['selected'] : FALSE;
			$_selected		= $_ci->input->post() ? FALSE : $_data_selected;

			if ( is_array( $_values ) && array_search( $options[0]['value'], $_values ) !== FALSE ) :

				$_selected = TRUE;

			endif;

		else :

			//	Normal field, continue as normal Mr Norman!
			if ( $_ci->input->post( $_field['key'] ) ) :

				$_selected = $_ci->input->post( $_field['key'] ) == $options[0]['value'] ? TRUE : FALSE;

			else :

				$_selected = isset( $options[0]['selected'] ) ? $options[0]['selected'] : FALSE;

			endif;

		endif;

		$_key	= isset( $options[0]['key'] ) ? $options[0]['key'] : $_field['key'];

		$_out .= form_checkbox( $_key, $options[0]['value'], $_selected, $_id ) . '<span class="text">' . $options[0]['label'] . '</span>';

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		$_out .= '</span>';
		$_out .= '</label>';


		//	Remaining options
		for ( $i = 1; $i < count( $options ); $i++ ) :

			$_out .= '<label>';

			//	Label
			$_out .= '<span class="label">&nbsp;</span>';
			$_out .= '<span class="input">';

			//	Does the field have an id?
			$_id = isset( $options[$i]['id'] ) && $options[$i]['id'] ? 'id="' . $options[$i]['id'] . '-' . $i . '" ' : '';

			//	Input
			if ( substr( $_field['key'], -2 ) == '[]' ) :

				//	Field is an array, need to look for the value
				$_values	= $_ci->input->post( substr( $_field['key'], 0, -2 ) );
				$_data_selected	= isset( $options[$i]['selected'] ) ? $options[$i]['selected'] : FALSE;
				$_selected		= $_ci->input->post() ? FALSE : $_data_selected;

				if ( is_array( $_values ) && array_search( $options[$i]['value'], $_values ) !== FALSE ) :

					$_selected = TRUE;

				endif;

			else :

				//	Normal field, continue as normal Mr Norman!
				if ( $_ci->input->post( $_field['key'] ) ) :

					$_selected = $_ci->input->post( $_field['key'] ) == $options[$i]['value'] ? TRUE : FALSE;

				else :

					$_selected = isset( $options[$i]['selected'] ) ? $options[$i]['selected'] : FALSE;

				endif;

			endif;

			$_key = isset( $options[$i]['key'] ) ? $options[$i]['key'] : $_field['key'];

			$_out .= form_checkbox( $_key, $options[$i]['value'], $_selected, $_id ) . '<span class="text">' . $options[$i]['label'] . '</span>';

			$_out .= '</span>';
			$_out .= '</label>';

		endfor;

		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		$_out .= '</div>';

		// --------------------------------------------------------------------------

		return $_out;
	}
}


// --------------------------------------------------------------------------


/**
 * form_field
 *
 * Generates a form field containing radio buttons
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if ( ! function_exists( 'form_field_submit' ) )
{
	function form_field_submit( $button_value = 'Submit', $button_name = 'submit', $button_attributes = '' )
	{
		$_out  = '<div class="field submit">';

		//	Label
		$_out .= '<span class="label">&nbsp;</span>';

		//	field
		$_out .= '<span class="input">';
		$_out .= form_submit( $button_name, $button_value, $button_attributes );
		$_out .= '</span>';

		$_out .= '</div>';

		// --------------------------------------------------------------------------

		return $_out;
	}
}