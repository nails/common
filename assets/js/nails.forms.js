var NAILS_Forms;
NAILS_Forms = function()
{
	this.__construct = function()
	{
		this._init_form_field_mm();
		this._init_form_field_mm_image();
		this._init_form_field_multiimage();
	};

	// --------------------------------------------------------------------------

	this._init_form_field_mm = function()
	{
		//	Bind to the chooser, open the Fancybox
		$(document).on( 'click', '.field.mm-file a.mm-file-choose', function()
		{
			var _href,_w,_h;

			_href = $(this).attr( 'href' );
			_href += _href.indexOf( '?' ) >= 0 ? '&is_fancybox=1' : '?is_fancybox=1';

			if ( $.fancybox.isOpen && $.fancybox.opts.type !== 'iframe' )
			{
				_href += '&reopen_fancybox=' + encodeURIComponent( $.fancybox.opts.href );
				$.fancybox.close();
			}

			_w = $(this).data( 'width' ) ? $(this).data( 'width' ) : null;
			_h = $(this).data( 'height' ) ? $(this).data( 'height' ) : null;

			$.fancybox.open({
				href: _href,
				type: 'iframe',
				width: _w,
				height: _h,
				iframe: {
					preload: false // fixes issue with iframe and IE
				}
			});

			return false;
		});

		//	Bind to remover
		$(document).on( 'click', '.field.mm-file a.mm-file-remove', function()
		{
			$(this).closest( '.field.mm-file' ).find( '.mm-file-preview' ).html( '' );
			$(this).closest( '.field.mm-file' ).find( '.mm-file-value' ).val( '' );
			$(this).closest( '.field.mm-file' ).find( '.mm-file-remove' ).css( 'display', 'none' );

			return false;
		});
	};

	// --------------------------------------------------------------------------

	this._callback_form_field_mm = function( file, id, reopen, fieldid )
	{
		if ( file.length === 0 )
		{
			$( '#' + fieldid + ' .mm-file-preview' ).html( '' );
			$( '#' + fieldid + ' .mm-file-value' ).val( '' );
			$( '#' + fieldid + ' .mm-file-remove' ).css( 'display', 'none' );

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

		var _scheme = $( '#' + fieldid).data( 'scheme' );
		var _file	= file.split( '.' );

		_scheme = _scheme.replace( '{[filename]}', _file[0] );
		_scheme = _scheme.replace( '{[extension]}', '.' + _file[1] );

		$( '#' + fieldid + ' .mm-file-preview' ).html( '<a href="' + _scheme + '">Download</a>' );
		$( '#' + fieldid + ' .mm-file-value' ).val( id );
		$( '#' + fieldid + ' .mm-file-remove' ).css( 'display', 'inline-block' );

		// --------------------------------------------------------------------------

		//	Reopen facybox?
		if ( reopen.length )
		{
			$.fancybox.open({
				href:reopen
			});
		}
	};

	// --------------------------------------------------------------------------

	this._init_form_field_mm_image = function()
	{
		//	Bind to the chooser, open the Fancybox
		$(document).on( 'click', '.field.mm-image a.mm-image-choose', function()
		{
			var _href,_w,_h;

			_href = $(this).attr( 'href' );
			_href += _href.indexOf( '?' ) >= 0 ? '&is_fancybox=1' : '?is_fancybox=1';

			if ( $.fancybox.isOpen && $.fancybox.opts.type !== 'iframe' )
			{
				_href += '&reopen_fancybox=' + encodeURIComponent( $.fancybox.opts.href );
				$.fancybox.close();
			}

			_w = $(this).data( 'width' ) ? $(this).data( 'width' ) : null;
			_h = $(this).data( 'height' ) ? $(this).data( 'height' ) : null;

			$.fancybox.open({
				href: _href,
				type: 'iframe',
				width: _w,
				height: _h,
				iframe: {
					preload: false // fixes issue with iframe and IE
				}
			});

			return false;
		});

		//	Bind to remover
		$(document).on( 'click', '.field.mm-image a.mm-image-remove', function()
		{
			$(this).closest( '.field.mm-image' ).find( '.mm-image-preview' ).html( '' );
			$(this).closest( '.field.mm-image' ).find( '.mm-image-value' ).val( '' );
			$(this).closest( '.field.mm-image' ).find( '.mm-image-remove' ).css( 'display', 'none' );

			return false;
		});
	};

	// --------------------------------------------------------------------------

	this._callback_form_field_mm_image = function( file, id, reopen, fieldid )
	{
		if ( file.length === 0 )
		{
			$( '#' + fieldid + ' .mm-image-preview' ).html( '' );
			$( '#' + fieldid + ' .mm-image-value' ).val( '' );
			$( '#' + fieldid + ' .mm-image-remove' ).css( 'display', 'none' );

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

		var _scheme = $( '#' + fieldid).data( 'scheme' );
		var _file	= file.split( '.' );

		_scheme = _scheme.replace( '{[filename]}', _file[0] );
		_scheme = _scheme.replace( '{[extension]}', '.' + _file[1] );

		$( '#' + fieldid + ' .mm-image-preview' ).html( '<img src="' + _scheme + '" / >' );
		$( '#' + fieldid + ' .mm-image-value' ).val( id );
		$( '#' + fieldid + ' .mm-image-remove' ).css( 'display', 'inline-block' );

		// --------------------------------------------------------------------------

		//	Reopen facybox?
		if ( reopen.length )
		{
			$.fancybox.open({
				href:reopen
			});
		}
	};

	// --------------------------------------------------------------------------

	this._init_form_field_multiimage = function()
	{

	};

	// --------------------------------------------------------------------------

	return this.__construct();
};