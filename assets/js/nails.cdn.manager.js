var NAILS_CDN_Manager;
NAILS_CDN_Manager =  function() {

	this.handler			= '';
	this.callback			= '';
	this.url_schemes		= {};
	this.is_fancybox		= '';
	this.reopen_fancybox	= '';

	// --------------------------------------------------------------------------

	this.init = function( handler, callback, url_schemes, is_fancybox, reopen_fancybox )
	{
		this.handler			= handler;
		this.callback			= callback;
		this.url_schemes		= url_schemes;
		this.is_fancybox		= is_fancybox;
		this.reopen_fancybox	= reopen_fancybox;

		this._init_submit();
		this._init_alerts();
		this._init_upload();
		this._init_delete();
		this._init_insert();
		this._init_sidebar();
		this._init_thumbs();
		this._init_search();

		// --------------------------------------------------------------------------

		//	Is the callback callable?
		var _callable = false;
		if ( this.handler === 'ckeditor' )
		{
			//	Assume callable - CKEditor should handle this.
			_callable = true;
		}
		else
		{
			if ( this.is_fancybox )
			{
				_callable = typeof( parent[this.callback] ) === 'function' ? true : false;
			}
			else
			{
				if ( window.opener )
				{
					_callable = typeof( window.opener[this.callback] ) === 'function' ? true : false;
				}
				else
				{
					_callable = false;
				}
			}
		}

		if ( ! _callable )
		{
			$( 'a.insert' ).remove();
		}
	};


	// --------------------------------------------------------------------------


	this._init_submit = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( 'form' ).on( 'submit', function() { _this._show_mask(); } );
	};


	// --------------------------------------------------------------------------


	this._init_alerts = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( '.system-alert .awesome' ).on( 'click', function() { _this._hide_alert(); } );

		// --------------------------------------------------------------------------

		//	If alert is open and enter is pressed then trigger the first button
		$(document).on( 'keydown', function(e)
		{
			//	Enter key
			if ( e.keyCode === 13 && $( '#alert:visible' ).length > 0)
			{
				$( '#alert .awesome.ok' ).first().click();
			}

			//	Escape
			if ( e.keyCode === 27 && $( '#alert:visible' ).length > 0)
			{
				$( '#alert .awesome.cancel' ).first().click();
			}
		});
	};


	// --------------------------------------------------------------------------


	this._init_upload = function()
	{

	};


	// --------------------------------------------------------------------------


	this._init_delete = function()
	{
		$( 'a.delete' ).on( 'click', function()
		{
			var _a = $(this);

			$('<div>').html( '<p>Any resources which are using this object will have the reference removed.</p><p>Continue?</p>' ).dialog({
				title: 'Are you sure?',
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					"Delete": function()
					{
						window.location.href = _a.attr( 'href' );
					},
					Cancel: function()
					{
						$(this).dialog("close");
					}
				}
			});

			return false;
		});
	};


	// --------------------------------------------------------------------------


	this._init_insert = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/

		$( 'a.insert' ).on( 'click', function()
		{
			var _bucket	= $(this).attr( 'data-bucket' );
			var _file	= $(this).attr( 'data-file' );
			var _id		= $(this).attr( 'data-id' );

			if ( _this.handler === 'ckeditor' )
			{
				_this._insert_ckeditor( _bucket, _file, _id );
			}
			else
			{
				_this._insert_native( _bucket, _file, _id );
			}

			//	Close window
			if ( _this.is_fancybox )
			{
				parent.$.fancybox.close();
			}
			else
			{
				window.close();
			}

			// --------------------------------------------------------------------------

			return false;
		});
	};


	// --------------------------------------------------------------------------


	this._insert_ckeditor = function( bucket, file, id )
	{
		//	TODO Render a modal asking for customisations to the URL

		//	Choose the scheme to use (TODO, make this dynamic)
		var _scheme = this.url_schemes.serve;

		//	Break into filename and extensions
		alert(file);

		//	Define the data object
		var _data = {
			bucket		: bucket,
			filename	: _filename,
			extension	: _extension,
			id			: id,
			width		: 0,		//	TODO
			height		: 0,		//	TODO
			sex			: '',		//	TODO
			border		: 0			//	TODO
		};

		//	Apply the scheme
		var _url = Mustache.render( _scheme, _data );

		//	Call back to the CKEditor instance
		window.opener.CKEDITOR.tools.callFunction( this.callback, _url );
	};


	// --------------------------------------------------------------------------


	this._insert_native = function( bucket, file, id )
	{
		if ( this.is_fancybox )
		{
			parent[this.callback].call( bucket, file, id, this.reopen_fancybox );
		}
		else
		{
			window.opener[this.callback].call( bucket, file, id, this.reopen_fancybox );
		}
	};


	// --------------------------------------------------------------------------


	this._init_sidebar = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/

		$( 'li.tag.droppable' ).droppable({
			accept: '.file',
			hoverClass: 'can-drop',
			tolerance: 'pointer',
			drop: function(e,ui)
			{
				var _object_id	= $(ui.draggable).data( 'id' );
				var _tag_id		= $(this).data( 'id' );

				//	Send off API request
				var _droppable = $(this);
				var _draggable = $(ui.draggable);

				_api.call(
				{
					controller	: 'cdnapi',
					method		: 'add_object_tag',
					action		: 'GET',
					data		: { object_id:_object_id,tag_id:_tag_id},
					success		: function( data ) { _this._add_tag_ok( _droppable, _draggable, data ); },
					error		: function( data ) { _this._add_tag_fail( _droppable, _draggable, data ); }
				});

				//	Set the counter to updating
				$(this).find( '.count' ).addClass( 'saving' );

			}
		});


		// --------------------------------------------------------------------------


		$( 'div.remove-object-tag' ).droppable({
			accept: '.file',
			hoverClass: 'can-drop',
			tolerance: 'pointer',
			drop: function(e,ui)
			{
				var _object_id	= $(ui.draggable).data( 'id' );
				var _tag_id		= $(this).data( 'id' );

				//	Send off API request
				var _droppable = $(this);
				var _draggable = $(ui.draggable);

				_api.call(
				{
					controller	: 'cdnapi',
					method		: 'delete_object_tag',
					action		: 'GET',
					data		: { object_id:_object_id,tag_id:_tag_id},
					success		: function( data ) { _this._remove_tag_ok( _droppable, _draggable, data ); },
					error		: function( data ) { _this._remove_tag_fail( _droppable, _draggable, data ); }
				});

				//	Set the counter to updating
				$(this).find( '.count' ).addClass( 'saving' );
			}
		});
	};


	// --------------------------------------------------------------------------


	this._add_tag_ok = function( dropabble, draggable, data )
	{
		dropabble.find( '.count' ).text( data.new_total ).removeClass( 'saving' );
	};


	// --------------------------------------------------------------------------


	this._add_tag_fail = function( dropabble, draggable, data )
	{
		var _data = JSON.parse( data.responseText );
		alert( _data.error );
		dropabble.find( '.count' ).removeClass( 'saving' );
	};


	// --------------------------------------------------------------------------


	this._remove_tag_ok = function( dropabble, draggable, data )
	{
		draggable.remove();
		dropabble.removeClass( 'saving' );
		$( 'li.tag.selected .count' ).text( data.new_total );
	};


	// --------------------------------------------------------------------------


	this._remove_tag_fail = function( dropabble, draggable, data )
	{
		var _data = JSON.parse( data.responseText );
		alert( _data.error );
		dropabble.removeClass( 'saving' );
	};


	// --------------------------------------------------------------------------


	this._init_thumbs = function()
	{
		$( '.file' ).draggable({
			helper: "clone",
			start: function(e, ui)
			{
				$(ui.helper).addClass( 'clone' );
			}
		});
	};


	// --------------------------------------------------------------------------


	this._init_search = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( '#search-text' ).on( 'keyup', function() { _this._do_search( $(this).val() ); return false; } );
	};


	// --------------------------------------------------------------------------


	this._do_search = function( term )
	{
		$( 'li.file' ).each(function()
		{
			var regex = new RegExp( term, 'gi' );

			if ( regex.test( $(this).attr( 'data-title' ) ) )
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
	};


	// --------------------------------------------------------------------------


	this._show_mask = function()
	{
		$( '#mask' ).show();
	};


	// --------------------------------------------------------------------------


	this._hide_mask = function()
	{
		$( '#mask' ).hide();
	};


	// --------------------------------------------------------------------------


	this._show_alert = function()
	{
		$( '#alert' ).show();
	};


	// --------------------------------------------------------------------------


	this._hide_alert = function()
	{
		$( '#alert' ).hide();
	};
};