var NAILS_Admin_Blog_Create_Edit;
NAILS_Admin_Blog_Create_Edit = function()
{
	this.init = function(mode, upload_token)
	{
		//	Set vars
		this.mode			= mode;
		this.upload_token	= upload_token;

		// --------------------------------------------------------------------------

		//	Init everything!
		this._init_chosens();
		this._init_submit();
	};


	// --------------------------------------------------------------------------


	this._init_chosens = function() {
		var _this = this;

		// --------------------------------------------------------------------------

		//	Define targets and URLs
		var _target = {};
		var _url = {};

		_target.categories = '#tab-categories select.categories';
		_url.categories = SITE_URL + 'admin/blog/manage/categories?mode=' + _this.mode + '&is_fancybox=1';

		_target.tags = '#tab-tags select.tags';
		_url.tags = SITE_URL + 'admin/blog/manage/tags?mode=' + _this.mode + '&is_fancybox=1';

		_target.associations = '#tab-associations select';


		// --------------------------------------------------------------------------

		//	Init chosens
		$(_target.categories).chosen({
			footer_html: '<a href="#" class="manage-categories">Manage Categories</a>',
			width: '100%'
		});

		$(_target.tags).chosen({
			footer_html: '<a href="#" class="manage-tags">Manage Tags</a>',
			width: '100%'
		});

		$(_target.associations).chosen({
			width: '100%'
		});

		// --------------------------------------------------------------------------

		//	Bind fancybox to chosens
		$(document).on('click', 'a.manage-categories', function() {
			$.fancybox.open(_url.categories, {
				type: 'iframe',
				beforeClose: function() {
					_this._rebuild_chosen(_target.categories);
				}
			});
			return false;
		});

		$(document).on('click', 'a.manage-tags', function() {
			$.fancybox.open(_url.tags, {
				type: 'iframe',
				beforeClose: function() {
					_this._rebuild_chosen(_target.tags);
				}
			});
			return false;
		});

		// --------------------------------------------------------------------------

		//	Ensure all chosens are updated when their tab is shown
		$( 'ul.tabs a:not(.disabled)' ).on( 'click', function()
		{
			setTimeout(function()
			{
				$(_target.categories).trigger( 'chosen:updated' );
				$(_target.tags).trigger( 'chosen:updated' );
				$(_target.associations).trigger( 'chosen:updated' );
			}, 1);
		});
	};


	// --------------------------------------------------------------------------


	this._rebuild_chosen = function(target) {
		var _DATA = $('.fancybox-iframe').get(0).contentWindow._DATA;

		if (typeof(_DATA) === 'undefined') {
			//	Do nothing, nothing to work with
			return false;
		}

		//	Fetch the target(s)
		var _targets = $(target);

		if (!_targets.length) {
			//	Target doesn't exist, ignore
			return false;
		}

		//	Rebuild the target(s)
		_targets.each(function() {
			//	Save a referene to the target
			var _target = this;

			//	Get the currently selected items in this select
			//	and store as an array of ID's

			var _selected = [];
			$('option:selected', this).each(function() {
				_selected.push(parseInt($(this).val(), 10));
			});

			//	Rebuild, marking as selected where appropriate
			$(this).empty();
			$.each(_DATA, function() {
				var _option = $('<option>');

				_option.val(this.id);
				_option.html(this.label);

				if ($.inArray(this.id, _selected) > -1) {
					_option.prop('selected', true);
				}

				$(_target).append(_option);
			});

			//	Trigger the chosen
			$(this).trigger('chosen:updated');
		});
	};


	// --------------------------------------------------------------------------


	this._init_submit = function()
	{
		$( '#post-form' ).on( 'submit', $.proxy(function(){ return this._submit(); }, this ) );
	};


	// --------------------------------------------------------------------------


	this._submit = function()
	{
		var _form	= $( '#post-form' );
		var _errors	= 0;

		// --------------------------------------------------------------------------

		//	Reset everything
		$( 'ul.tabs li a.error, div.field.error' ).removeClass( 'error' );
		$( '#body-error' ).hide();

		// --------------------------------------------------------------------------

		//	ERror messages
		var msg =
		{
			required: 'This field is required'
		};

		// --------------------------------------------------------------------------

		//	Tab: Meta
		//	Title
		if ( ! $( 'input[name=title]', _form ).val().length )
		{
			_errors++;

			$( '#tabber-meta' ).addClass( 'error' );
			$( 'input[name=title]', _form ).closest( 'div.field' ).addClass( 'error' );
			$( 'input[name=title]', _form ).closest( 'div.field' ).find( 'span.error' ).text( msg.required );

		}

		//	Excerpt
		if ( ! $( 'textarea[name=excerpt]', _form ).val().length )
		{
			_errors++;

			$( '#tabber-meta' ).addClass( 'error' );
			$( 'textarea[name=excerpt]', _form ).closest( 'div.field' ).addClass( 'error' );
			$( 'textarea[name=excerpt]', _form ).closest( 'div.field' ).find( 'span.error' ).text( msg.required );

		}

		//	Body
		if ( ! $( 'textarea[name=body]', _form ).val().length )
		{
			_errors++;

			$( '#tabber-body' ).addClass( 'error' );
			$( 'textarea[name=body]', _form ).closest( 'div.field' ).addClass( 'error' );
			$( 'textarea[name=body]', _form ).closest( 'div.field' ).find( 'span.error' ).text( msg.required );

		}

		//	SEO Description
		if ( ! $( 'textarea[name=seo_description]', _form ).val().length )
		{
			_errors++;

			$( '#tabber-seo' ).addClass( 'error' );
			$( '#body-error' ).show().text( msg.required );

		}

		//	SEO Keywords
		if ( ! $( 'input[name=seo_keywords]', _form ).val().length )
		{
			_errors++;

			$( '#tabber-seo' ).addClass( 'error' );
			$( 'input[name=seo_keywords]', _form ).closest( 'div.field' ).addClass( 'error' );
			$( 'input[name=seo_keywords]', _form ).closest( 'div.field' ).find( 'span.error' ).text( msg.required );

		}

		if ( _errors )
		{

			//	Tab to the first error'd view
			$( 'ul.tabs a.error' ).first().click();

			return false;
		}
		else
		{
			return true;
		}
		return _errors ? false : true;
	};
};