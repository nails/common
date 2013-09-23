var NAILS_Shop_Basket;
NAILS_Shop_Basket = function()
{
	this.init = function()
	{
		this._init_currency_chooser();
		this._init_shipping_chooser();
	};


	// --------------------------------------------------------------------------


	this._init_currency_chooser = function()
	{
		$( '#currency-chooser' ).on( 'change', function()
		{
			$(this).closest( '.currency-chooser' ).addClass( 'working' );
			$(this).closest( 'form' ).submit();
		});
	};


	// --------------------------------------------------------------------------


	this._init_shipping_chooser = function()
	{
		$( '#shipping-chooser' ).on( 'change', function()
		{
			$(this).closest( '.shipping-chooser' ).addClass( 'working' );
			$(this).closest( 'form' ).submit();
		});
	};
};