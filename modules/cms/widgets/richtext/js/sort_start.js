//	Destroy the instance
var _id = ui.attr('id') + '-ckeditor';
CKEDITOR.instances[_id].destroy();

//	Show the mask
ui.addClass( 'sorting' );
ui.find( '.mask' ).animate({opacity:1},150);