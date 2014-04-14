var NAILS_Admin_Blog_Settings;
NAILS_Admin_Blog_Settings = function()
{
	this.init = function()
	{
		this._init_comments();

		// --------------------------------------------------------------------------

		//	Set up shop
		this._comment_engine_change();
	};


	// --------------------------------------------------------------------------


	this._init_comments = function()
	{
		var _this = this;

		$( '#comment-engine' ).on( 'change', function()
		{
			_this._comment_engine_change();
		});
	};


	// --------------------------------------------------------------------------


	this._comment_engine_change = function()
	{
		switch( $( '#comment-engine' ).val() )
		{
			case 'NATIVE' :

				$( '#native-settings' ).show();
				$( '#disqus-settings' ).hide();

			break;

			// --------------------------------------------------------------------------

			case 'DISQUS' :

				$( '#native-settings' ).hide();
				$( '#disqus-settings' ).show();

			break;
		}
	};
};