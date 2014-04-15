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

	//	Allow the editor to define whatever classes the user wants on elements
	config.extraAllowedContent = { '*' : { classes: '*' } };

	// Considering that the basic setup doesn't provide pasting cleanup features,
	// it's recommended to force everything to be plain text.
	config.forcePasteAsPlainText = true;

	//	CDN
	config.filebrowserImageBrowseUrl	= window.SITE_URL + 'cdn/manager/browse/image';
	config.filebrowserFlashBrowseUrl	= window.SITE_URL + 'cdn/manager/browse/flash';
	config.filebrowserBrowseUrl			= window.SITE_URL + 'cdn/manager/browse/file';

	//	Dialog colour; tie it in with the rest of admin
	config.dialog_backgroundCoverColor		= 'rgb(0,0,0)';
	config.dialog_backgroundCoverOpacity	= 0.75;
};
