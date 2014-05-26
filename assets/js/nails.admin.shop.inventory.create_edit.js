var NAILS_Admin_Shop_Inventory_Create_Edit;
NAILS_Admin_Shop_Inventory_Create_Edit = function()
{
	this.mode				= null;
	this.product_types		= [];
	this.upload_token		= null;
	this._api				= null;
	this._variation_counter	= 0;


	// --------------------------------------------------------------------------


	this.init = function( mode, product_types, upload_token )
	{
		//	Set vars
		this.mode			= mode;
		this.product_types	= product_types;
		this.upload_token	= upload_token;

		// --------------------------------------------------------------------------

		//	Set up the API interface
		this._api = new window.NAILS_API();

		// --------------------------------------------------------------------------

		//	Init everything!
		this._init_info();
		this._init_variations();
		this._init_gallery();
		this._init_attributes();
		this._init_select2();
		this._init_submit();
	};


	// --------------------------------------------------------------------------


	this._init_info = function()
	{
		//	Show the correct meta field
		this.info_select2_type_change();
	};


	// --------------------------------------------------------------------------


	this.info_select2_type_change = function()
	{
		//	Make sure the appropriate meta fields are being displayed
		var _type_id = parseInt( $('#tab-basics .type_id:input').val(), 10 );

		//	We'll be updating the template, so fetch it now
		var _template = $('<div>').html($.parseHTML($.trim($('#template-variation').html(), null, true)));

		// --------------------------------------------------------------------------

		//	Set meta fields
		$('.meta-fields').hide();
		$('.meta-fields-' + _type_id).show();
		$('.meta-fields', _template).hide();
		$('.meta-fields-' + _type_id, _template).show();

		// --------------------------------------------------------------------------

		//	How many variations does this product type allow
		this._variations_checkmax();

		// --------------------------------------------------------------------------

		//	Does this product type require shipping?
		var _is_physical = false;

		for (var _key in this.product_types)
		{
			if (this.product_types[_key].id === _type_id)
			{
				_is_physical = this.product_types[_key].is_physical;
				break;
			}
		}

		if ( ! _is_physical )
		{
			//	Ensure shipping tabs are hidden and the physical fields are not showing
			$( 'li.tab a.tabber-variation-shipping' ).addClass( 'disabled' ).hide();

			//	If the shipping tab is active, make it non active
			$( 'li.tab a.tabber-variation-shipping' ).each(function()
			{
				var _shipping_tab	= $(this);

				if ( _shipping_tab.parent().hasClass( 'active' ) )
				{
					//	Swap tabs
					var _details_tab	= _shipping_tab.closest( 'ul' ).find( 'a.tabber-variation-details' );

					_shipping_tab.parent().removeClass( 'active' );
					_details_tab.parent().addClass( 'active' );

					//	Swap bodies
					$( '#' + _shipping_tab.data( 'tab' ) ).hide();
					$( '#' + _details_tab.data( 'tab' ) ).show();
				}
			});

			//	Update template
			$( 'li.tab a.tabber-variation-shipping', _template ).addClass( 'disabled' ).hide();

		}
		else
		{
			//	Enable shipping tabs and show physical fields
			$( 'li.tab a.tabber-variation-shipping' ).removeClass( 'disabled' ).css( 'display', 'inline-block' );

			//	Update template
			$( 'li.tab a.tabber-variation-shipping', _template ).removeClass( 'disabled' ).css( 'display', 'inline-block' );
		}

		// --------------------------------------------------------------------------

		//	Finally, replace the content of the template
		$('#template-variation').html($(_template).html());
	};


	// --------------------------------------------------------------------------


	this._init_variations = function()
	{
		var _this = this;

		//	Add a variation
		$('#product-variation-add').on('click', function()
		{
			var _template = $('#template-variation').html().replace('<!--script type="text/javascript"-->', '<script type="text/javascript">').replace('<!--/script-->', '</script>');
			var _counter = $('#product-variations .variation').length;

			_template = Mustache.render(_template, {
				counter: _counter
			});

			$('#product-variations').append(_template);

			_nails.add_stripes();
			_nails.process_prefixed_inputs();
			_nails_admin.init_toggles();
			_this._variations_checkmax();

			//	Show the price syncher
			$( '#variation-sync-prices' ).show();
			$( '#sync-shipping-options' ).show();

			return false;
		});

		// --------------------------------------------------------------------------

		//	Delete variation
		$('#product-variations').on('click', 'a.delete', function()
		{
			var _variation = this;

			$('#dialog-confirm-delete').dialog({
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					"Delete Variation": function()
				{
						//	Remove the variation
						$(_variation).closest('.variation').remove();

						//	Check the max variations
						_this._variations_checkmax();

						//	If there's only one variation left then hide the price syncher
						if ( $( 'product-variations .variation' ).length <= 1 )
						{
							$( '#variation-sync-prices' ).hide();
							$( '#sync-shipping-options' ).hide();
						}

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

		// --------------------------------------------------------------------------

		//	Stock status
		$('#product-variations').on('change', 'select.stock-status', function()
		{
			_this._variation_stock_status_change( $(this) );
		});

		// --------------------------------------------------------------------------

		//	Toggle gallery associations
		$('#product-variations').on('click', 'ul.gallery-associations li:not(.actions)', function(e)
		{
			e.preventDefault();

			var _checked = $('input:checked', this).length ? true : false;
			$('input', this).prop('checked', !_checked);

			if (_checked) {
				$(this).removeClass('selected');
				$('input', this).prop('checked', false);
			} else {
				$(this).addClass('selected');
				$('input', this).prop('checked', true);
			}

			return false;
		});

		// --------------------------------------------------------------------------

		//	Gallery association buttons
		$('#product-variations').on('click', 'ul.gallery-associations a.action', function()
		{
			switch( $(this).data( 'function' ) )
			{
				case 'all' :

					//	Ensure that all of the items are selected
					$(this).closest( 'ul.gallery-associations' ).find( 'li.image:not(.selected)' ).click();

				break;

				// --------------------------------------------------------------------------

				case 'none' :

					$(this).closest( 'ul.gallery-associations' ).find( 'li.image.selected' ).click();

				break;

				// --------------------------------------------------------------------------

				case 'toggle' :

					$(this).closest( 'ul.gallery-associations' ).find( 'li.image' ).click();

				break;
			}

			// --------------------------------------------------------------------------

			return false;
		});

		// --------------------------------------------------------------------------

		//	Switch to Gallery tab (when there are no gallery associations)
		$('#product-variations').on('click', 'ul.gallery-associations.empty li.empty a', function()
		{
			$('#tabber-gallery').click();
			return false;
		});

		// --------------------------------------------------------------------------

		//	Add a shipping option
		$(document).on('click', '#add-shipping-option', function()
		{
			var _template	= $( '#template-shipping-option' ).html();
			var _variation	= $( '#variation-' + $(this).data( 'variation-counter' ) );
			var _counter	= $( 'tr.shipping-option', _variation ).length;

			_template = Mustache.render(_template, {
				counter: _counter+1
			});

			$( 'table.shipping-options tbody', _variation ).append(_template);
			$( 'table.shipping-options', _variation ).removeClass( 'empty' );

			_nails.add_stripes();
			_nails.process_prefixed_inputs();
			_this._init_select2();

			return false;
		});

		// --------------------------------------------------------------------------

		$('#product-variations').on('click', 'a.delete-shipping', function()
		{
			var _option = this;

			$('#dialog-confirm-delete').dialog({
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					"Delete Option": function()
				{
						//	Last one?
						if ( $(_option).closest('table.shipping-options').find( 'tr.shipping-option' ).length === 1 )
						{
							$(_option).closest('table.shipping-options').addClass( 'empty' );
						}

						//	Remove the variation
						$(_option).closest('tr.shipping-option').remove();

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


		// --------------------------------------------------------------------------

		//	Helper: copy the fill price to the sale price on blur
		$( 'input.base-price, input.variation-price' ).on( 'blur', function()
		{
			var _price = $(this).val();
			var _sale_price = $(this).closest( 'tr' ).find( 'input.base-price-sale, input.variation-price' );

			if ( $.trim( _sale_price.val() ).length === 0 )
			{
				_sale_price.val( _price );
			}

		});

		//	Sync prices
		$( '#variation-sync-prices a' ).on( 'click', function()
		{
			$('<div>').html( 'This action will take the values form the first variation and <strong>overwrite</strong> the corresponding values in each other variation.' ).dialog(
			{
				title: 'Confirm Sync',
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					OK: function()
					{
						//	Get base prices
						$( '#variation-0 .base-price' ).each(function()
						{
							var _code = $(this).data( 'code' );

							$( '#product-variations .variation-price.' + _code ).val( $(this).val() );
						});

						$( '#variation-0 .base-price-sale' ).each(function()
						{
							var _code = $(this).data( 'code' );

							$( '#product-variations .variation-price-sale.' + _code ).val( $(this).val() );
						});

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

		// --------------------------------------------------------------------------

		//	Sync shipping options
		$( '#sync-shipping-options' ).on( 'click', function()
		{
			$('<div>').html( 'This action will take the values form the first variation and <strong>overwrite</strong> the corresponding values in each other variation.' ).dialog(
			{
				title: 'Confirm Sync',
				resizable: false,
				draggable: false,
				modal: true,
				dialogClass: "no-close",
				buttons:
				{
					OK: function()
					{
						alert( 'TODO' );

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


	this._variations_checkmax = function()
	{
		var _type_id = $('#tab-basics select[name=type_id]').val();
		var max_variations = 0; //	Unlimited, by default

		for (var _key in this.product_types) {
			if (this.product_types[_key].id === _type_id) {
				max_variations = parseInt(this.product_types[_key].max_variations, 10);
			}
		}

		if (max_variations !== 0 && $('#product-variations .variation').length >= max_variations) {
			$('.add-variation-button').removeClass('enabled').addClass('disabled');

			//	If there are any existing variations which are no longer applicable, mark them so
			$('#product-variations .variation:gt(' + (max_variations - 1) + ')').addClass('not-applicable');
		} else {
			$('.add-variation-button').removeClass('disabled').addClass('enabled');

			//	Remove any variations which were previously disabled
			$('#product-variations .variation.not-applicable').removeClass('not-applicable');
		}
	};


	// --------------------------------------------------------------------------


	this._variation_stock_status_change = function( obj )
	{
		//	Get parent
		var _parent = obj.closest( '.variation' );

		//	Hide all fields
		_parent.find( 'div.stock-status-field' ).hide();

		//	Show the ones we're interested in
		_parent.find( 'div.stock-status-field.' + obj.val() ).show();
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
				'bucket': 'shop-product-images',
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
				$('#product-form').off('submit');
				$('#product-form').on('submit', function() {
					return false;
				});

				var _uploading_string = 'Uploading...';
				var _button_val = $('#product-form input[type=submit]').val();
				window.onbeforeunload = function() {
					return 'Uploads are in progress. Leaving this page will cause them to stop.';
				};

				//	Disable tabs - SWFUpload aborts uploads if it is hidden.
				$('ul.tabs li a').addClass('disabled');
				$('#upload-message').show();

				if (_button_val !== _uploading_string) {
					$('#product-form input[type=submit]').attr({
						'data-old_val': _button_val,
						'disabled': 'disabled'
					}).val('Uploading...');
				}
			},
			'onQueueComplete': function() {
				$('#product-form').off('submit');
				_this._init_submit();

				$('#product-form input[type=submit]').removeAttr('disabled').val($('#product-form input[type=submit]').data('old_val'));
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

		//	Switch to variations tab (when there are no gallery associations)
		$('#tab-gallery').on('click', 'a.switch-to-variations', function()
		{
			$('#tabber-variations').click();

			return false;
		});

		// --------------------------------------------------------------------------

		//	Init sorting
		$('#gallery-items').disableSelection().sortable({
			placeholder: 'gallery-item placeholder',
			items: "li.gallery-item",
			stop: function()
			{
				//	Re-order the variation gallery tabs

				//	Get the ID's, in order
				var _ids = [];
				$('#gallery-items li.gallery-item:not(.deleted,.crunching) input[type=hidden]').each(function() {
					_ids.push(parseInt($(this).val(), 10));
				});

				//	Now we know the order of ID's we need to shuffle the order of the variations' gallery
				//	We're gonna do this by reversing the array then cloning the element, removing the original
				//	and then reinserting it at as the first element. The next element will go above it and so forth.

				//	Parse the template so we can adjust it
				var _template = $('<div>').html($.parseHTML($.trim($('#template-variation').html(), null, true)));

				$.each(_ids, function(index, id)
				{
					//	Find the appropriate item(s) and move it to the beginning of the list
					$('li.image.object-id-' + id).each(function()
					{
						var _parent = $(this).closest('ul.gallery-associations');
						$(this).clone().appendTo(_parent);
						$(this).remove();
					});

					//	Manipulate the template
					$('li.image.object-id-' + id, _template).each(function()
					{
						var _parent = $(this).closest('ul.gallery-associations');
						$(this).clone().appendTo(_parent);
						$(this).remove();
					});
				});

				//	Move the actions back to the end
				$( 'ul.gallery-associations li.actions' ).each(function()
				{
					var _parent = $(this).closest('ul.gallery-associations');
					$(this).clone().appendTo(_parent);
					$(this).remove();
				});

				$( 'ul.gallery-associations li.actions', _template ).each(function()
				{
					var _parent = $(this).closest('ul.gallery-associations');
					$(this).clone().appendTo(_parent);
					$(this).remove();
				});

				//	Replace the template
				$('#template-variation').html($(_template).html());
			}
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


	this._init_attributes = function()
	{
		$('#product-attribute-add').on('click', function()
		{
			var _template = $('#template-attribute').html();
			var _counter = $('#product-attributes tr.attribute').length;

			_template = Mustache.render(_template,
			{
				counter: _counter
			});

			$('#product-attributes').append(_template);
			$('#product-attributes tr:last-child select.attributes').select2();

			return false;
		});

		// --------------------------------------------------------------------------

		//	Delete attribute
		$('#product-attributes').on('click', 'a.delete', function()
		{
			$(this).closest('tr.attribute').remove();

			return false;
		});
	};


	// --------------------------------------------------------------------------


	this._init_select2 = function()
	{
		var _this = this;

		// --------------------------------------------------------------------------

		//	Define targets and URLs
		var _target	= {};
		var _url	= {};

		_target.types			= '#tab-basics select.type_id';
		_url.types				= window.SITE_URL + 'admin/shop/manage/types?is_fancybox=1';

		_target.brands			= '#tab-basics select.brands';
		_url.brands				= window.SITE_URL + 'admin/shop/manage/brands?is_fancybox=1';

		_target.categories		= '#tab-basics select.categories';
		_url.categories			= window.SITE_URL + 'admin/shop/manage/categories?is_fancybox=1';

		_target.tags			= '#tab-basics select.tags';
		_url.tags				= window.SITE_URL + 'admin/shop/manage/tags?is_fancybox=1';

		_target.tax_rates		= '#tab-basics select.tax_rate_id';
		_url.tax_rates			= window.SITE_URL + 'admin/shop/manage/tax_rates?is_fancybox=1';

		_target.attributes		= '#tab-attributes select.attributes';
		_url.attributes			= window.SITE_URL + 'admin/shop/manage/attributes?is_fancybox=1';

		_target.ranges			= '#tab-ranges-collections select.ranges';
		_url.ranges				= window.SITE_URL + 'admin/shop/manage/ranges?is_fancybox=1';

		_target.collections		= '#tab-ranges-collections select.collections';
		_url.collections		= window.SITE_URL + 'admin/shop/manage/collections?is_fancybox=1';

		_target.courier_method	= '#tab-variations select.shipping_methods';
		_url.courier_method		= window.SITE_URL + 'admin/shop/manage/shipping_methods?is_fancybox=1';

		// --------------------------------------------------------------------------

		//	Bind to select2 changes
		$(_target.types).change(function() {
			_this.info_select2_type_change();
		});

		// --------------------------------------------------------------------------

		//	Bind fancybox to select2's
		$(document).on('click', 'a.manage-types', function() {
			$.fancybox.open(_url.types, {
				type: 'iframe',
				width: '95%',
				height: '95%',
				beforeClose: function() {
					_this._rebuild_select2(_target.types);
				}
			});
			return false;
		});

		$(document).on('click', 'a.manage-brands', function() {
			$.fancybox.open(_url.brands, {
				type: 'iframe',
				width: '95%',
				height: '95%',
				beforeClose: function() {
					_this._rebuild_select2(_target.brands);
				}
			});
			return false;
		});

		$(document).on('click', 'a.manage-categories', function() {
			$.fancybox.open(_url.categories, {
				type: 'iframe',
				width: '95%',
				height: '95%',
				beforeClose: function() {
					_this._rebuild_select2(_target.categories);
				}
			});
			return false;
		});

		$(document).on('click', 'a.manage-tags', function() {
			$.fancybox.open(_url.tags, {
				type: 'iframe',
				width: '95%',
				height: '95%',
				beforeClose: function() {
					_this._rebuild_select2(_target.tags);
				}
			});
			return false;
		});

		$(document).on('click', 'a.manage-tax-rates', function() {
			$.fancybox.open(_url.tax_rates, {
				type: 'iframe',
				width: '95%',
				height: '95%',
				beforeClose: function() {
					_this._rebuild_select2(_target.tax_rates);
				}
			});
			return false;
		});

		$(document).on('click', 'a.manage-attributes', function() {
			$.fancybox.open(_url.attributes, {
				type: 'iframe',
				width: '95%',
				height: '95%',
				beforeClose: function() {
					_this._rebuild_select2(_target.attributes, 'template-attribute');
				}
			});
			return false;
		});

		$(document).on('click', 'a.manage-ranges', function() {
			$.fancybox.open(_url.ranges, {
				type: 'iframe',
				width: '95%',
				height: '95%',
				beforeClose: function() {
					_this._rebuild_select2(_target.ranges);
				}
			});
			return false;
		});

		$(document).on('click', 'a.manage-collections', function() {
			$.fancybox.open(_url.collections, {
				type: 'iframe',
				width: '95%',
				height: '95%',
				beforeClose: function() {
					_this._rebuild_select2(_target.collections);
				}
			});
			return false;
		});

		// --------------------------------------------------------------------------

		//	Ensure all select2's are updated when their tab is shown
		$( 'ul.tabs a:not(.disabled)' ).on( 'click', function()
		{
			setTimeout(function()
			{
				$(_target.types).trigger( 'change' );
				$(_target.brands).trigger( 'change' );
				$(_target.categories).trigger( 'change' );
				$(_target.tags).trigger( 'change' );
				$(_target.tax_rates).trigger( 'change' );
				$(_target.attributes).trigger( 'change' );
				$(_target.ranges).trigger( 'change' );
				$(_target.collections).trigger( 'change' );
			}, 1);
		});
	};


	// --------------------------------------------------------------------------


	this._rebuild_select2 = function(target, template)
	{
		var _DATA = $('.fancybox-iframe').get(0).contentWindow._DATA;

		if (typeof(_DATA) === 'undefined')
		{
			//	Do nothing, nothing to work with
			return false;
		}

		//	Fetch the target(s)
		var _targets = $(target);

		if (!_targets.length)
		{
			//	Target doesn't exist, ignore
			return false;
		}

		//	Rebuild the target(s)
		_targets.each(function()
		{
			//	Save a reference to the target
			var _target = this;

			//	Get the currently selected items in this select
			//	and store as an array of ID's

			var _selected = [];
			$('option:selected', this).each(function()
			{
				_selected.push(parseInt($(this).val(), 10));
			});

			//	Rebuild, marking as selected where appropriate
			$(this).empty();
			$.each(_DATA, function()
			{
				var _option = $('<option>');

				_option.val(this.id);
				_option.html(this.label);

				if ($.inArray(this.id, _selected) > -1)
				{
					_option.prop('selected', true);
				}

				$(_target).append(_option);
			});

			//	Trigger the select2
			$(this).trigger('change');
		});

		//	Rebuild template
		if ( template )
		{
			var _template = $('<div>').html($.parseHTML($.trim($('#' + template).html(), null, true)));

			var _target = $( 'select', _template ).empty();

			$.each(_DATA, function()
			{
				var _option = $('<option>');

				_option.val(this.id);
				_option.html(this.label);

				$(_target).append(_option);
			});

			$('#' + template).html($(_template).html());
		}
	};


	// --------------------------------------------------------------------------


	this._init_submit = function()
	{
		var _this = this;

		$('#product-form').on('submit', function() {
			return _this._submit();
		});
	};


	// --------------------------------------------------------------------------


	this._submit = function()
	{
		//	Fetch the product and analyse the object tell the user what they did wrong
		//	remember to highlight tabs etc

		//	TODO

		// --------------------------------------------------------------------------

		//	Got here? All is good!
		return true;
	};
};