<div class="group-cms blocks create">

	<?=form_open()?>
	
	<p>
		Use this form to create a new CMS block. Blocks can be used within the code using the <code>cms_render_block()</code>
		function made available by the CMS helper. Blocks may also be used within page content by using the appropriate shortcode.
	</p>
	
	<fieldset>
		<legend>Block details</legend>
		<?php
		
		//	Slug
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
		
		//	Description
		$_field					= array();
		$_field['key']			= 'description';
		$_field['label']		= 'Description';
		$_field['default']		= '';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'A description of what this block\'s value should be';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Located
		$_field					= array();
		$_field['key']			= 'located';
		$_field['label']		= 'Located';
		$_field['default']		= '';
		$_field['required']		= TRUE;
		$_field['placeholder']	= 'A brief outline of where this block might be used';
		
		echo form_field( $_field );
		
		// --------------------------------------------------------------------------
		
		//	Block Type
		$_field					= array();
		$_field['key']			= 'type';
		$_field['label']		= 'Block Type';
		$_field['required']		= TRUE;
		
		echo form_field_dropdown( $_field, $block_types );
		
		?>
	</fieldset>
	
	<fieldset id="default-value">

		<?php if ( APP_MULTI_LANG && count( $languages ) > 1 ) : ?>
		<legend><?=APP_DEFAULT_LANG_NAME?> Value</legend>
		<p class="system-alert message no-close">
			<strong>Note:</strong> All blocks must have an <?=APP_DEFAULT_LANG_NAME?> value, define the
			initial <?=APP_DEFAULT_LANG_NAME?> value now.
		</p>
		<?php else : ?>
		<legend>Value</legend>
		<?php endif; ?>
		<?php
		
			//	Value
			echo form_textarea( 'value', set_value( 'value' ), 'placeholder="Define the default value" id="default_value"' );
		
		?>
		<p id="ckeditor-warn" class="system-alert notice no-close" style="margin-top:10px;">
			<strong>Note:</strong> The editor's display might not be a true representation of the final layout
			due to application stylesheets on the front end which are not loaded here.
		</p>
	</fieldset>
	
	<p>
		<?=form_submit( 'submit', lang( 'action_create' ) )?>
	</p>
	
	<?=form_close()?>
</div>

<script style="text/javascript">
<!--//

	$(function(){
	
		var CMS_Blocks = new NAILS_Admin_CMS_Blocks;
		CMS_Blocks.init_create();
	
	});

//-->
</script>