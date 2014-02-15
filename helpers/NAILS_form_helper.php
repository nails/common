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
		$_field				= array();
		$_field_id			= isset( $field['id'] )				? $field['id']			: NULL;
		$_field_type		= isset( $field['type'] )			? $field['type']		: 'text';
		$_field_oddeven		= isset( $field['oddeven'] )		? $field['oddeven']		: NULL;
		$_field_key			= isset( $field['key'] )			? $field['key']			: NULL;
		$_field_label		= isset( $field['label'] )			? $field['label']		: NULL;
		$_field_default		= isset( $field['default'] )		? $field['default']		: NULL;
		$_field_sub_label	= isset( $field['sub_label'] )		? $field['sub_label']	: NULL;
		$_field_required	= isset( $field['required'] )		? $field['required']	: FALSE;
		$_field_placeholder	= isset( $field['placeholder'] )	? $field['placeholder']	: NULL;
		$_field_readonly	= isset( $field['readonly'] )		? $field['readonly']	: FALSE;
		$_field_error		= isset( $field['error'] )			? $field['error']		: FALSE;
		$_field_bucket		= isset( $field['bucket'] )			? $field['bucket']		: FALSE;
		$_field_class		= isset( $field['class'] )			? $field['class']		: FALSE;
		$_field_data		= isset( $field['data'] )			? $field['data'] 		: array();

		$_help				= array();
		$_help['src']		= is_array( $help ) && isset( $help['src'] )	? $help['src']		: NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']		= is_array( $help ) && isset( $help['class'] )	? $help['class']	: 'help';
		$_help['rel']		= is_array( $help ) && isset( $help['rel'] )	? $help['rel']		: 'tipsy-left';
		$_help['title']		= is_array( $help ) && isset( $help['title'] )	? $help['title']	: NULL;
		$_help['title']		= is_string( $help ) ? $help : $_help['title'];

		$_error				= form_error( $_field_key ) || $_field_error ? 'error' : '';
		$_error_class		= $_error ? 'error' : '';
		$_readonly			= $_field_readonly ? 'readonly="readonly"' : '';
		$_readonly_cls		= $_field_readonly ? 'readonly' : '';

		// --------------------------------------------------------------------------

		//	Is the label required?
		$_field_label .= $_field_required ? '*' : '';

		//	Prep sublabel
		$_field_sub_label = $_field_sub_label ? '<small>' . $_field_sub_label . '</small>' : '';

		//	Has the field got a tip?
		$_tipclass	= $_help['title'] ? 'with-tip' : '';
		$_tip 		= $_help['title'] ? img( $_help ) : '';

		// --------------------------------------------------------------------------

		//	Prep the field's attributes
		$_attr = '';

		//	Does the field have an id?
		$_attr .= $_field_id ? 'id="' . $_field_id . '" ' : '';

		//	Any data attributes?
		foreach( $_field_data AS $attr => $value ) :

			$_attr .= ' data-' . $attr . '="' . $value . '"';

		endforeach;

		// --------------------------------------------------------------------------

		//	Generate the field's HTML
		switch ( $_field_type ) :

			case 'password' :

				$_field_html = form_password( $_field_key, NULL, $_attr . ' class="' . $_field_class . '" placeholder="' . $_field_placeholder . '" ' . $_readonly );

			break;

			case 'textarea' :

				$_field_html = form_textarea( $_field_key, set_value( $_field_key, $_field_default ), $_attr . ' class="' . $_field_class . '" placeholder="' . $_field_placeholder . '" ' . $_readonly );

			break;

			case 'upload' :
			case 'file' :

				$_field_html = form_upload( $_field_key, NULL, $_attr . ' class="' . $_field_class . '" placeholder="' . $_field_placeholder . '" ' . $_readonly );

			break;

			case 'text' :
			default :

				$_field_html = form_input( $_field_key, set_value( $_field_key, $_field_default ), $_attr . ' class="' . $_field_class . '" placeholder="' . $_field_placeholder . '" ' . $_readonly );

			break;

		endswitch;

		//	Download original file, if type is file and original is available
		if ( ( $_field_type == 'file' || $_field_type == 'upload' ) && $_field_default ) :

			$_field_html .= '<span class="file-download">';

			$_ext = end( explode( '.', $_field_default ) );

			switch ( $_ext ) :

				case 'jpg' :
				case 'png' :
				case 'gif' :

					$_field_html .= 'Download: ' . anchor( cdn_serve( $_field_default ), img( cdn_thumb( $_field_default, 35, 35 ) ), 'class="fancybox"' );

				break;

				// --------------------------------------------------------------------------

				default :

					$_field_html .= anchor( cdn_serve( $_field_default, TRUE ), 'Download', 'class="awesome small" target="_blank"' );

				break;

			endswitch;

			$_field_html .= '</span>';

		endif;

		// --------------------------------------------------------------------------

		//	Errors
		if ( $_error && $_field_error ) :

			$_error = '<span class="error">' . $_field_error . '</span>';

		elseif( $_error ) :

			$_error = form_error( $_field_key, '<span class="error">', '</span>' );

		endif;

		// --------------------------------------------------------------------------

