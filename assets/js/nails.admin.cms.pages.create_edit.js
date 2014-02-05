var NAILS_Admin_CMS_pages_Create_Edit;
NAILS_Admin_CMS_pages_Create_Edit = function()
{
	this.editor_id			= Math.floor( Math.random() * 10000000000000001 );
	this.search_placeholder	= 'Search widget library';
	this.templates			= [];
	this.widgets			= [];
	this._dragging_widget	= false;

	// --------------------------------------------------------------------------


	this.init = function( templates, widgets )
	{
		this.templates	= templates;
		this.widgets	= widgets;

		// --------------------------------------------------------------------------

		this._template_chooser_init();
		this._editor_init();
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
		var _header			= $( '<ul>' ).attr( 'id', this.editor_id + '-header' ).addClass( 'header' );
		var _widgets		= $( '<ul>' ).attr( 'id', this.editor_id + '-widgets' ).addClass( 'widgets' ).disableSelection();
		var _widgets_search	= $( '<li>' ).attr( 'id', this.editor_id + '-search' ).addClass( 'search' ).html( $( '#template-widget-search' ).html() );
		var _dropzone		= $( '<ul>' ).attr('id',  this.editor_id + '-dropzone' ).addClass( 'dropzone empty' ).html( $( '#template-dropzone-empty' ).html() );

		// --------------------------------------------------------------------------

		//	Get the pritt-stick out
		_widgets.append( _widgets_search );

		//	Build the widgets HTML
		var _group_counter,_tpl_group,_tpl_widget,_data,_html;

		_group_counter	= 0;
		_tpl_group		=  $( '#template-widget-grouping' ).html();
		_tpl_widget		=  $( '#template-widget' ).html();

		for ( var _key in this.widgets )
		{
			//	Build the grouping HTML
			_data	= {
				name: this.widgets[_key].label,
				group : 'group-' + _group_counter
			};

			_html	= Mustache.render( _tpl_group, _data );

			_widgets.append( _html );

			//	Build this grouping's widgets
			for ( var _key2 in this.widgets[_key].widgets )
			{
				_data	= {
							group: 'group-' + _group_counter,
							name: this.widgets[_key].widgets[_key2].label,
							description: this.widgets[_key].widgets[_key2].description,
							keywords: this.widgets[_key].widgets[_key2].keywords,
							slug:  this.widgets[_key].widgets[_key2].slug
						};

				_html	= Mustache.render( _tpl_widget, _data );

				_widgets.append( _html );
			}

			_group_counter++;
		}

		_container.append( _loader );
		_container.append( _header );
		_container.append( _dropzone );
		_container.append( _widgets );

		$( 'body' ).append( _container );

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
		$( '#' + this.editor_id ).removeClass( 'ready' ).show();

		// --------------------------------------------------------------------------

		//	Disable scrolling of body
		$( 'body' ).addClass( 'noscroll' );

		// --------------------------------------------------------------------------

		//	Load any existing widgets
		this._load_widgets_for_area( template, area );

		// --------------------------------------------------------------------------

		//	Set up the editor environment
		this._editor_construct( template, area );

		// --------------------------------------------------------------------------

		//	Editor ready
		$( '#' + this.editor_id ).addClass( 'ready' );
	};


	// --------------------------------------------------------------------------


	this._editor_construct = function( template, area )
	{
		var _this = this;

		// --------------------------------------------------------------------------

		//	Init the header
		var _template,_tpl_header;
		_template				= this._get_template( template );
		_template.active_area	= _template.widget_areas[area].title;

		_tpl_header	= $( '#template-header' ).html();
		_tpl_header	= Mustache.render( _tpl_header, _template );

		$( '#' + this.editor_id + '-header' ).html( _tpl_header );

		// --------------------------------------------------------------------------

		//	Initialise sortables
		var _tpl_widget = $( '#template-dropzone-widget' ).html();
		$( '#' + this.editor_id + '-dropzone' ).sortable(
		{
			placeholder: 'placeholder',
			handle: '.sorter',
			start: function(e,ui)
			{
				console.log('start',e,ui);
				ui.placeholder.height( ui.helper.height() );
			},
			update: function(e,ui)
			{
				console.log('update',e,ui);
				if ( ! ui.item.hasClass( 'processed' ) )
				{
					//	Define vars
					var _slug,_widget,_data,_html,_item;

					//	What type of widget are we dealing with? Get more info.
					_slug	= $(ui.item).data( 'slug' );
					_widget	= _this._get_widget( _slug );

					_data = {
						id			: 'widget-editor-' + Math.floor( Math.random() * 10000000000000001 ),
						slug		: _slug,
						label		: _widget.label,
						description	: _widget.description
					};

					_html = Mustache.render( _tpl_widget, _data );

					_item = $( '<li>' );
					_item.addClass( 'processed dropzone-widget ' + _data.slug );
					_item.attr( 'id', _data.id );
					_item.data( 'id', _data.id );
					_item.data( 'slug', _data.slug );
					_item.html( _html );

					// --------------------------------------------------------------------------



					// --------------------------------------------------------------------------

					//	Place it into the DOM
					ui.item.replaceWith( _item );

					// --------------------------------------------------------------------------

					$( '#' + _this.editor_id + '-dropzone' ).removeClass( 'empty' );
					$( '#' + _this.editor_id + '-dropzone li.empty' ).remove();

					//	Call the server asking for the widget's editor view
					var _call =
					{
						'controller'	: 'cms/pages',
						'method'		: 'widget/get_editor',
						'data'			: { widget: _widget.slug, template: _template.slug, id: _data.id },
						'success'		: function( data )
						{
							$( _item ).find( '.editor' ).html( data.HTML );
						},
						'error'			: function( data )
						{
							var _data = JSON.parse( data.responseText );

							$( _item ).addClass( 'error' ).find( '.editor' ).html( '<p class="system-alert error no-close"><strong>Error:</strong> ' + _data.error + '</p>' );

						},
					};

					window.NAILS.API.call(_call);
				}
			},
			over: function(e,ui)
			{
				console.log('over',e,ui);
				$( '#' + _this.editor_id + '-dropzone' ).removeClass( 'empty' );
				$( '#' + _this.editor_id + '-dropzone li.empty' ).remove();
			},
			out: function(e,ui)
			{
				console.log('out',e,ui);
				var _counter = 0;
				_counter = _counter + $( '#' + _this.editor_id + '-dropzone li.dropzone-widget' ).length;
				_counter = _counter + $( '#' + _this.editor_id + '-dropzone li.widget' ).length;

				if ( _counter <= 1 )
				{
					var _tpl_empty = $('#template-dropzone-empty').html();
					$( '#' + _this.editor_id + '-dropzone' ).addClass( 'empty' ).append( _tpl_empty );
				}
			},
			sort: function(e,ui)
			{
				console.log('sort',e,ui);
			},
			activate: function(e,ui)
			{
				console.log('activate',e,ui);
			},
			beforeStop: function(e,ui)
			{
				console.log('beforeStop',e,ui);
			},
			change: function(e,ui)
			{
				console.log('change',e,ui);
			},
			create: function(e,ui)
			{
				console.log('create',e,ui);
			},
			deactivate: function(e,ui)
			{
				console.log('deactivate',e,ui);
			},
			stop: function(e,ui)
			{
				console.log('stop',e,ui);
				if ( $( '#' + _this.editor_id + '-dropzone li.dropzone-widget' ) <= 0 )
				{
					var _tpl_empty = $('#template-dropzone-empty').html();
					$( '#' + _this.editor_id + '-dropzone' ).addClass( 'empty' ).append( _tpl_empty );
				}
				else
				{
					$( '#' + _this.editor_id + '-dropzone' ).removeClass( 'empty' );
					$( '#' + _this.editor_id + '-dropzone li.empty' ).remove();
				}
			},
			remove: function(e,ui)
			{
				console.log('remove',e,ui);
			},
			receive: function(e,ui)
			{
				console.log('receive',e,ui);
			}
		});

		// --------------------------------------------------------------------------

		//	Disable widgets which don't apply to this template and/orarea
		for( var _key in this.widgets )
		{
			for( var _key2 in this.widgets[_key].widgets )
			{
				//	Restricted to: Templates
				if ( this.widgets[_key].widgets[_key2].restrict_to_template.length )
				{
					if ( this._array_search( template, this.widgets[_key].widgets[_key2].restrict_to_template ) === false )
					{
						$( '#' + this.editor_id + '-widgets li.widget.' + this.widgets[_key].widgets[_key2].slug ).addClass( 'disabled' );
					}
				}

				//	Restricted to: Areas
				if ( this.widgets[_key].widgets[_key2].restrict_to_area.length )
				{
					if ( this._array_search( area, this.widgets[_key].widgets[_key2].restrict_to_area ) === false )
					{
						$( '#' + this.editor_id + '-widgets li.widget.' + this.widgets[_key].widgets[_key2].slug ).addClass( 'disabled' );
					}
				}

				//	Restricted from: Templates
				if ( this.widgets[_key].widgets[_key2].restrict_from_template.length )
				{
					if ( this._array_search( template, this.widgets[_key].widgets[_key2].restrict_from_template ) !== false )
					{
						$( '#' + this.editor_id + '-widgets li.widget.' + this.widgets[_key].widgets[_key2].slug ).addClass( 'disabled' );
					}
				}

				//	Restricted from: Areas
				if ( this.widgets[_key].widgets[_key2].restrict_from_area.length )
				{
					if ( this._array_search( area, this.widgets[_key].widgets[_key2].restrict_from_area ) !== false )
					{
						$( '#' + this.editor_id + '-widgets li.widget.' + this.widgets[_key].widgets[_key2].slug ).addClass( 'disabled' );
					}
				}
			}
		}

		//	Initialise draggables
		$( '#' + this.editor_id + '-widgets li.widget:not(.disabled)' ).draggable(
		{
			helper : function(e)
			{
				var _src = $(e.currentTarget);
				return $( '<div>' ).addClass( 'dragging-widget' ).text( _src.data( 'title' ) );
			},
			appendTo: '#' + this.editor_id,
			zIndex:3,
			connectToSortable: '#' + this.editor_id + '-dropzone',
			start: function()
			{
				_this._dragging_widget = true;
			},
			stop: function()
			{
				_this._dragging_widget = false;
			}
		});


		// --------------------------------------------------------------------------

		//	Bind keyUp event for the escape key
		$(document).on( 'keyup', function( e )
		{
			if ( ! _this._dragging_widget && e.keyCode === 27 )
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


	this._editor_destruct = function()
	{
		//	Save widget data/state
		console.log('TODO' );

		// --------------------------------------------------------------------------

		//	Re-enable scrolling
		$( 'body' ).removeClass( 'noscroll' );

		// --------------------------------------------------------------------------

		//	Remove draggables
		$( '#' + this.editor_id + '-widgets li.widget:not(.disabled)' ).draggable( 'destroy' );

		//	Remove sortables
		$( '#' + this.editor_id + '-widgets li.dropzone' ).sortable( 'destroy' );

		//	Enable all widgets
		$( '#' + this.editor_id + '-widgets li.widget.disabled' ).removeClass( 'disabled' );

		// --------------------------------------------------------------------------

		//	Remove binds
		$(document).off( 'keyup' );
		$( '#' + this.editor_id + '-search a.minimiser' ).off( 'click' );
		$( '#' + this.editor_id + '-search input' ).off( 'keyup' );
		$( '#' + this.editor_id + '-widgets li.grouping' ).off( 'click' );
		$( '#' + this.editor_id + '-header ul.rhs a' ).off( 'click' );
	};


	// --------------------------------------------------------------------------


	this._load_widgets_for_area = function( template, area )
	{
		//	Clear the dropzone and make empty
		var _tpl_empty = $( '#template-dropzone-empty' ).html();
		$( '#' + this.editor_id + '-dropzone' ).addClass( 'empty' ).html( _tpl_empty );

		// --------------------------------------------------------------------------

		//	Load widgets
		console.log( 'TODO', template, area );
	};


	// --------------------------------------------------------------------------


	this._editor_close = function()
	{
		$( '#' + this.editor_id ).hide();
		this._editor_destruct();

	};


	// --------------------------------------------------------------------------


	this._get_template = function( slug )
	{
		for ( var _key in this.templates )
		{
			if ( slug === this.templates[_key].slug )
			{
				return this.templates[_key];
			}
		}

		return false;
	};


	// --------------------------------------------------------------------------


	this._get_widget = function( slug )
	{
		for ( var _key in this.widgets )
		{
			for ( var _key2 in this.widgets[_key].widgets )
			{
				if ( slug === this.widgets[_key].widgets[_key2].slug )
				{
					return this.widgets[_key].widgets[_key2];
				}
			}
		}

		return false;
	};



	// --------------------------------------------------------------------------


	this._array_search = function( needle, haystack, argStrict  )
	{
		//  discuss at: http://phpjs.org/functions/array_search/
		// original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		//    input by: Brett Zamir (http://brett-zamir.me)
		// bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		//  depends on: array
		//        test: skip
		//   example 1: array_search('zonneveld', {firstname: 'kevin', middle: 'van', surname: 'zonneveld'});
		//   returns 1: 'surname'
		//   example 2: ini_set('phpjs.return_phpjs_arrays', 'on');
		//   example 2: var ordered_arr = array({3:'value'}, {2:'value'}, {'a':'value'}, {'b':'value'});
		//   example 2: var key = array_search(/val/g, ordered_arr); // or var key = ordered_arr.search(/val/g);
		//   returns 2: '3'

		var strict = !! argStrict,
		key = '';

		if (haystack && typeof haystack === 'object' && haystack.change_key_case)	// Duck-type check for our own array()-created PHPJS_Array
		{
			return haystack.search(needle, argStrict);
		}
		if (typeof needle === 'object' && needle.exec)	// Duck-type for RegExp
		{
			if (!strict) // Let's consider case sensitive searches as strict
			{
				var flags = 'i' + (needle.global ? 'g' : '') +
				(needle.multiline ? 'm' : '') +
				(needle.sticky ? 'y' : ''); // sticky is FF only
				needle = new RegExp(needle.source, flags);
			}
			for (key in haystack)
			{
				if (needle.test(haystack[key]))
				{
					return key;
				}
			}
			return false;
		}

		for (key in haystack)
		{
			/* jshint ignore:start */
			if ((strict && haystack[key] === needle) || (!strict && haystack[key] == needle))
			{
				return key;
			}
			/* jshint ignore:end */
		}

		return false;
	};
};