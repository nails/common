var NAILS_Configure;
NAILS_Configure = function()
{
	this._token	= '';
	this._guid	= '';
	this._time	= '';

	// --------------------------------------------------------------------------

	this.init = function( token, guid, time )
	{
		this._token = token;
		this._guid	= guid;
		this._time	= time;

		// --------------------------------------------------------------------------

		//	Immediately run tests
		this._run_tests();
	};

	// --------------------------------------------------------------------------

	this._run_tests = function(  )
	{
		//	Make a GET request to the testing environment
		var _this = this;
		$.get(
			SITE_URL + 'system/test/run/json',
			{
				token: this._token,
				guid: this._guid,
				time: this._time
			},
			$.proxy( function( data ) { this._tests_ok( data ); }, this )
		).fail( function() { _this._tests_fail(); } );
	};

	// --------------------------------------------------------------------------

	this._tests_ok = function( data )
	{
		if ( data.total === data.pass )
		{
			//	All tests passed, hide alert
			$( '.system-alert.testing' ).remove();
		}
		else
		{
			var _url	= SITE_URL + 'system/test/run?token=' + this._token + '&guid=' + this._guid + '&time=' + this._time; 
			var _error	= '<strong>Oh no!</strong> Some tests failed. You should probably <a href="' + _url + '">check this out</a>.';
			$( '.system-alert.testing' ).removeClass( 'notice' ).addClass( 'error' ).html( _error );
		}
	};

	// --------------------------------------------------------------------------

	this._tests_fail = function()
	{
		var _message = '<strong>Failed to run tests.</strong> The tests failed to run, this may need investigated.';
		$( '.system-alert.testing' ).removeClass( 'notice' ).addClass( 'message' ).html( _message );
	};
};