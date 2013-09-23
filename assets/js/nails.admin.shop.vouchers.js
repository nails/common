var NAILS_Admin_Shop_Vouchers;
NAILS_Admin_Shop_Vouchers = function()
{
	this.init_create = function()
	{
		var _this = this;

		// --------------------------------------------------------------------------

		//	Visible chosens
		$( 'select.chosen:visible' ).chosen();

		// --------------------------------------------------------------------------

		//	Bind listeners
		$( 'select[name=type]' ).on( 'change', function()
		{
			_this._handle_type_change();
		});

		$( 'select[name=discount_application]' ).on( 'change', function()
		{
			_this._handle_application_change();
		});

		$( '#generate-code' ).on( 'click', function()
		{
			return _this._generate_code();
		});

		// --------------------------------------------------------------------------

		//	Run the handlers, so the form is rendered appropriately
		this._handle_application_change();
		this._handle_type_change();

		// --------------------------------------------------------------------------

		//	Instanciate the datepickers
		$( '.datetime1' ).datetimepicker(
		{
			dateFormat	: 'yy-mm-dd',
			timeFormat	: 'HH:mm:ss',
			beforeShow		: function()
			{
				$( '.datetime1' ).datetimepicker( 'option', "maxDate", $( '.datetime2' ).datetimepicker( 'getDate' ) );
			},
			onSelect	: function()
			{
				$( '.datetime2' ).datetimepicker( 'option', "minDate", $( '.datetime1' ).datetimepicker( 'getDate' ) );
			}
		});

		var _today = new Date();

		$( '.datetime2' ).datetimepicker(
		{
			dateFormat	: 'yy-mm-dd',
			timeFormat	: 'HH:mm:ss',
			minDate		: _today,
			beforeShow		: function()
			{
				$( '.datetime2' ).datetimepicker( 'option', "minDate", $( '.datetime1' ).datetimepicker( 'getDate' ) );
			},
			onSelect	: function()
			{
				$( '.datetime1' ).datetimepicker( 'option', "maxDate", $( '.datetime2' ).datetimepicker( 'getDate' ) );
			}
		});
	};


	// --------------------------------------------------------------------------


	this._handle_type_change = function()
	{
		switch( $( 'select[name=type]' ).val() )
		{
			case 'NORMAL' :

				$( '#type-limited' ).hide();

				// --------------------------------------------------------------------------

				$( 'select[name=discount_type]' ).removeAttr( 'disabled' );
				$( 'select[name=discount_type]' ).trigger('liszt:updated');

				$( 'select[name=discount_application]' ).removeAttr( 'disabled' );
				$( 'select[name=discount_application]' ).trigger('liszt:updated');

			break;

			case 'LIMITED_USE' :

				$( '#type-limited' ).show();

				// --------------------------------------------------------------------------

				$( 'select[name=discount_type]' ).removeAttr( 'disabled' );
				$( 'select[name=discount_type]' ).trigger('liszt:updated');

				$( 'select[name=discount_application]' ).removeAttr( 'disabled' );
				$( 'select[name=discount_application]' ).trigger('liszt:updated');

			break;

			case 'GIFT_CARD' :

				$( '#type-limited' ).hide();
				$( '#application-product_types' ).hide();

				// --------------------------------------------------------------------------

				//	Set the discount type to amount and readonly-ify
				$( 'select[name=discount_type] option[selected=selected]' ).attr( 'selected', '' );
				$( 'select[name=discount_type] option[value=AMOUNT]' ).attr( 'selected', 'selected' );
				$( 'select[name=discount_type]' ).attr( 'disabled', 'disabled' );
				$( 'select[name=discount_type]' ).trigger('liszt:updated');

				$( 'select[name=discount_application] option[selected=selected]' ).attr( 'selected', '' );
				$( 'select[name=discount_application] option[value=ALL]' ).attr( 'selected', 'selected' );
				$( 'select[name=discount_application]' ).attr( 'disabled', 'disabled' );
				$( 'select[name=discount_application]' ).trigger('liszt:updated');

			break;
		}

		// --------------------------------------------------------------------------

		//	Test if any of the extended views are visible, if so, hide the message
		if ( $( '#type-limited:visible, #type-gift_card:visible, #application-product_types:visible' ).length )
		{
			$( '#no-extended-data' ).hide();
		}
		else
		{
			$( '#no-extended-data' ).show();
		}
	};


	// --------------------------------------------------------------------------


	this._handle_application_change = function()
	{
		switch( $( 'select[name=discount_application]' ).val() )
		{
			case 'PRODUCTS' :
			case 'SHIPPING' :
			case 'ALL' :

				$( '#application-product_types' ).hide();

			break;

			case 'PRODUCT_TYPES' :

				$( '#application-product_types' ).show();
				$( 'select.chosen:not(.chosen-done)' ).chosen();

			break;
		}

		// --------------------------------------------------------------------------

		//	Test if any of the extended views are visible, if so, hide the message
		if ( $( '#type-limited:visible, #type-gift_card:visible, #application-product_types:visible' ).length )
		{
			$( '#no-extended-data' ).hide();
		}
		else
		{
			$( '#no-extended-data' ).show();
		}
	};


	// --------------------------------------------------------------------------


	this._generate_code = function()
	{
		var code = "";
		var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		for( var i=0; i < 10; i++ )
		{
			code += possible.charAt(Math.floor(Math.random() * possible.length));
		}

		$( 'input[name=code]' ).val( code );

		return false;
	};
};