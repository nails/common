
// --------------------------------------------------------------------------

//	NAILS_JS: A ton of helpful stuff

// --------------------------------------------------------------------------

//	Catch undefined console
/* jshint ignore:start */
if ( typeof( console ) === "undefined" )
{
	var console;
	console = {
		log: function() {},
		debug: function() {},
		info: function() {},
		warn: function() {},
		error: function() {}
	};
}
/* jshint ignore:end */

// --------------------------------------------------------------------------

var NAILS_JS;
NAILS_JS = function()
{
	this.init = function()
	{
		this._init_system_alerts();
		this._init_tipsy();
		this._init_fancybox();
		this._init_chosen();
		this._init_confirm();
		this._init_nicetime();
		this._init_tabs();
		this._init_forms();
	};


	// --------------------------------------------------------------------------


	/**
	 *
	 * Add a close button to any system-alerts which are on the page
	 *
	 **/
	this._init_system_alerts = function(){

		//	Add close buttons to messages
		var _close = $( '<p>' ).addClass( 'close' ).attr( 'rel', 'tipsy' ).attr( 'title', 'Dismiss' );
		_close.html( $( '<a>' ).attr( 'href', '#' ) );

		$( '.system-alert:not(.no-close)' ).addClass( 'close-enabled' ).find( '.padder' ).prepend( _close );

		// --------------------------------------------------------------------------

		//	Add handler to close button
		$( '.system-alert p.close a' ).on( 'click', function(){

			$(this).closest( '.padder' ).fadeOut();
			$(this).closest( '.system-alert' ).slideUp();

			return false;

		});

		// --------------------------------------------------------------------------

		//	Scroll to first error, if scrollTo is available
		if ( $.fn.scrollTo )
		{
			var _inline	= $( 'div.field.error:visible' );
			var _scroll;

			if ( _inline.length )
			{
				//	Scroll to this item
				_scroll = $(_inline.get(0));
			}
			else
			{
				var _system = $( 'div.system-alert.error:visible' );
				_scroll = $(_system.get(0));
			}

			if ( _scroll.length )
			{
				//	Giving the browser a slight chance to work out sizes etc
				setTimeout( function() { $.scrollTo( _scroll, 'fast', { axis: 'y', offset : { top: -25 } } ); }, 750 );
			}
		}
		else
		{
			this.error( 'NAILS_JS: scrollTo not available.' );
		}
	};


	// --------------------------------------------------------------------------


	/**
	 *
	 * Initialise any tipsy elements on the page
	 *
	 **/
	this._init_tipsy = function()
	{
		if ( $.fn.tipsy )
		{
			$( '*[rel=tipsy]' ).tipsy({ opacity : 0.85 });
			$( '*[rel=tipsy-html]' ).tipsy({ opacity : 0.85, html: true });
			$( '*[rel=tipsy-right]' ).tipsy({ opacity : 0.85, gravity: 'w' });
			$( '*[rel=tipsy-left]' ).tipsy({ opacity : 0.85, gravity: 'e' });
			$( '*[rel=tipsy-top]' ).tipsy({ opacity : 0.85, gravity: 's' });
			$( '*[rel=tipsy-bottom]' ).tipsy({ opacity : 0.85, gravity: 'n' });
		}
		else
		{
			this.error( 'NAILS_JS: Tipsy not available.' );
		}
	};


	// --------------------------------------------------------------------------


	/**
	 *
	 * Initialise any fancybox elements on the page
	 *
	 **/
	this._init_fancybox = function()
	{
		if ( $.fn.fancybox )
		{
			$(document).on( 'click', '.fancybox', function()
			{
				//	Prep the URL
				var _href	= $(this).attr( 'href' );

				if ( _href.substr( 0, 1 ) !== '#' )
				{
					var _regex	= /\?/g;

					if ( ! _regex.test( _href ) )
					{
						//	Append '?'
						_href += '?';
					}
					else
					{
						//	Append '&'
						_href += '&';
					}

					_href += 'is_fancybox=true';
				}

				//	Interpret width and height
				var _h = $(this).data( 'height' );
				var _w = $(this).data( 'width' );

				//	Open a new fancybox instance
				$(this).fancybox({
					'href'		: _href,
					'width'		: _w,
					'height'	: _h
				});

				if ( ! $(this).hasClass( 'fancyboxed' ) )
				{
					$(this).addClass( 'fancyboxed' );

					$(this).trigger( 'click' );
				}

				return false;
			});
		}
		else
		{
			this.error( 'NAILS_JS: Fancybox not available.' );
		}
	};


	// --------------------------------------------------------------------------


	/**
	 *
	 * Initialise any chosen elements on the page
	 *
	 **/
	this._init_chosen = function()
	{
		if ( $.fn.chosen )
		{
			$( 'select.chosen' ).each(function()
			{
				$(this).chosen(
				{
					width: $(this).css( 'width' )
				});
			});
		}
		else
		{
			this.error( 'NAILS_JS: Chosen not available.' );
		}
	};


	// --------------------------------------------------------------------------


	/**
	 *
	 * Initialise any confirm links or buttons on the page
	 *
	 **/
	this._init_confirm = function()
	{

		$(document).on( 'click', 'a.confirm' , function()
		{
			var _a		= $(this);
			var _body	= _a.data( 'body' ).replace( /\\n/g, "\n" );
			var _title	= _a.data( 'title' );

			if ( _body.length )
			{
				$('<div>').html(_body).dialog({
					title: _title,
					resizable: false,
					draggable: false,
					modal: true,
					dialogClass: "no-close",
					buttons:
					{
						OK: function()
						{
							window.location.href = _a.attr( 'href' );
						},
						Cancel: function()
						{
							$(this).dialog("close");
						}
					}
				});

				return false;
			}
			else
			{

				//	No message, just let the event bubble as normal.
				return true;
			}
		});
	};


	// --------------------------------------------------------------------------


	/**
	 *
	 * Initialise any nice-time DOM elements
	 *
	 **/
	this._init_nicetime = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		var _elems = $( '.nice-time:not(.nice-timed)' );	//	Fetch just new objects

		//	Fetch objects which can be nice-timed
		_elems.each( function() {

			//	Setup variables
			var _src = $(this).text();

			//	Check format
			var _regex = /^\d\d\d\d-\d\d?-\d\d?( \d\d?\:\d\d?\:\d\d?)?$/;

			if ( _regex.test( _src ) )
			{
				//	Parse into various bits
				var _basic = _src.split( ' ' );

				if ( ! _basic[1] )
				{
					_basic[1] = '00:00:00';
				}

				if ( _basic[0] )
				{
					var _date	= _basic[0].split( '-' );
					var _time	= _basic[1].split( ':' );

					var _Y = _date[0];
					var _M = _date[1];
					var _D = _date[2];
					var _h = _time[0];
					var _m = _time[1];
					var _s = _time[2];

					//	Attempt to parse the time
					var _date_obj = new Date( _Y, _M, _D, _h, _m, _s );

					if ( ! isNaN( _date_obj.getTime() ) )
					{
						//	Date was parsed successfully, stick it as the attribute.
						//	Add .nice-timed to it so it's not picked up as a new object

						$(this).addClass( 'nice-timed' );
						$(this).attr( 'data-time', _src );
						$(this).attr( 'data-year', _Y );
						$(this).attr( 'data-month', _M );
						$(this).attr( 'data-day', _D );
						$(this).attr( 'data-hour', _h );
						$(this).attr( 'data-minute', _m );
						$(this).attr( 'data-second', _s );
						$(this).attr( 'title', _src );
					}
				}
			}
		});

		// --------------------------------------------------------------------------

		//	Nice time-ify everything
		$( '.nice-timed' ).each( function() {

			//	Pick up date form object
			var _Y = $(this).attr( 'data-year' );
			var _M = $(this).attr( 'data-month' ) - 1;	//	Because the date object does months from 0
			var _D = $(this).attr( 'data-day' );
			var _h = $(this).attr( 'data-hour' );
			var _m = $(this).attr( 'data-minute' );
			var _s = $(this).attr( 'data-second' );

			var _date		= new Date( _Y, _M, _D, _h, _m, _s );
			var _now		= new Date();
			var _relative	= '';

			// --------------------------------------------------------------------------

			//	Do whatever it is we need to do to get relative time
			var _diff = Math.ceil( ( _now.getTime() - _date.getTime() ) / 1000 );

			if ( _diff >= 0 && _diff < 10 )
			{
				//	Has just happened so for a few seconds show plain ol' English
				_relative = 'just now';
			}
			else if ( _diff >= 10 )
			{
				//	Target time is in the past
				_relative = _this._nice_time_calc( _diff ) + ' ago';
			}
			else if ( _diff < 0)
			{
				//	Target time is in the future
				_relative = _this._nice_time_calc( _diff ) + ' from now';
			}
			// --------------------------------------------------------------------------

			//	Set the new relative time
			$(this).text( _relative );
		});
	};


	// --------------------------------------------------------------------------


	this._nice_time_calc = function( diff )
	{
		var _value = 0;
		var _term = '';

		//	Constants
		var _second	= 1;
		var _minute	= _second * 60;
		var _hour	= _minute * 60;
		var _day	= _hour * 24;
		var _week	= _day * 7;
		var _month	= _day * 30;
		var _year	= _day * 365;

		//	Always dealing with positive values
		if ( diff < 0 )
		{
			diff = diff * -1;
		}

		//	Seconds
		if ( diff < _minute )
		{
			_value = diff;
			_term = 'second';
		}

		//	Minutes
		else if ( diff < _hour )
		{
			_value = Math.floor( diff / 60 );
			_term = 'minute';
		}

		//	Hours
		else if ( diff < _day )
		{
			_value = Math.floor( diff / 60 / 60 );
			_term = 'hour';
		}

		//	Days
		else if ( diff < _week )
		{
			_value = Math.floor( diff / 60 / 60 / 24 );
			_term = 'day';
		}

		//	Weeks
		else if ( diff < _month )
		{
			_value = Math.floor( diff / 60 / 60 / 24 / 7 );
			_term = 'week';
		}

		//	Months
		else if ( diff < _year )
		{
			_value = Math.floor( diff / 60 / 60 / 24 / 30 );
			_term = 'month';
		}

		//	Years
		else
		{
			_value = Math.floor( diff / 60 / 60 / 24 / 365 );
			_term = 'year';
		}

		// --------------------------------------------------------------------------

		var _suffix = ( _value === 1 ) ? '' : 's';

		return _value + ' ' + _term + _suffix;
	};


	// --------------------------------------------------------------------------


	this._init_tabs = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( document ).on( 'click', 'ul.tabs li.tab a', function()
		{
			if ( ! $(this).hasClass( 'disabled' ) )
			{
				_this.switch_to_tab( $(this) );
			}

			return false;
		});

		// --------------------------------------------------------------------------

		//	Look for tabs which contain error'd fields
		$( 'li.tab a' ).each(function(){

			if ( $( '#' + $(this).data( 'tab' ) + ' div.field.error' ).length )
			{
				$(this).addClass( 'error' );
			}

			if ( $( '#' + $(this).data( 'tab' ) + ' .system-alert.error' ).length )
			{
				$(this).addClass( 'error' );
			}

			if ( $( '#' + $(this).data( 'tab' ) + ' .error.show-in-tabs' ).length )
			{
				$(this).addClass( 'error' );
			}
		});
	};


	// --------------------------------------------------------------------------


	this.switch_to_tab = function( switch_to )
	{
		//	Tab group
		var _tabs		= switch_to.parents( 'ul.tabs' );
		var _tabgroup	= switch_to.parents( 'ul.tabs' ).data( 'tabgroup' );
		_tabgroup		= _tabgroup ? '.' + _tabgroup : '';

		//	Switch tab
		$( 'li.tab', _tabs ).removeClass( 'active' );
		switch_to.parent().addClass( 'active' );

		// --------------------------------------------------------------------------

		//	Show results
		var _tab = switch_to.attr( 'data-tab' );
		$( 'section.tabs' + _tabgroup + ' > div.tab.page' ).removeClass( 'active' );
		$( '#' + _tab ).addClass( 'active' );

		// --------------------------------------------------------------------------

		this.add_stripes();
	};


	// --------------------------------------------------------------------------


	this._init_forms = function()
	{
		this.add_stripes();
		this.process_prefixed_inputs();

		// --------------------------------------------------------------------------

		//	Init any datetime pickers
		if ( $.fn.datepicker )
		{
			//	Date pickers
			$( 'div.field.date input.date' ).each(function()
			{
				//	Fetch some info which may be available in the data attributes
				var _dateformat	= $(this).data( 'datepicker-dateformat' ) || 'yy-mm-dd';
				var _yearrange	= $(this).data( 'datepicker-yearrange' ) || 'c-100:c+10';

				//	Instanciate datepicker
				$(this).datepicker(
				{
					dateFormat	: _dateformat,
					changeMonth	: true,
					changeYear	: true,
					yearRange	: _yearrange
				}).prop( 'readonly', true );
			});
		}
		else
		{
			this.error( 'NAILS_JS: datepicker not available.' );
		}

		if ( $.fn.datetimepicker )
		{
			//	Datetime pickers
			$( 'div.field.datetime input.datetime' ).each( function()
			{
				//	Fetch some info which may be available in the data attributes
				var _dateformat	= $(this).data( 'datepicker-dateformat' ) || 'yy-mm-dd';
				var _timeformat	= $(this).data( 'datepicker-timeformat' ) || 'HH:mm:ss';
				var _yearrange	= $(this).data( 'datepicker-yearrange' ) || 'c-100:c+10';

				$(this).datetimepicker(
				{
					dateFormat	: _dateformat,
					timeFormat	: _timeformat,
					changeMonth	: true,
					changeYear	: true,
					yearRange	: _yearrange
				}).prop( 'readonly', true );

			});
		}
		else
		{
			this.error( 'NAILS_JS: datetimepicker not available.' );
		}
	};


	// --------------------------------------------------------------------------


	this.add_stripes = function()
	{
		$( 'fieldset,.fieldset' ).each( function() {

			$( 'div.field', this ).removeClass( 'odd even' );
			$( 'div.field:visible:odd', this ).addClass( 'odd' );
			$( 'div.field:visible:even', this ).addClass( 'even' );

		});
	};


	// --------------------------------------------------------------------------


	this.process_prefixed_inputs = function()
	{
		$( 'input[data-prefix]:not(.nails-prefixed)' ).each(function()
		{
			var _container	= $( '<div>' ).addClass( 'nails-prefixed' ).css( 'width', $(this).css('width') );
			var _prefix		= $( '<div>' ).addClass( 'nails-prefix' ).html( $(this).data( 'prefix' ) );

			_container.append( _prefix );
			$(this).clone( true ).addClass( 'nails-prefixed' ).appendTo(_container);

			$(this).replaceWith(_container);
		});
	};


	// --------------------------------------------------------------------------


	this.log = function( output )
	{
		if ( window.console && ENVIRONMENT !== 'production' )
		{
			console.log( output);
		}
	};


	// --------------------------------------------------------------------------


	this.error = function( output )
	{
		if ( window.console && ENVIRONMENT !== 'production' )
		{
			console.error( output);
		}
	};
};