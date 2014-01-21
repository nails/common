var NAILS_Admin_CMS_pages_Create_Edit;
NAILS_Admin_CMS_pages_Create_Edit = function()
{
	this.editor_id = Math.floor( Math.random() * 10000000000000001 );
	this.search_placeholder	= 'Search widget library';

	// --------------------------------------------------------------------------


	this.init = function()
	{
		this._template_chooser_init();
		this._editor_init();
		console.log(this.editor_id);
	};


	// --------------------------------------------------------------------------


	this._template_chooser_init = function()
	{
		$( 'label.template' ).on( 'click', function()
		{
			//	Set the correct label
			$( 'label.template' ).removeClass( 'selected' );
			$(this).addClass( 'selected' );

			//	Show the correct buttons
			var _template = $(this).data( 'template-slug' );
			$( 'a.launch-editor' ).hide();
			$( 'a.launch-editor.template-' + _template ).show();
		});
	};


	// --------------------------------------------------------------------------


	this._editor_init = function()
	{
		var _this = this;

		//	Generate the editor HTML and inject into DOM
		var _container		= $( '<div>').attr( 'id', this.editor_id ).addClass( 'group-cms pages widgeteditor ready' );
		var _loader			= $( '<div>' ).attr( 'id', this.editor_id + '-loader' ).addClass( 'loader' ).html( '<span class="ion-looping"></span>' );
		var _header			= $( '<ul>' ).attr( 'id', this.editor_id + '-header' ).addClass( 'header' ).html( $( '#template-header' ).html() );
		var _widgets		= $( '<ul>' ).attr( 'id', this.editor_id + '-widgets' ).addClass( 'widgets' );
		var _widgets_search	= $( '<li>' ).attr( 'id', this.editor_id + '-search' ).addClass( 'search' ).html( $( '#template-widget-search' ).html() );
		var _dropzone		= $( '<ul>' ).attr('id',  this.editor_id + '-dropzone' ).addClass( 'dropzone empty' ).html( $( '#template-dropzone-empty' ).html() );

		// --------------------------------------------------------------------------

		//	Get the pritt-stick out
		_widgets.append( _widgets_search );

		//	scaffolding
		var _data,_tpl,_html;
		var _keywords = ['test1','test2,something,bla','ssh,images,layout'];

		for ( var i = 0; i < 10; i++ )
		{
			_data	= { name: 'Group ' + i, group : 'group-' + i  };
			_tpl	= $( '#template-widget-grouping' ).html();
			_html	= Mustache.render( _tpl, _data );

			_widgets.append( _widgets.append( _html ) );

			for ( var y = 0; y < 5; y++ )
			{
				_data	= { group: 'group-' + i, name: 'Widget ' + y, keywords: _keywords[Math.floor(Math.random() * _keywords.length)]  };
				_tpl	= $( '#template-widget' ).html();
				_html	= Mustache.render( _tpl, _data );

				_widgets.append( _html );
			}
		}

		_container.append( _loader );
		_container.append( _header );
		_container.append( _widgets );
		_container.append( _dropzone );

		$( 'body' ).append( _container );

		//	TODO: Set up editor environment's HTML/templates
		//	Draggables
		//	Widget areas
		//	etc

		// --------------------------------------------------------------------------

		//	Bind to launchers
		$( 'a.launch-editor' ).on( 'click', function()
		{
			var _template	= $(this).data( 'template' );
			var _area		= $(this).data( 'area' );

			_this._editor_launch( _template, _area );

			return false;
		});
	};


	// --------------------------------------------------------------------------


	this._editor_launch = function( template, area )
	{
		console.log(template,area);
		$( '#' + this.editor_id ).removeClass( 'ready' ).show();

		var _this = this;

		// --------------------------------------------------------------------------

		//	Disable scrolling of body
		$( 'body' ).addClass( 'noscroll' );

		// --------------------------------------------------------------------------

		//	TODO: Set up the editor environment
		//	Disable any draggables
		//	Render any existing widgets (from the DB, POST data or in memory)

		// --------------------------------------------------------------------------

		//	Editor ready
		$( '#' + this.editor_id ).addClass( 'ready' );

		// --------------------------------------------------------------------------

		//	Bind keyUp event for the escape key
		$(document).on( 'keyup', function( e )
		{
			if ( e.keyCode === 27 )
			{
				_this._editor_close();
			}
		});

		//	Bind the minimiser
		$( '#' + this.editor_id + '-search a.minimiser' ).on( 'click', function()
		{
			$( '#' + _this.editor_id ).toggleClass( 'minimised' );
		});

		//	Bind the search box
		$( '#' + this.editor_id + '-search input' ).on( 'keyup', function()
		{
			var _text = $.trim( $(this).val() );

			//	TODO: consider adding fuzzy search
			//	http://glench.github.io/fuzzyset.js/
			//	or
			//	http://listjs.com/examples/fuzzy-search

			if ( _text.length > 0 )
			{
				$( '#' + _this.editor_id + '-widgets li.widget' ).each( function()
				{
					var _keywords	= $(this).data( 'keywords' ).split( ',' );
					var _regex		= new RegExp( _text, 'gi' );

					if ( _regex.test( _keywords ) )
					{
						//	Search hit
						$(this).addClass( 'search-hit' );
						$(this).removeClass( 'search-miss' );
					}
					else
					{
						//	Search miss
						$(this).addClass( 'search-miss' );
						$(this).removeClass( 'search-hit' );
					}
				});
			}
			else
			{
				//	Remove search hit/miss classes
				$( '#' + _this.editor_id + '-widgets li.widget' ).removeClass( 'search-miss search-hit' );
			}

		});

		//	Bind group containers
		$( '#' + this.editor_id + '-widgets li.grouping' ).on( 'click', function()
		{
			$(this).toggleClass( 'open' );

			var _grouping = $(this).data( 'group' );
			$( '#' + _this.editor_id + '-widgets li.widget.' + _grouping ).toggleClass( 'hidden' );
		});

		//	Bind the action buttons
		$( '#' + this.editor_id + '-header ul.rhs a' ).on( 'click', function()
		{
			var _action = $(this).data( 'action' );

			switch( _action )
			{
				case 'close' :

					_this._editor_close();

				break;
			}

			return false;
		});
	};


	// --------------------------------------------------------------------------


	this._editor_close = function()
	{
		$( '#' + this.editor_id ).hide();

		//	Re-enable scrolling
		$( 'body' ).removeClass( 'noscroll' );

		//	Remove binds
		$(document).off( 'keyup' );
		$( '#' + this.editor_id + '-search a.minimiser' ).off( 'click' );
		$( '#' + this.editor_id + '-search input' ).off( 'keyup' );
		$( '#' + this.editor_id + '-widgets li.grouping' ).off( 'click' );
		$( '#' + this.editor_id + '-header ul.rhs a' ).off( 'click' );
	};
};