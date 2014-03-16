/**
 * api.js
 *
 * This class handles API requests
 *
 **/

var NAILS_API;
NAILS_API = function()
{
	this._api_base		= '';				//	The base of the API
	this._hash			= '';				//	The hash to send along with each request
	this._guid			= '';				//	The guid to send along with each request
	this._csrf_cookie	= 'nailscsrftoken';	//	The name of the CSRF token to send, hard coded in config.php too
	this._csrf_token	= 'nailscsrftest';	//	The name of the cooking containing the CSRF token, hard coded in config.php too

	// --------------------------------------------------------------------------


	/* !INIT & SETUP */


	/**
	 * Sets everything up and binds all the listeners
	 *
	 **/
	this.init = function( hash, guid )
	{
		this._hash = hash;
		this._guid = guid;

		// --------------------------------------------------------------------------

		//	If we're on a secure conenction then make our request secure as well
		//	TODO: check the domain as well as the protocol

		if ( document.location.protocol === 'https:' )
		{
			this._api_base = window.SITE_URL + 'api/';
		}
		else
		{
			this._api_base = window.SECURE_SITE_URL + 'api/';
		}
	};


	// --------------------------------------------------------------------------


	/**
	 * Sets everything up and binds all the listeners
	 *
	 **/
	this.call = function( controller, method, data, success, error, action, async )
	{
		//	Define the settings for this request
		//	If controller is an object then use that as our settings

		var _settings = {};

		if ( typeof( controller ) === 'object' )
		{
			_settings = controller;

			//	Make sure we have everything we need to make the call and handle the result
			if ( typeof( _settings.controller ) === 'undefined' )
			{
				throw new Error('Controller not defined');
			}

			if ( typeof( _settings.method ) === 'undefined' )
			{
				throw new Error('Method not defined');
			}

			if ( typeof( _settings.data ) === 'undefined' )
			{
				_settings.data = {};
			}

			if ( typeof( _settings.success ) === 'undefined' )
			{
				_settings.success = function() {};
			}

			if ( typeof( _settings.error ) === 'undefined' )
			{
				_settings.error = function() {};
			}

			if ( typeof( _settings.action ) === 'undefined' )
			{
				_settings.action = 'GET';
			}

			if ( typeof( _settings.async ) === 'undefined' )
			{
				_settings.async = true;
			}
		}
		else
		{
			//	Otherwise define each item individualy
			_settings.controller	= typeof( controller ) === 'undefined'	? '' : controller;
			_settings.method		= typeof( method ) === 'undefined'		? '' : method;
			_settings.data			= typeof( data ) === 'undefined'		? {} : data;
			_settings.success		= typeof( success ) === 'undefined'		? function() {} : success;
			_settings.error			= typeof( error ) === 'undefined'		? function() {} : error;
			_settings.action		= typeof( action ) === 'undefined'		? 'GET' : action;
			_settings.async			= typeof( async ) === 'undefined'		? true : async;
		}

		// --------------------------------------------------------------------------

		//  If the CSRF token is set and the request is a POST request then make sure
		//  the cookie is sent along with the request

		if ( _settings.action === 'POST' )
		{
			var _csrf_cookie = $.cookie( this._csrf_cookie );

			if ( typeof( _csrf_cookie ) === 'string' && _csrf_cookie.length > 0 )
			{
				var _csrf = {};
				_csrf[this._csrf_token] = _csrf_cookie;

				_settings.data = $.extend({}, _settings.data, _csrf );
			}
		}

		// --------------------------------------------------------------------------

		//	Mix in some authentication variables
		var _auth =
		{
			'api_token': this._hash,
			'api_guid': this._guid
		};

		_settings.data = $.extend({}, _settings.data, _auth);

		// --------------------------------------------------------------------------

		//	Actually make the request now
		$.ajax(
		{
			'url'		: this._api_base + _settings.controller + '/' + _settings.method,
			'data'		: _settings.data,
			'dataType'	: 'json',
			'success'	: _settings.success,
			'error'		: _settings.error,
			'type'		: _settings.action,
			'async'		: _settings.async
		});
	};
};