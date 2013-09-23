var NAILS_Admin_CMS_Pages_Editor;
NAILS_Admin_CMS_Pages_Editor = function()
{
	this.counter	= 0;
	this.area		= '';
	this.container	= null;

	// --------------------------------------------------------------------------

	this.init = function( area )
	{
		this.area = area;
		this.container = $( '#cms-page-edit-' + area );

		// --------------------------------------------------------------------------

		this._init_draggables();
		this._init_deletes();
	};


	// --------------------------------------------------------------------------


	this._init_draggables = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/

		//	Widgets
		$( 'li.widget', this.container ).draggable({
			connectToSortable:'#cms-page-edit-' + this.area + ' ul.holders',
			helper:'clone'
		});

		//	Holders
		$( 'ul.holders', this.container ).sortable({
			handle : '.handle',
			placeholder: 'holder target',
			start: function(e,ui){
				//	Call the start method for this widget
				var _func = 'start_' + $(ui.item).attr( 'data-template' );
				if (typeof(window[_func]) === 'function' )
				{
					window[_func].call();
				}
			},
			stop: function(e,ui) {

				var _slug = $(ui.item).attr( 'data-template' );

				_this._widget_dropped(e,ui,_slug);

				//	Call the stop method for this widget
				var _func = 'stop_' + _slug;
				if (typeof(window[_func]) === 'function' )
				{
					window[_func].call();
				}
			}
		});
	};


	// --------------------------------------------------------------------------


	this._widget_dropped = function(e,ui,slug)
	{
		if (ui.item.hasClass( 'holder' ))
		{
			//	Do nothing, we're dragging a holder, not adding a widget.
			return true;
		}

		// --------------------------------------------------------------------------

		$( '#cms-page-edit-' + this.area + ' ul.holders' ).removeClass( 'empty' );
		var _data = { key : this.area, counter : this.counter };
		this.counter++;

		//	Make a new holder object
		var _holder = $( '<li>' ).addClass( 'holder ' + slug ).attr( 'data-template', slug );

		//	Get instance of new widget
		var _template_html	= $( '#' + slug ).html();

		_holder.html( Mustache.render( _template_html, _data ) );

		ui.item.replaceWith( _holder );
	};


	// --------------------------------------------------------------------------


	this._init_deletes = function()
	{
		var _this = this;

		$(document).on( 'click', '#cms-page-edit-' + this.area + ' .holder a.close', function() {

			var _item = $(this);

			$('<div>').text( 'If you continue, the widget will be removed from the interface. You will need to save changes to commit the deletion.' ).dialog({
				title: 'Are you sure?',
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					"Delete Widget": function()
					{
						//	Remove the widget
						_item.closest( 'li.holder' ).remove();

						//	Show the empty?
						if ( $('#cms-page-edit-' + _this.area + ' li.holder').length === 0 )
						{
							$( '#cms-page-edit-' + _this.area + ' ul.holders' ).addClass( 'empty' );
						}
						else
						{
							$( '#cms-page-edit-' + _this.area + ' ul.holders' ).removeClass( 'empty' );
						}

						//	Close dialog
						$(this).dialog("close");
					},
					Cancel: function()
					{
						$(this).dialog("close");
					}
				}
			});

			// --------------------------------------------------------------------------

			return false;

		} );
	};
};