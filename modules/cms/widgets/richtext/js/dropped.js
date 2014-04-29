//	Setup the textarea
var _textarea	= ui.find('textarea');
var _id			= ui.attr( 'id' ) + '-ckeditor';
_textarea.attr( 'id', _id );

//	Instantiate the editor
_textarea.ckeditor(
{
	customConfig: window.NAILS.URL + 'js/libraries/ckeditor/ckeditor.config.min.js'
},
function()
{
	// Increase the height of the container
	var _header_height	= ui.find('.header-bar').outerHeight();
	var _editor_height	= ui.find('.editor').outerHeight();
	var _height			= _header_height + _editor_height;

	ui.stop().animate({height:_height}, 250);
});

//	Bind event listener
CKEDITOR.instances[_id].on( 'autoGrow', function(e)
{
	//	Calculate how much the editor has grown or shurnk by
	var _difference		= e.data.newHeight - e.data.currentHeight;
	var _header_height	= ui.find('.header-bar').outerHeight();
	var _editor_height	= ui.find('.editor').outerHeight();
	var _height			= _header_height + _editor_height + _difference;

	ui.css({height:_height});
});