/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */
 
CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	config.extraPlugins = 'mediaembed,autogrow,codemirror';
	config.removePlugins = 'resize,elementspath';
	
	// The toolbar groups arrangement, optimized for a single toolbar row.
	config.toolbarGroups = [
		{ name: 'document',    groups: [ 'mode' ] },
		{ name: 'styles' },
		{ name: 'basicstyles', groups: [ 'basicstyles' ] },
		{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
		{ name: 'links' },
		{ name: 'insert',      groups: [ 'mediaembed' ] },
		{ name: 'tools' },
		{ name: 'others' }
	];

	// The default plugins included in the basic setup define some buttons that
	// we don't want too have in a basic editor. We remove them here.
	config.removeButtons = 'Save,NewPage,Preview,Print,Anchor,Strike,Subscript,Superscript,CreateDiv,Flash,Smiley,HorizontalRule,PageBreak,SpecialChar,Styles,Font,FontSize';

	//	Only allow certain formatting
	config.format_small = { element : 'small', name : 'Small' };
	config.format_tags = 'p;small;h1;h2;h3;h4;h5';

	// Considering that the basic setup doesn't provide pasting cleanup features,
	// it's recommended to force everything to be plain text.
	config.forcePasteAsPlainText = true;
	
	//	KCFinder
	//	Disabled for now until I can work out how to tell KCFinder to look at the current app's
	//	uplaod directory and not it's own one.
	
	config.filebrowserImageBrowseUrl	= SITE_URL + 'cdn/manager/browse/image';
	config.filebrowserFlashBrowseUrl	= SITE_URL + 'cdn/manager/browse/flash';
	config.filebrowserBrowseUrl			= SITE_URL + 'cdn/manager/browse/file';
};
