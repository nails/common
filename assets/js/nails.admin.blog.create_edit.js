var NAILS_Admin_Blog_Create_Edit;
NAILS_Admin_Blog_Create_Edit = function()
{
	this.upload_token	= null;
	this._api			= null;

	// --------------------------------------------------------------------------


	this.init = function( upload_token )
	{
		//	Set vars
		this.upload_token	= upload_token;

		// --------------------------------------------------------------------------

		//	Set up the API interface
		this._api = new window.NAILS_API();

		// --------------------------------------------------------------------------

		//	Init everything!
		this._init_chosens();
		this._init_gallery();
		this._init_submit();
	};


	// --------------------------------------------------------------------------


	this._init_chosens = function()
	{
		var _this = this;

		// --------------------------------------------------------------------------

		//	Define targets and URLs
		var _target = {};
		var _url = {};

		_target.categories	= '#tab-categories select.categories';
		_url.categories		= window.SITE_URL + 'admin/blog/manage/categories?is_fancybox=1';

		_target.tags	= '#tab-tags select.tags';
		_url.tags		= window.SITE_URL + 'admin/blog/manage/tags?is_fancybox=1';

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


	this._init_gallery = function()
	{
		var _this = this;

		//	Init uploadify
		//	TODO: replace uploadify, it's lack of session support is killing me.
		//	Additionally, if CSRF is enabled, this won't work.

		$('#file_upload').uploadify({
			'debug': false,
			'auto': true,
			'swf': window.SITE_URL + 'vendor/shed/nails/assets/swf/jquery.uploadify/uploadify.swf',
			'queueID': 'gallery-items',
			'uploader': window.SITE_URL + 'api/cdnapi/object_create/script.php',
			'fileSizeLimit': 2048,
			'fileObjName': 'upload',
			'fileTypeExts': '*.gif; *.jpg; *.jpeg; *.png',
			'formData': {
				'token': this.upload_token,
				'bucket': 'blog-post-gallery',
				'return': 'URL|THUMB|100x100,34x34'
			},
			'itemTemplate': $('#template-uploadify').html(),
			'onSelect': function() {
				if ($('#gallery-items li.gallery-item').length) {
					$('#gallery-items li.empty').fadeOut(250, function() {
						$(this).parent().removeClass('empty');
					});
				}
			},
			'onUploadStart': function() {
				var _uploading_string = 'Uploading...';
				var _button_val = $('#post-form input[type=submit]').val();
				window.onbeforeunload = function() {
					return 'Uploads are in progress. Leaving this page will cause them to stop.';
				};

				//	Disable tabs - SWFUpload aborts uploads if it is hidden.
				$('ul.tabs li a').addClass('disabled');
				$('#upload-message').show();

				if (_button_val !== _uploading_string) {
					$('#post-form input[type=submit]').attr({
						'data-old_val': _button_val,
						'disabled': 'disabled'
					}).val('Uploading...');
				}
			},
			'onQueueComplete': function() {
				$('#post-form').off('submit');
				_this._init_submit();

				$('#post-form input[type=submit]').removeAttr('disabled').val($('#post-form input[type=submit]').data('old_val'));
				window.onbeforeunload = null;

				//	Enable tabs - SWFUpload aborts uploads if it is hidden.
				$('ul.tabs li a').removeClass('disabled');
				$('#upload-message').hide();
			},
			'onUploadProgress': function(file, bytesUploaded, bytesTotal) {
				var _percent = bytesUploaded / bytesTotal * 100;

				$('#' + file.id + ' .progress').css('height', _percent + '%');
			},
			'onUploadSuccess': function(file, data) {
				var _data;
				try
				{
					_data = JSON.parse( data );
				}
				catch( err )
				{
					_data = {};
				}

				// --------------------------------------------------------------------------

				var _html = $.trim($('#template-gallery-item').html());
				var _item = $($.parseHTML(_html));

				_item.attr('id', file.id + '-complete');
				$('#' + file.id).replaceWith(_item);

				// --------------------------------------------------------------------------

				var _target = $('#' + file.id + '-complete');

				if (!_target.length) {
					_html = $.trim($('#template-gallery-item').html());
					_item = $($.parseHTML(_html));

					_item.attr('id', file.id + '-complete');
					$('#' + file.id).replaceWith(_item);

					_target = $('#' + file.id + '-complete');
				}

				// --------------------------------------------------------------------------

				//	Switch the response code
				if (_data.status === 200) {
					//	Insert the image
					var _img = $('<img>').attr('src', _data.object_url[0]).on('load', function() {
						_target.removeClass('crunching');
					});
					var _del = $('<a>').attr({
						'href': '#',
						'class': 'delete',
						'data-object_id': _data.object_id
					});

					_target.append(_img).append(_del).find('input').val(_data.object_id);

					// --------------------------------------------------------------------------

					//	Update any variations

					//	Create a new checkbox item
					_img = $('<img>').attr({
						'src': _data.object_url[1]
					});
					var _in = $('<input>').attr({
						'type': 'checkbox',
						'value': _data.object_id
					});
					var _li = $('<li>').addClass('image object-id-' + _data.object_id).append(_in).append(_img);

					//	Find variations and for each of them append this item
					$('#product-variations .variation').each(function() {
						//	Set the name of the checkbox based on this variants counter ID
						_in.attr('name', 'variation[' + $(this).data('counter') + '][gallery][]');

						$('ul.gallery-associations', this).removeClass('empty');
						_li.clone().insertBefore( $( 'ul.gallery-associations li.actions', this ) );
					});

					//	Now update the template
					var _template = $('<div>').html($.parseHTML($.trim($('#template-variation').html(), null, true)));

					_in.attr('name', 'variation[{{counter}}][gallery][]');
					$('ul.gallery-associations', _template).removeClass('empty');
					_li.clone().insertBefore( $( 'ul.gallery-associations li.actions', _template ) );

					//	Replace the template
					$('#template-variation').html($(_template).html());
				} else {
					//	An error occurred
					var _filename = $('<p>').addClass('filename').text(file.name);
					var _message = $('<p>').addClass('message').text(_data.error);

					_target.addClass('error').append(_filename).append(_message).removeClass('crunching');
				}
			},
			'onUploadError': function(file, errorCode, errorMsg, errorString) {
				var _target = $('#' + file.id + '-complete');

				if (!_target.length) {
					var _html = $.trim($('#template-gallery-item').html());
					var _item = $($.parseHTML(_html));

					_item.attr('id', file.id + '-complete');
					$('#' + file.id).replaceWith(_item);

					_target = $('#' + file.id + '-complete');
				}

				var _filename = $('<p>').addClass('filename').text(file.name);
				var _message = $('<p>').addClass('message').text(errorString);

				_target.addClass('error').append(_filename).append(_message).removeClass('crunching');
			}
		});

		// --------------------------------------------------------------------------

		//	Init sorting
		$('#gallery-items').disableSelection().sortable({
			placeholder: 'gallery-item placeholder',
			items: "li.gallery-item"
		});

		// --------------------------------------------------------------------------

		//	Removes/Cancels an upload
		$(document).on('click', '#gallery-items .gallery-item .remove', function()
		{
			var _instance_id = $(this).data('instance_id');
			var _file_id = $(this).data('file_id');

			$('#' + _instance_id).uploadify('cancel', _file_id);
			$('#' + _file_id + ' .data-cancel').text('Cancelled').show();
			$('#' + _file_id).addClass('cancelled');

			if ($('#gallery-items li.gallery-item:not(.cancelled)').length === 0)
			{
				$('#gallery-items').addClass('empty');
				$('#gallery-items li.empty').css('opacity', 0).delay(1000).animate({
					opacity: 1
				}, 250);
			}

			return false;
		});

		// --------------------------------------------------------------------------

		//	Deletes an uploaded image
		$(document).on('click', '#gallery-items .gallery-item .delete', function()
		{
			var _object = this;

			$('#dialog-confirm-delete').dialog(
			{
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					"Delete Image": function()
					{
						var _object_id = $(_object).data('object_id');

						//	Send off the delete request
						var _call = {
							'controller'	: 'cdnapi',
							'method'		: 'object_delete',
							'action'		: 'POST',
							'data'			:
							{
								'object_id': _object_id
							}
						};
						_this._api.call( _call );

						// --------------------------------------------------------------------------

						$(_object).closest('li.gallery-item').addClass('deleted').fadeOut('slow', function()
						{
							$(_object).closest('li.gallery-item').remove();
						});

						//	Remove the image from any variations
						$('.image.object-id-' + _object_id).remove();

						//	Update the template
						var _template = $('<div>').html($.parseHTML($.trim($('#template-variation').html(), null, true)));

						$('.image.object-id-' + _object_id, _template).remove();

						// --------------------------------------------------------------------------

						//	Show the empty screens
						if ($('#gallery-items li.gallery-item:not(.deleted)').length === 0)
						{
							$('#gallery-items').addClass('empty');
							$('#gallery-items li.empty').css('opacity', 0).delay(1000).animate({
								opacity: 1
							}, 250);

							//	Variations
							$( 'ul.gallery-associations' ).addClass( 'empty' );

							//	Template
							$('ul.gallery-associations', _template).addClass( 'empty' );
						}

						// --------------------------------------------------------------------------

						//	Replace the template
						$('#template-variation').html($(_template).html());

						// --------------------------------------------------------------------------

						//	Close dialog
						$(this).dialog("close");
					},
					Cancel: function()
					{
						$(this).dialog("close");
					}
				}
			});

			return false;
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
		$( 'ul.tabs li a.error,div.field.error' ).removeClass( 'error' );
		$( 'div.field.error span.error' ).remove();
		$( '#body-error' ).hide();

		// --------------------------------------------------------------------------

		//	Error messages
		var msg =
		{
			required: '<span class="error">This field is required</span>'
		};

		// --------------------------------------------------------------------------

		//	Title
		if ( ! $( 'input[name=title]', _form ).val().length )
		{
			_errors++;

			$( '#tabber-meta' ).addClass( 'error' );
			$( 'input[name=title]', _form ).closest( 'div.field' ).addClass( 'error' );
			$( 'input[name=title]', _form ).closest( 'div.field' ).find( 'span.input' ).append( msg.required );

		}

		//	Body
		var _body_length;
		if ( typeof(CKEDITOR) === 'object' )
		{
			//	CKEDITOR is available, use it's methods
			_body_length = CKEDITOR.instances.post_body.getData().length;
		}
		else
		{
			//	CKEDITOR isn't available, check the value of the textarea
			_body_length = $( 'textarea[name=body]', _form ).val().length;

		}

		if ( ! _body_length )
		{
			_errors++;

			$( '#tabber-body' ).addClass( 'error' );
			$( 'textarea[name=body]', _form ).closest( 'div.field' ).addClass( 'error' );
			$( 'textarea[name=body]', _form ).closest( 'div.field' ).find( 'span.input' ).append( msg.required );

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