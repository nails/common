var NAILS_Admin_CMS_pages_Create_Edit;
NAILS_Admin_CMS_pages_Create_Edit = function()
{
	this.editor_id = Math.floor(Math.random() * 10000000000000001);

	// --------------------------------------------------------------------------


	this.init = function()
	{
		this._template_chooser_init();
		this._editor_init();
		console.log(this.editor_id);
	};


	// --------------------------------------------------------------------------


	this._template_chooser_init = function()
	{
		$( 'label.template' ).on( 'click', function()
		{
			//	Set the correct label
			$( 'label.template' ).removeClass( 'selected' );
			$(this).addClass( 'selected' );

			//	Show the correct buttons
			var _template = $(this).data( 'template-slug' );
			$( 'a.launch-editor' ).hide();
			$( 'a.launch-editor.template-' + _template ).show();
		});
	};


	// --------------------------------------------------------------------------


	this._editor_init = function()
	{
		var _this = this;

		//	Generate the editor HTML and inject into DOM
		var _container	= $('<div>').attr( 'id', this.editor_id ).addClass( 'group-cms pages widgeteditor' );
		var _header		= $( '<ul>' ).attr( 'id', this.editor_id + '-header' ).addClass( 'header' ).html( '<li>Header</li>' );
		var _widgets	= $( '<ul>' ).attr( 'id', this.editor_id + '-widgets' ).addClass( 'widgets' ).html( '<li>Widgets</li>' );
		var _dropzone	= $( '<ul>' ).attr('id',  this.editor_id + '-dropzone' ).addClass( 'dropzone' ).html( 'dropzone' );

		_container.append( _header );
		_container.append( _widgets );
		_container.append( _dropzone );

		$( 'body' ).append( _container );

		//	TODO: Set up editor environment's HTML/templates
		//	Draggables
		//	Widget areas
		//	etc

		// --------------------------------------------------------------------------

		//	Bind to launchers
		$( 'a.launch-editor' ).on( 'click', function()
		{
			var _template	= $(this).data( 'template' );
			var _area		= $(this).data( 'area' );

			_this._editor_launch( _template, _area );

			return false;
		});
	};


	// --------------------------------------------------------------------------


	this._editor_launch = function( template, area )
	{
		$( '#' + this.editor_id ).fadeIn( 'fast' );

		var _this = this;

		// --------------------------------------------------------------------------

		//	TODO: Set up the editor environment
		//	Disable any draggables
		//	Render any existing widgets (from the DB, POST data or in memory)

		// --------------------------------------------------------------------------

		//	Bind keyUp event for the escape key
		$(document).on( 'keyup', function( e )
		{
			if ( e.keyCode === 27 )
			{
				_this._editor_close();
			}
		});
	};


	// --------------------------------------------------------------------------


	this._editor_close = function()
	{
		$( '#' + this.editor_id ).fadeOut( 'fast' );
	};
};