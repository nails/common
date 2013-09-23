/**
 * api.js
 *
 * This class handles API requests
 *
 * Author: Pablo de la Peña (@hellopablo)
 *
 **/

var NAILS_API;
NAILS_API = function()
{
    this.api_base = window.SITE_URL + 'api/'; //	The base of the API
    this.hash = ''; //	The hash to send along with each request
    this.guid = ''; //	The guid to send along with each request

    // --------------------------------------------------------------------------


    /* !INIT & SETUP */


    /**
     * Sets everything up and binds all the listeners
     *
     **/
    this.init = function(hash, guid) {
        this.hash = hash;
        this.guid = guid;

        // --------------------------------------------------------------------------

        //	If we're on a secure conenction then make our request secure as well
        if (document.location.protocol === 'https:') {
            this.api_base = this.api_base.replace('http://', 'https://');
        }
    };


    // --------------------------------------------------------------------------


    /**
     * Sets everything up and binds all the listeners
     *
     **/
    this.call = function(controller, method, data, success, error, action, async) {
        //	Define the settings for this request
        //	If controller is an object then use that as our settings

        var _settings = {};

        if (typeof(controller) === 'object') {
            _settings = controller;

            //	Make sure we have everything we need to make the call and handle the result
            if (typeof(_settings.controller) === 'undefined') {
                throw new Error('Controller not defined');
            }

            if (typeof(_settings.method) === 'undefined') {
                throw new Error('Method not defined');
            }

            if (typeof(_settings.data) === 'undefined') {
                _settings.data = {};
            }

            if (typeof(_settings.success) === 'undefined') {
                _settings.success = function() {};
            }

            if (typeof(_settings.error) === 'undefined') {
                _settings.error = function() {};
            }

            if (typeof(_settings.action) === 'undefined') {
                _settings.action = 'GET';
            }

            if (typeof(_settings.async) === 'undefined') {
                _settings.async = true;
            }
        } else {
            //	Otherwise define each item individualy
            _settings.controller = (typeof(controller) === 'undefined') ? '' : controller;
            _settings.method = (typeof(method) === 'undefined') ? '' : method;
            _settings.data = (typeof(data) === 'undefined') ? {} : data;
            _settings.success = (typeof(success) === 'undefined') ? function() {} : success;
            _settings.error = (typeof(error) === 'undefined') ? function() {} : error;
            _settings.action = (typeof(action) === 'undefined') ? 'GET' : action;
            _settings.async = (typeof(async) === 'undefined') ? true : async;
        }

        // --------------------------------------------------------------------------

        //	Mix in some authentication variables
        var _auth = {
            'api_token': this.hash,
            'api_guid': this.guid
        };
        _settings.data = $.extend({}, _settings.data, _auth);

        // --------------------------------------------------------------------------

        //	Actually make the request now
        $.ajax({
            'url': this.api_base + _settings.controller + '/' + _settings.method,
            'data': _settings.data,
            'dataType': 'json',
            'success': _settings.success,
            'error': _settings.error,
            'type': _settings.action,
            'async': _settings.async
        });
    };
};