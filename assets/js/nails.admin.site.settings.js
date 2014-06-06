var NAILS_Admin_Site_Settings;
NAILS_Admin_Site_Settings = function()
{
	this.init = function()
	{
		this._init_auth();
	};


	// --------------------------------------------------------------------------


	this._init_auth = function()
	{
		$( '#site-settings-socialsignin .toggle' ).on( 'toggle', function( e, active )
		{
			var _checkbox = $(this).parents( 'span.input' ).find( 'input[type=checkbox]' );

			if ( active )
			{
				$( '#' + _checkbox.data( 'fields' ) ).show();
			}
			else
			{
				$( '#' + _checkbox.data( 'fields' ) ).hide();
			}

			_nails.add_stripes();
		});
	};
};