//	Shortcuts
var _this	= this;
var _width	= $( 'input[name=width]', ui ).closest( '.field' );
var _height	= $( 'input[name=height]', ui ).closest( '.field' );
var _url	= $( 'input[name=url]', ui ).closest( '.field' );
var _target	= $( 'select[name=target]', ui ).closest( '.field' );
var _a_attr	= $( 'input[name=link_attr]', ui ).closest( '.field' );

//	Functions
var _scaling_change = function( value )
{
	switch ( value )
	{
		case 'CROP' :
		case 'SCALE' :

			_width.show();
			_height.show();

		break;
		default :

			_width.hide();
			_height.hide();

		break;
	}

	if ( typeof( _nails.add_stripes ) === 'function' )
	{
		_nails.add_stripes();
	}

	_this.resize_widget(ui);
};

var _linking_change = function( value )
{
	switch ( value )
	{
		case 'FULLSIZE' :

			_url.hide();
			_target.show().trigger( 'change' );
			_a_attr.show();

		break;
		case 'CUSTOM' :

			_url.show();
			_target.show().trigger( 'change' );
			_a_attr.show();

		break;
		default :

			_url.hide();
			_target.hide();
			_a_attr.hide();

		break;
	}

	if ( typeof( _nails.add_stripes ) === 'function' )
	{
		_nails.add_stripes();
	}

	_this.resize_widget(ui);
};


//	Initially, hide all the options
_width.hide();
_height.hide();
_url.hide();
_target.hide();
_a_attr.hide();

if ( typeof( _nails.add_stripes ) === 'function' )
{
	_nails.add_stripes();
}

// --------------------------------------------------------------------------

//	Bind scale select
$( 'select[name=scaling]', ui ).on( 'change', function()
{
	_scaling_change( $(this).val() );
});

//	Bind link select
$( 'select[name=linking]', ui ).on( 'change', function()
{
	_linking_change( $(this).val() );
});

// --------------------------------------------------------------------------

//	Set initial state
_scaling_change( $( 'select[name=scaling]', ui ).val() );
_linking_change( $( 'select[name=linking]', ui ).val() );