$_out = <<<EOT

	<div class="field $_error_class $_field_oddeven $_readonly_cls $_field_type">
		<label>
			<span class="label">
				$_field_label
				$_field_sub_label
			</span>
			<span class="input $_tipclass">
				$_field_html
				$_tip
				$_error
			<span>
		</label>
	</div>


EOT;

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
		$_field['id']			= isset( $field['id'] )				? $field['id']			: NULL;
		$_field['type']			= isset( $field['type'] )			? $field['type']		: 'text';
		$_field['oddeven']		= isset( $field['oddeven'] )		? $field['oddeven']		: NULL;
		$_field['key']			= isset( $field['key'] )			? $field['key']			: NULL;
		$_field['label']		= isset( $field['label'] )			? $field['label']		: NULL;
		$_field['default']		= isset( $field['default'] )		? $field['default']		: NULL;
		$_field['sub_label']	= isset( $field['sub_label'] )		? $field['sub_label']	: NULL;
		$_field['required']		= isset( $field['required'] )		? $field['required']	: FALSE;
		$_field['placeholder']	= isset( $field['placeholder'] )	? $field['placeholder']	: NULL;
		$_field['readonly']		= isset( $field['readonly'] )		? $field['readonly']	: FALSE;
		$_field['error']		= isset( $field['error'] )			? $field['error']		: FALSE;
		$_field['bucket']		= isset( $field['bucket'] )			? $field['bucket']		: FALSE;
		$_field['class']		= isset( $field['class'] )			? $field['class']		: FALSE;
		$_field['data']			= isset( $field['data'] )			? $field['data'] 		: array();

		$_help					= array();
		$_help['src']			= is_array( $help ) && isset( $help['src'] )	? $help['src'] : NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']			= is_array( $help ) && isset( $help['class'] )	? $help['class'] : 'help';
		$_help['rel']			= is_array( $help ) && isset( $help['rel'] )	? $help['rel'] : 'tipsy-left';
		$_help['title']			= is_array( $help ) && isset( $help['title'] )	? $help['title'] : NULL;
		$_help['title']			= is_string( $help ) ? $help : $_help['title'];

		$_error					= form_error( $_field['key'] ) || $_field['error'] ? 'error' : '';
		$_readonly				= $_field['readonly'] ? 'readonly="readonly"' : '';
		$_readonly_cls			= $_field['readonly'] ? 'readonly' : '';

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
		$_force_secure = page_is_secure();

		if ( $_field['bucket'] ) :

			$_nonce		= time();
			$_bucket	= urlencode( get_instance()->encrypt->encode( $_field['bucket'] . '|' . $_nonce , APP_PRIVATE_KEY ) );
			$_hash		= md5( $_field['bucket'] . '|' . $_nonce . '|' . APP_PRIVATE_KEY );

			$_url		= site_url( 'cdn/manager/browse', $_force_secure ) . '?callback=callback_' . $_id . '&bucket=' . $_bucket . '&hash=' . $_hash;

		else :

			$_url		= site_url( 'cdn/manager/browse', $_force_secure );

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

		//	Quick script to instantiate the field, not indented due to heredoc syntax
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
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-left';
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
		$_force_secure = page_is_secure();

		if ( $_field['bucket'] ) :

			$_nonce		= time();
			$_bucket	= urlencode( get_instance()->encrypt->encode( $_field['bucket'] . '|' . $_nonce , APP_PRIVATE_KEY ) );
			$_hash		= md5( $_field['bucket'] . '|' . $_nonce . '|' . APP_PRIVATE_KEY );

			$_url		= site_url( 'cdn/manager/browse', $_force_secure ) . '?callback=callback_' . $_id . '&bucket=' . $_bucket . '&hash=' . $_hash;

		else :

			$_url		= site_url( 'cdn/manager/browse', $_force_secure );

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

		//	Quick script to instantiate the field, not indented due to heredoc syntax
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
		$_field_id			= isset( $field['id'] )				? $field['id']			: NULL;
		$_field_type		= isset( $field['type'] )			? $field['type']		: 'text';
		$_field_oddeven		= isset( $field['oddeven'] )		? $field['oddeven']		: NULL;
		$_field_key			= isset( $field['key'] )			? $field['key']			: NULL;
		$_field_label		= isset( $field['label'] )			? $field['label']		: NULL;
		$_field_default		= isset( $field['default'] )		? $field['default']		: NULL;
		$_field_sub_label	= isset( $field['sub_label'] )		? $field['sub_label']	: NULL;
		$_field_required	= isset( $field['required'] )		? $field['required']	: FALSE;
		$_field_placeholder	= isset( $field['placeholder'] )	? $field['placeholder']	: NULL;
		$_field_readonly	= isset( $field['readonly'] )		? $field['readonly']	: FALSE;
		$_field_error		= isset( $field['error'] )			? $field['error']		: FALSE;
		$_field_bucket		= isset( $field['bucket'] )			? $field['bucket']		: FALSE;
		$_field_class		= isset( $field['class'] )			? $field['class']		: FALSE;

		$_help				= array();
		$_help['src']		= is_array( $help ) && isset( $help['src'] )	? $help['src']		: NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']		= is_array( $help ) && isset( $help['class'] )	? $help['class']	: 'help';
		$_help['rel']		= is_array( $help ) && isset( $help['rel'] )	? $help['rel']		: 'tipsy-left';
		$_help['title']		= is_array( $help ) && isset( $help['title'] )	? $help['title']	: NULL;
		$_help['title']		= is_string( $help ) ? $help : $_help['title'];

		$_error				= form_error( $_field_key ) || $_field_error ? 'error' : '';
		$_error_class		= $_error ? 'error' : '';
		$_readonly			= $_field_readonly ? 'readonly="readonly"' : '';
		$_readonly_cls		= $_field_readonly ? 'readonly' : '';

		// --------------------------------------------------------------------------

		//	Generate a unique ID for this field
		$_id = 'field_multiimage_' . md5( microtime() );

		// --------------------------------------------------------------------------

		//	Sanitize the key
		$_field_key .= substr( $_field_key, -2 ) != '[]' ? '[]' : '';

		// --------------------------------------------------------------------------

		//	Is the label required?
		$_field_label .= $_field_required ? '*' : '';

		//	Prep sublabel
		$_field_sub_label = $_field_sub_label ? '<small>' . $_field_sub_label . '</small>' : '';

		// --------------------------------------------------------------------------

		//	Set the defaults
		$_field_default	= set_value( $_field_key, $_field_default );
		$_default_html	= '';

		//	Render any defaults
		if ( is_array( $_field_default ) ) :

			foreach( $_field_default AS $file ) :

				$_default_html .= '<li class="item">';
				$_default_html .= '<a href="#" class="delete" data-object_id="' . $file . '"></a>';
				$_default_html .= img( cdn_thumb( $file, 92, 92 ) );
				$_default_html .= form_hidden( $_field_key, $file );
				$_default_html .= '</li>';

			endforeach;

		endif;

		// --------------------------------------------------------------------------

		//	Error
		if ( $_error && $_field_error ) :

			$_error = '<span class="error">' . $_field_error . '</span>';

		elseif( $_error ) :

			$_error = form_error( $_field_key, '<span class="error">', '</span>' );

		endif;

		// --------------------------------------------------------------------------

		//	Tip
		$_tip				= $_help['title'] ? img( $_help ) : '';
		$_filelist_class	= $_help['title'] ? 'has-tip ' : '';

		// --------------------------------------------------------------------------

		//	Quick script to instantiate the field, not indented due to heredoc syntax
		get_instance()->load->library( 'cdn' );

		$_movie_url		= NAILS_ASSETS_URL . 'swf/jquery.uploadify/uploadify.swf';
		$_upload_url	= site_url( 'api/cdnapi/object_create/script.php', page_is_secure() );
		$_upload_token	= get_instance()->cdn->generate_api_upload_token();
		$_bucket		= $_field_bucket;

