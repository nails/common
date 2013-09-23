var NAILS_Admin_CMS_Pages;
NAILS_Admin_CMS_Pages = function()
{
	this.init_search = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( '.search-text input' ).on( 'keyup', function() { _this._do_search( $(this).val() ); return false; } );
	};
	
	
	// --------------------------------------------------------------------------
	
	
	this._do_search = function( term )
	{
		$( 'tr.page' ).each(function()
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
};