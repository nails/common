<div class="group-cms blocks create">

	<?=form_open()?>
	
	<p>
		Use this form to create a new CMS block. Blocks can be used within the code using the <code>cms_render_block()</code>
		function made available by the CMS helper. Blocks may also be used within page content by using the appropriate shortcode.
	</p>
	
	<fieldset>
		<legend>Block details</legend>
		<?php
		
		//	Title
		$_field					= array();
		$_field['key']			= 'slug';
		$_field['label']		= 'Slug';
		$_field['default']		= '';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The block\'s unique slug';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Title
		$_field					= array();
		$_field['key']			= 'title';
		$_field['label']		= 'Title';
		$_field['default']		= '';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The Human friendly block title';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Title
		$_field					= array();
		$_field['key']			= 'description';
		$_field['label']		= 'Description';
		$_field['default']		= '';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'A description of what this block\'s value should be';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Title
		$_field					= array();
		$_field['key']			= 'located';
		$_field['label']		= 'Located';
		$_field['default']		= '';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'A brief outline of where this block might be used';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		?>
	</fieldset>
	
	<p>
		All blocks must have an <?=APP_DEFAULT_LANG_NAME?> value, define the initial <?=APP_DEFAULT_LANG_NAME?> value now.
	</p>
	
	<fieldset>
		<legend><?=APP_DEFAULT_LANG_NAME?> Value</legend>
		<?php
		
		//	Title
		$_field					= array();
		$_field['key']			= 'value';
		$_field['type']			= 'textarea';
		$_field['label']		= 'Value';
		$_field['default']		= '';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'The initial ' . APP_DEFAULT_LANG_NAME . ' value';
		
		echo form_field( $_field );
		
		?>
	</fieldset>
	
	<p>
		<?=form_submit( 'submit', lang( 'action_create' ) )?>
	</p>
	
	<?=form_close()?>
</div>