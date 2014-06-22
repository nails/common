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

var NAILS_Admin;
NAILS_Admin = function()
{
	this.error_delay = 6000; //	Amount of time the error message will stay on screen.


	// --------------------------------------------------------------------------


	this.init = function() {
		this.init_boxes();
		this.init_navsearch();
		this.init_search_boxes();
		this.init_mobile_menu();
		this.init_fieldsets();
		this.init_toggles();
		this.init_ckeditor();
		this.init_select2();
	};


	// --------------------------------------------------------------------------


	this.init_boxes = function() {
		var _this = this;

		//	Bind click events
		$('.box .toggle').on('click', function(e) {

			if ($(this).parents('.box').hasClass('open'))
			{
				_this._open_box(this, true);
			}
			else
			{
				_this._close_box(this, true);
			}

			// --------------------------------------------------------------------------

			//	Save user's preference
			_this._save_nav();

			// --------------------------------------------------------------------------

			e.preventDefault();
			return false;
		});

		// --------------------------------------------------------------------------

		//	Set initial state of each box
		var _state, _toggle, _height, _container;

		$('ul.modules li.module').each(function() {

			_state = $(this).data( 'initial-state' );

			// --------------------------------------------------------------------------

			//	Determine height of each box and set it
			_toggle		= $(this).find( '.toggle' );
			_container	= $(this).find('.box-container');
			_height		= _container.outerHeight();

			_toggle.attr('data-height', _height);

			// --------------------------------------------------------------------------

			if ( _state === 'open' )
			{
				_this._open_box(_toggle, false);
			}
			else
			{
				_this._close_box(_toggle, false);
			}

		});

		// --------------------------------------------------------------------------

		//	Sortables!
		$( 'ul.modules' ).sortable({
			axis:'y',
			placeholder: 'sort-placeholder',
			items: 'li.module:not(.no-sort)',
			handle: '.handle',
			containment: 'parent',
			start: function(e, ui )
			{
				ui.placeholder.height(ui.helper.outerHeight());
			},
			stop: function()
			{
				_this._save_nav();
			}
		});
	};


	// --------------------------------------------------------------------------


	this._save_nav = function()
	{
		var _data, _call, _open;

		_data = {};

		$( 'ul.modules li.module' ).each(function()
		{
			_open = $( '.box', this ).hasClass( 'closed' );
			_data[$(this).data( 'module' )] = {
				open : _open
			};
		});

		_call =
		{
			controller : 'admin',
			method: 'nav/save',
			data :
			{
				preferences : _data
			}
		};

		_nails_api.call(_call);
	};


	// --------------------------------------------------------------------------


	this._open_box = function(toggle, animate) {
		var _id, _height;

		_id = $(toggle).parents('.box').attr('id');

		// --------------------------------------------------------------------------

		$(toggle).parents('.box').removeClass('open');
		$(toggle).parents('.box').addClass('closed');

		//	Set the height (so it animates)
		_height = $(toggle).attr('data-height');

		if (animate) {
			$(toggle).parents('.box').find('.box-container').stop().animate({
				'height': _height
			});
		} else {
			$(toggle).parents('.box').find('.box-container').height(_height);
		}
	};


	// --------------------------------------------------------------------------


	this._close_box = function(toggle, animate) {
		var _id;

		_id = $(toggle).parents('.box').attr('id');

		// --------------------------------------------------------------------------

		$(toggle).parents('.box').removeClass('closed');
		$(toggle).parents('.box').addClass('open');

		//	Set the height (so it animates)
		if (animate) {
			$(toggle).parents('.box').find('.box-container').stop().animate({
				'height': 0
			});
		} else {
			$(toggle).parents('.box').find('.box-container').height(0);
		}
	};


	// --------------------------------------------------------------------------


	this._save = function(key, value) {
		if (typeof(localStorage) === 'undefined') {
			this._show_error(window.NAILS.LANG.non_html5);
		} else {
			try {
				localStorage.setItem(key, value);

			} catch (e) {
				this._show_error(window.NAILS.LANG.no_save);
			}
		}
	};


	// --------------------------------------------------------------------------


	this._get = function(key) {
		if (typeof(localStorage) === 'undefined') {
			this._show_error(window.NAILS.LANG.non_html5);
		} else {
			try {
				return localStorage.getItem(key);
			} catch (e) {
				this._show_error(window.NAILS_LANG.no_save);
			}
		}
	};


	// --------------------------------------------------------------------------


	this.init_navsearch = function() {
		var _this = this;

		$('.nav-search input').on('keyup', function() {
			var _search = $(this).val();

			if (_search.length) {
				$('.box .toggle').hide();

				//	Loop through each menu item and hide items which don't apply to the search term
				$('.box li').each(function() {

					var regex = new RegExp(_search, 'gi');

					if (regex.test($(this).text())) {
						$(this).show();
					} else {
						$(this).hide();
					}

					//	Resize the item to accomodate the number of viewable options
					var _height = $(this).parents('.box').find('ul').outerHeight();
					$(this).parents('.box').find('.box-container').height(_height);

					//	If there are no items viewable, fade out the box
					if (!$(this).parents('.box').find('li:visible').length) {
						$(this).parents('.box').stop().css({
							'opacity': 0.25
						});
					} else {
						$(this).parents('.box').stop().css({
							'opacity': 1
						});
					}
				});
			} else {
				//	Reset search
				$('.box').stop().animate({
					'opacity': 1
				}, 'fast');

				$('.box li').each(function() {

					$(this).show();

				});

				$('.box .toggle').each(function() {

					$(this).show();

					var _state = _this._get('adminbox-' + $(this).parents('.box').attr('id'));

					if (_state === 'closed') {
						_this._close_box(this, false);
					} else {
						_this._open_box(this, false);
					}

				});
			}
		});
	};


	// --------------------------------------------------------------------------


	this.init_search_boxes = function() {
		//	Bind submit to select changes
		$('div.search select, div.search input[type=checkbox]').on('change', function() {

			$(this).closest('form').find('input[type=submit]').click();

		});

		// --------------------------------------------------------------------------

		//	Show amsk when submitting form
		$('div.search form').on('submit', function() {

			$(this).closest('div.search').find('div.mask').show();

		});
	};


	// --------------------------------------------------------------------------


	this.init_mobile_menu = function() {
		$('#mobile-menu-main').on('change', function() {

			var _url = $(this).find('option:selected').attr('data-url');

			if (_url.length) {
				window.location.href = window.SITE_URL + _url;
			}

		});
	};


	// --------------------------------------------------------------------------


	this.init_fieldsets = function() {
		var _this = this; /*	Ugly Scope Hack	*/
		var _toggle;

		$('fieldset:not(.collapsable)').each(function() {

			_toggle = $('<a>').attr({
				href: '#',
				'class': 'fieldset-toggle'
			}).html('<span class="show">Show</span><span class="hide">Hide</span>');
			_toggle.on('click', function() {
				_this._fieldset_toggle($(this));
				return false;
			});
			$(this).prepend(_toggle).addClass('collapsable');

			// --------------------------------------------------------------------------

			//	Attempt to set the initial state of the fieldset
			if ($(this).attr('id')) {
				if (_this._get('fieldset-' + $(this).attr('id')) === 'closed') {
					$(this).addClass('closed');
				}
			}

		});
	};


	// --------------------------------------------------------------------------


	this._fieldset_toggle = function(obj)
	{
		var _fieldset = obj.closest('fieldset');

		if (_fieldset.hasClass('closed')) {
			_fieldset.removeClass('closed');

			if (_fieldset.attr('id')) {
				this._save('fieldset-' + _fieldset.attr('id'), 'open');
			}
		} else {
			_fieldset.addClass('closed');

			if (_fieldset.attr('id')) {
				this._save('fieldset-' + _fieldset.attr('id'), 'closed');
			}
		}
	};


	// --------------------------------------------------------------------------


	this.init_toggles = function()
	{
		if ( $.fn.toggles )
		{
			$('.field.boolean:not(.toggled)').each(function()
			{
				var _checkbox	= $(this).find('input[type=checkbox]');
				var _readonly	= $(this).hasClass('readonly');
				var _on			= $(this).data('text-on')	? $(this).data('text-on')	: 'ON';
				var _off		= $(this).data('text-off')	? $(this).data('text-off')	: 'OFF';

				$(this).find('.toggle').css({
					width:	'100px',
					height:	'30px'
				}).toggles({
					checkbox:	_checkbox,
					click:		!_readonly,
					drag:		!_readonly,
					clicker:	_checkbox,
					on:			_checkbox.is(':checked'),
					text:
					{
						on:		_on,
						off:	_off
					}
				});

				$(this).addClass( 'toggled' );

				_checkbox.hide();

			});
		}
		else
		{
			this.error('NAILS_ADMIN_JS: jQuery Toggles not available.');
		}
	};


	// --------------------------------------------------------------------------


	this.init_ckeditor = function()
	{
		if ( $.fn.ckeditor )
		{
			$( '.wysiwyg' ).ckeditor(
			{
				customConfig: window.NAILS.URL + 'js/libraries/ckeditor/ckeditor.config.min.js'
			});
		}
		else
		{
			this.error('NAILS_ADMIN_JS: CKEditor not available.');
		}
	};


	// --------------------------------------------------------------------------


	/**
	 *
	 * Initialise any select2 elements on the page
	 *
	 **/
	this.init_select2 = function()
	{
		if ( $.fn.select2 )
		{
			$( 'select.select2:not(.select2-offscreen)' ).select2();
		}
		else
		{
			this.error( 'NAILS_JS: Select2 not available.' );
		}
	};

	// --------------------------------------------------------------------------


	this._show_error = function(msg) {
		$('.js_error span.message').text(msg);
		$('.js_error').slideDown().delay(this.error_delay).slideUp();
		return true;
	};


	// --------------------------------------------------------------------------


	this.error = function(output) {
		if (window.console && window.ENVIRONMENT !== 'production') {
			console.error(output);
		}
	};
};