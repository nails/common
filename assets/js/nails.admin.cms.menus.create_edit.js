var NAILS_Admin_CMS_Menus_Create_Edit;
NAILS_Admin_CMS_Menus_Create_Edit = function()
{
	this._item_template	= '';
	this._id_length		= 32;


	// --------------------------------------------------------------------------


	this.init = function( items )
	{
		var _this = this;
		this._item_template = $( '#template-item' ).html();


		//	Init NestedSortable
		$( 'div.nested-sortable' ).each(function()
		{
			var _container,_html;
			_container = $(this).children( 'ol.nested-sortable' ).first();

			//	Build initial menu items
			for ( var _key in items )
			{
				items[_key].counter = $( 'li.target' ).length;

				_html = Mustache.render( _this._item_template, items[_key] );

				//	Does this have a parent? If so then we need to append it there
				var _target;

				if ( items[_key].parent_id !== null && items[_key].parent_id !== '' )
				{
					//	Find the parent and append to it's <ol class="nested-sortable-sub">
					_target = $( 'li.target-' + items[_key].parent_id + ' ol.nested-sortable-sub' ).first();
				}
				else
				{
					_target = _container;
				}

				_target.append(_html);
			}

			// --------------------------------------------------------------------------

			//	Sortitize!
			_container.nestedSortable({
				handle: 'div.handle',
				items: 'li',
				toleranceElement: '> div',
				stop: function()
				{
					//	Update orders
					_this._update_orders( _container );

					//	Update parents
					_this._update_parent_ids( _container );
				}
			});

			// --------------------------------------------------------------------------

			//	Bind to add button
			$(this).find( 'a.add-item' ).on( 'click', function()
			{
				var _data =
				{
					id: _this._generate_id(),
					counter: $( 'li.target' ).length
				};

				var _html = Mustache.render( _this._item_template, _data );

				_container.append( _html );

				_this._update_orders();

				return false;
			});
		});

		// --------------------------------------------------------------------------

		//	Bind to remove buttons
		$(document).on( 'click', 'a.item-remove', function()
		{
			var _obj = $(this);

			$('<div>')
			.html( '<p>This will remove this menu item (and any children) from the interface.</p><p>You will still need to "Save Changes" to commit the removal</p>' )
			.dialog(
			{
				title: 'Are you sure?',
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					OK: function()
					{
						_obj.closest( 'li.target' ).remove();
						$(this).dialog("close");
					},
					Cancel: function()
					{
						$(this).dialog("close");
					}
				}
			})
			.show();

			return false;
		});
	};


	// --------------------------------------------------------------------------


	this._update_orders = function( container )
	{
		var _counter = 0;
		$( 'input.input-order', container ).each( function()
		{
			$(this).val( _counter );
			_counter++;
		});
	};


	// --------------------------------------------------------------------------


	this._update_parent_ids = function( container )
	{
		$( 'input.input-parent_id', container ).each(function()
		{
			var _parent_id = $(this).closest( 'ol' ).closest( 'li' ).data( 'id' );
			$(this).val( _parent_id );
		});
	};


	// --------------------------------------------------------------------------


	this._generate_id = function( )
	{
		var chars	= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		var _result;
		do
		{
			_result	= 'newid-';

			for ( var i = this._id_length; i > 0; --i )
			{
				_result += chars[Math.round(Math.random() * (chars.length - 1))];
			}
		}
		while( $( 'li.target-' + _result ).length > 0 );

		return _result;
	};
};