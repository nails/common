//	Destroy the instance
var _id = ui.attr('id') + '-ckeditor';
CKEDITOR.instances[_id].destroy();

//	Show the mask
ui.addClass( 'sorting' );
ui.find( '.editor-sorting' ).animate({opacity:1},150);