$_out = <<<EOT

	<div class="field multiimage $_error_class $_field_oddeven $_readonly_cls $_field_type" id="$_id">
		<label>
			<span class="label">
				$_field_label
				$_field_sub_label
			</span>
			<span class="input">
				<p class="system-alert error no-close" id="$_id-uploadify-not-available">
					<strong>Configuration Error.</strong> Uploadify is not available.
				</p>
				<div id="$_id-uploadify-available" style="display:none;">
					<ul id="$_id-filelist" class="filelist ">
						$_default_html
						<li class="empty">No Images, add some now.</li>
					</ul>
					<button id="$_id-uploadify">Choose Images</button>
				</div>
				$_error
			<span>
		</label>
	</div>

	<script type="text/template" id="$_id-template-uploadify">
		<li class="item uploadify-queue-item" id="$_id-\${fileID}" data-instance_id="\${instanceID}" data-file_id="\${fileID}">
			<a href="#" data-instance_id="\${instanceID}" data-file_id="\${fileID}" class="remove"></a>
			<div class="progress" style="height:0%"></div>
			<div class="data data-cancel">CANCELLED</div>
		</li>
	</script>
	<script type="text/template" id="$_id-template-item">
		<li class="item crunching">
			<div class="crunching"></div>
			<input type="hidden" name="$_field_key" />
		</li>
	</script>
	<div id="$_id-dialog-confirm-delete" title="Confirm Delete" style="display:none;">
		<p>
			<span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 0 0;"></span>
			This item will be removed from the interface and cannot be recovered.
			<strong>Are you sure?</strong>
		</p>
	</div>

	<script type="text/javascript">

	if ( typeof( $.fn.uploadify ) === 'function' )
	{
		$( '#$_id-uploadify-not-available' ).hide();
		$( '#$_id-uploadify-available' ).show();

		// --------------------------------------------------------------------------

		$( '#$_id-uploadify' ).uploadify(
		{
			'debug': false,
			'auto': true,
			'swf': '$_movie_url',
			'uploader': '$_upload_url',
			'fileObjName': 'upload',
			'fileTypeExts': '*.gif; *.jpg; *.jpeg; *.png',
			'queueID': '$_id-filelist',
			'formData':
			{
				'token': '$_upload_token',
				'bucket': '$_bucket',
				'return': 'URL|THUMB|92x92'
			},
			'itemTemplate': $('#$_id-template-uploadify').html(),
			'onSelect': function()
			{
				if ( $( '#$_id-filelist li' ).length )
				{
					$('#$_id-filelist').removeClass( 'empty' );
				}
			},
			'onUploadStart': function()
			{
				window.onbeforeunload = function()
				{
					return 'Uploads are in progress. Leaving this page will cause them to stop.';
				};

				//	Disable tabs - SWFUpload aborts uploads if it is hidden.
				$('ul.tabs li a').addClass('disabled');
			},
			'onQueueComplete': function()
			{
				window.onbeforeunload = null;
				$('ul.tabs li a').removeClass('disabled');
			},
			'onUploadProgress': function(file, bytesUploaded, bytesTotal)
			{
				var _percent = bytesUploaded / bytesTotal * 100;
				$('#$_id-' + file.id + ' .progress').css('height', _percent + '%');
			},
			'onUploadSuccess': function(file, data)
			{
				var _data = JSON.parse(data);

				// --------------------------------------------------------------------------

				var _html = $.trim($('#$_id-template-item').html());
				var _item = $($.parseHTML(_html));

				_item.attr('id', '$_id-' + file.id + '-complete');
				$('#$_id-' + file.id).replaceWith(_item);

				// --------------------------------------------------------------------------

				var _target = $('#$_id-' + file.id + '-complete');

				if (!_target.length)
				{
					_html = $.trim($('#$_id-template-item').html());
					_item = $($.parseHTML(_html));

					_item.attr('id', '$_id-' + file.id + '-complete');
					$('#' + file.id).replaceWith(_item);

					_target = $('#$_id-' + file.id + '-complete');
				}

				// --------------------------------------------------------------------------

				//	Switch the response code
				if (_data.status === 200)
				{
					//	Insert the image
					var _img = $('<img>').attr('src', _data.object_url[0]).on('load', function() {
						_target.removeClass('crunching');
					});
					var _del = $('<a>').attr({
						'href': '#',
						'class': 'delete',
						'data-object_id': _data.object_id
					});

					_target.append(_img).append(_del).find('input').val(_data.object_id);

				}
				else
				{
					//	An error occurred
					var _filename = $('<p>').addClass('filename').text(file.name);
					var _message = $('<p>').addClass('message').text(_data.error);

					_target.addClass('error').append(_filename).append(_message).removeClass('crunching');
				}
			},
			'onUploadError': function(file, errorCode, errorMsg, errorString)
			{
				var _target = $('#$_id-' + file.id + '-complete');

				if (!_target.length)
				{
					var _html = $.trim($('#$_id-template-item').html());
					var _item = $($.parseHTML(_html));

					_item.attr('id', '$_id-' + file.id + '-complete');
					$('#$_id-' + file.id).replaceWith(_item);

					_target = $('#$_id-' + file.id + '-complete');
				}

				var _filename = $('<p>').addClass('filename').text(file.name);
				var _message = $('<p>').addClass('message').text(errorString);

				_target.addClass('error').append(_filename).append(_message).removeClass('crunching');
			}

		});

		if ( typeof( $.fn.sortable ) === 'function' )
		{
			$('#$_id-filelist').disableSelection().sortable({
				placeholder: 'item placeholder',
				items: "li.item"
			});
		}

		//	Remove an item from the queue
		$(document).on('click', '#$_id-filelist .item .remove', function()
		{
			var _instance_id = $(this).data('instance_id');
			var _file_id = $(this).data('file_id');

			$('#$_id-' + _instance_id).uploadify('cancel', _file_id);
			$('#$_id-' + _file_id + ' .data-cancel').text('Cancelled').show();
			$('#$_id-' + _file_id).addClass('cancelled');

			if ($('#$_id-filelist li.item:not(.cancelled)').length === 0)
			{
				$('#$_id-filelist').addClass('empty');
				$('#$_id-filelist li.empty').css('opacity', 0).delay(1000).animate({
					opacity: 1
				}, 250);
			}

			return false;

		});

		//	Deletes an uploaded image
		$(document).on('click', '#$_id-filelist .item .delete', function()
		{
			var _object = this;

			$('#$_id-dialog-confirm-delete').dialog(
			{
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					"Delete Image": function()
					{
						var _object_id = $(_object).data('object_id');

						//	Send off the delete request
						var _call = {
							'controller'	: 'cdnapi',
							'method'		: 'object_delete',
							'action'		: 'POST',
							'data'			:
							{
								'object_id': _object_id
							}
						};
						window.NAILS.API.call(_call);

						// --------------------------------------------------------------------------

						$(_object).closest('li.item').addClass('deleted').fadeOut('slow', function()
						{
							$(_object).remove();
						});

						// --------------------------------------------------------------------------

						//	Show the empty screens
						if ($('#$_id-filelist li.item:not(.deleted)').length === 0)
						{
							$('#$_id-filelist').addClass('empty');
						}

						// --------------------------------------------------------------------------

						//	Close dialog
						$(this).dialog("close");
					},
					Cancel: function()
					{
						$(this).dialog("close");
					}
				}
			});

			return false;
		});
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
		$_field						= array();
		$_field['id']				= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['type']				= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']			= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']				= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']			= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']			= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']		= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']			= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']		= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['class']			= isset( $field['class'] ) ? $field['class'] : FALSE;
		$_field['readonly']			= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['data']				= isset( $field['data'] ) ? $field['data'] : array();
		$_field['disabled_options']	= isset( $field['disabled_options'] ) ? $field['disabled_options'] : array();
		$_field['info']				= isset( $field['info'] ) ? $field['info'] : array();

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-left';
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
		$_placeholder = NULL !== $_field['placeholder'] ? 'data-placeholder="' . $_field['placeholder'] . '"' : '';
		$_out .= '<select name="' . $_field['key'] . '" class="' . $_field['class'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';


		foreach ( $options AS $value => $label ) :

			//	Selected?
			$_checked = $value == $_selected ? ' selected="selected"' : '';

			//	Disabled?
			$_disabled = array_search( $value, $_field['disabled_options'] ) !== FALSE ? ' disabled="disabled"' : '';

			$_out .= '<option value="' . $value . '"' . $_checked . $_disabled . '>' . $label . '</option>';

		endforeach;
		$_out .= '</select>';

		// --------------------------------------------------------------------------

		if ( $_readonly ) :

			$_out .= form_hidden( $_field['key'], $_field['default'] );

		endif;

		//	Tip
		$_out .= $_help['title'] ? img( $_help ) : '';

		//	Error
		$_out .= form_error( $_field['key'], '<span class="error">', '</span>' );

		//	Info
		$_out .= $_field['info'] ? '<small class="info">' . $_field['info'] . '</small>' : '';

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
		$_field						= array();
		$_field['id']				= isset( $field['id'] ) ? $field['id'] : NULL;
		$_field['type']				= isset( $field['type'] ) ? $field['type'] : 'text';
		$_field['oddeven']			= isset( $field['oddeven'] ) ? $field['oddeven'] : NULL;
		$_field['key']				= isset( $field['key'] ) ? $field['key'] : NULL;
		$_field['label']			= isset( $field['label'] ) ? $field['label'] : NULL;
		$_field['default']			= isset( $field['default'] ) ? $field['default'] : NULL;
		$_field['sub_label']		= isset( $field['sub_label'] ) ? $field['sub_label'] : NULL;
		$_field['required']			= isset( $field['required'] ) ? $field['required'] : FALSE;
		$_field['placeholder']		= isset( $field['placeholder'] ) ? $field['placeholder'] : NULL;
		$_field['class']			= isset( $field['class'] ) ? $field['class'] : FALSE;
		$_field['readonly']			= isset( $field['readonly'] ) ? $field['readonly'] : FALSE;
		$_field['data']				= isset( $field['data'] ) ? $field['data'] : array();
		$_field['disabled_options']	= isset( $field['disabled_options'] ) ? $field['disabled_options'] : array();
		$_field['info']				= isset( $field['info'] ) ? $field['info'] : array();

		$_help			= array();
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-left';
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
		$_placeholder = NULL !== $_field['placeholder'] ? 'data-placeholder="' . $_field['placeholder'] . '"' : '';
		$_out .= '<select name="' . $_field['key'] . '" multiple="multiple" class="' . $_field['class'] . '" ' . $_field['id'] . ' ' . $_readonly . $_placeholder . $_data . '>';

		foreach ( $options AS $value => $label ) :

			//	Selected?
			if ( is_array( $_selected ) ) :
				if ( in_array( $value, $_selected ) ) :
					$_checked = ' selected="selected"';
				else :
					$_checked = '';
				endif;
			else:
				$_checked = $value == $_selected ? ' selected="selected"' : '';
			endif;

			//	Disabled?
			$_disabled = array_search( $value, $_field['disabled_options'] ) !== FALSE ? ' disabled="disabled"' : '';

			$_out .= '<option value="' . $value . '"' . $_checked . $_disabled . '>' . $label . '</option>';

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

		//	Info
		$_out .= $_field['info'] ? '<small class="info">' . $_field['info'] . '</small>' : '';

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
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-left';
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
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-left';
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
		$_help['src']	= is_array( $help ) && isset( $help['src'] ) ? $help['src'] : NAILS_ASSETS_URL . 'img/form/help.png';
		$_help['class']	= is_array( $help ) && isset( $help['class'] ) ? $help['class'] : 'help';
		$_help['rel']	= is_array( $help ) && isset( $help['rel'] ) ? $help['rel'] : 'tipsy-left';
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
		$_field_html = form_submit( $button_name, $button_value, $button_attributes );

		// --------------------------------------------------------------------------

$_out = <<<EOT

	<div class="field submit">
		<span class="label">&nbsp;</span>
		<span class="input">
			$_field_html
		</span>
	</div>

EOT;

		// --------------------------------------------------------------------------

		return $_out;
	}
}