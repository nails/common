var NAILS_Admin_CMS_Blocks;
NAILS_Admin_CMS_Blocks = function()
{
	this.new_counter	= 0;			//	Counter holds the number of new translation being added on a page
	this.block_type		= 'plaintext';	//	When editing, this field contains the type of block we're editing.

	// --------------------------------------------------------------------------


	this.init_search = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( '.search-text input' ).on( 'keyup', function() { _this._do_search( $(this).val() ); return false; } );
	};


	// --------------------------------------------------------------------------


	this._do_search = function( term )
	{
		$( 'tr.block' ).each(function()
		{
			var regex = new RegExp( term, 'gi' );

			if ( regex.test( $(this).attr( 'data-title' ) ) )
			{
				$(this).show();
			}
			else
			{
				$(this).hide();
			}
		});
	};


	// --------------------------------------------------------------------------

	/* ! CREATE */

	// --------------------------------------------------------------------------


	this.init_create = function()
	{
		this._init_type_change();
	};


	// --------------------------------------------------------------------------


	this._init_type_change = function()
	{
		$( 'select[name=type]' ).on( 'change', $.proxy( function() { this._type_changed(); }, this ) );
		this._type_changed();
	};


	// --------------------------------------------------------------------------


	this._type_changed = function()
	{
		var _type = $( 'select[name=type]' ).val();

		switch( _type )
		{
			case 'plaintext' :

				//	Destroy the rich text editor instance and hide the WYSIWYG warning
				$( '#default-value' ).show();
				$( '#ckeditor-warn' ).hide();

				if ( typeof( CKEDITOR.instances.default_value ) !== 'undefined' )
				{
					CKEDITOR.instances.default_value.destroy();
				}

			break;

			// --------------------------------------------------------------------------

			case 'richtext' :

				//	Instanciate a CKEditor instance and show the WYSIWYG warning
				$( '#default-value' ).show();
				$( '#ckeditor-warn' ).show();
				CKEDITOR.replace( 'default_value', { 'customConfig' : window.NAILS.URL + 'js/libraries/ckeditor/ckeditor.config.min.js' } );

			break;

			// --------------------------------------------------------------------------

			default :

				//	Destroy the instance, hide the warning and hide the fieldset
				$( '#default-value' ).hide();
				$( '#ckeditor-warn' ).hide();

				if ( typeof( CKEDITOR.instances.default_value ) !== 'undefined' )
				{
					CKEDITOR.instances.default_value.destroy();
				}

			break;
		}

		// --------------------------------------------------------------------------

		//	Show the input field
	};


	// --------------------------------------------------------------------------

	/* ! EDIT */

	// --------------------------------------------------------------------------


	this.init_edit = function( block_type )
	{
		this.block_type = block_type;

		// --------------------------------------------------------------------------

		this._init_new_translation();
		this._init_del_translation();
		this._init_toggle_revision();
		this._init_form_validation();

		// --------------------------------------------------------------------------

		//	Bind events
		var _this = this;	/*	Ugly Scope Hack	*/
		$( document ).on( 'change', '.translation select', function() { _this._lang_change( $(this) ); return false; } );
	};


	// --------------------------------------------------------------------------


	this._init_new_translation = function()
	{
		$( '#new-translation' ).on( 'click', $.proxy( function() { this._add_translation(); return false; }, this ) );
	};


	// --------------------------------------------------------------------------


	this._add_translation = function()
	{
		var _data		= { new_count : this.new_counter };
		var _template	= $( '#template-translation' ).html();
		var _fieldset	= $( '<fieldset>' ).addClass( 'translation' );

		_fieldset.html( Mustache.render( _template, _data ) );

		//	Add a 'please choose' option
		_fieldset
			.attr( 'data-translation_id', this.new_counter )
			.find( 'select' )
			.prepend( $( '<option>' )
			.attr( { 'value' : '', 'selected' : 'selected', 'class' : 'please-choose', 'disabled' : 'disabled' } )
			.text( 'Please Choose Language...' ) );

		$( '.translation' ).last().after( _fieldset );

		this._lang_disable();

		// --------------------------------------------------------------------------

		//	If editing a richtext field we need to instantiate the new editor
		if ( this.block_type === 'richtext' )
		{
			CKEDITOR.replace( 'translation_' + this.new_counter, { 'customConfig' : window.NAILS.URL + 'js/libraries/ckeditor/ckeditor.config.min.js' } );
		}

		// --------------------------------------------------------------------------

		this.new_counter++;
	};


	// --------------------------------------------------------------------------


	this._init_del_translation = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( document ).on( 'click', 'a.remove-translation', function() { _this._del_translation( $(this) ); return false; } );
	};


	// --------------------------------------------------------------------------


	this._del_translation = function( obj )
	{
		//	If editing a richtext field we need to destory the editor, free up some memory, innit.
		if ( this.block_type === 'richtext' )
		{
			var _id = obj.closest( 'fieldset' ).attr( 'data-translation_id' );
			_id = 'translation_' + _id;

			CKEDITOR.instances[_id].destroy();
		}

		// --------------------------------------------------------------------------

		obj.closest( 'fieldset' ).remove();
	};


	// --------------------------------------------------------------------------


	this._lang_change = function( obj )
	{
		obj.closest( 'fieldset' ).attr( 'data-lang_id', obj.val() );
		this._lang_disable();
	};


	// --------------------------------------------------------------------------


	this._lang_disable = function()
	{
		//	Enable all options
		$( '.translation option[disabled=disabled]:not(.please-choose)' ).removeAttr( 'disabled' );

		// --------------------------------------------------------------------------

		//	Disable options which have been used
		$( '.translation' ).each( function() {
			$( '.translation option[value=' + $(this).attr( 'data-lang_id' ) + ']' ).attr( 'disabled', 'disabled' );

			//	Re-enable for the selected item in this <select>
			$( 'option[value=' + $(this).attr( 'data-lang_id' ) + ']', this ).removeAttr( 'disabled' );
		});

		// --------------------------------------------------------------------------

		//	Remove the disabled attribute on the currently selected item (so it POSTS)
		$( '.translation option[selected=selected]:not(.please-choose)' ).removeAttr( 'disabled' );
	};


	// --------------------------------------------------------------------------


	this._init_toggle_revision = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( 'a.toggle-revisions' ).on( 'click', function() { _this._toggle_revision( $(this) ); return false; } );
	};


	// --------------------------------------------------------------------------


	this._toggle_revision = function( obj )
	{
		obj.closest( '.revisions' ).toggleClass( 'show' );
	};


	// --------------------------------------------------------------------------


	this._init_form_validation = function()
	{
		var _this = this;	/*	Ugly Scope Hack	*/
		$( 'form' ).on( 'submit', function() { return _this._form_validation(); } );
	};


	// --------------------------------------------------------------------------


	this._form_validation = function()
	{
		var _errors = 0;
		$( 'fieldset.translation' ).each( function() {

			//	Check select has a value
			var _select;
			if ( $( 'select', this ).length )
			{
				_select = $( 'select', this ).val();
			}
			else
			{
				_select = true;
			}

			//	Check textarea isn't empty
			var _textarea = $( 'textarea', this ).val();

			if ( ! _select || !_textarea )
			{
				_errors++;
				$( '.system-alert', this ).show();
			}
			else
			{
				$( '.system-alert', this ).hide();
			}

		});

		// --------------------------------------------------------------------------

		if ( _errors )
		{
			//	Scroll to the nearest error
			var _system = $( 'div.system-alert.error:visible' ).get(0);
			$.scrollTo( _system, 'fast', { axis: 'y', offset : { top: -50 } } );

			// --------------------------------------------------------------------------

			return false;
		}
		else
		{
			return true;
		}
	};
};