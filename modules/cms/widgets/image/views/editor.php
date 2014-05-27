<div class="fieldset">
	<?php

		$_field				= array();
		$_field['key']		= 'image_id';
		$_field['label']	= 'Image';
		$_field['default']	= isset( ${$_field['key']} ) ? ${$_field['key']} : '';
		$_field['bucket']	= 'cms-widget-image';

		echo form_field_mm_image( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= 'scaling';
		$_field['label']	= 'Scaling';
		$_field['class']	= 'select2';
		$_field['default']	= isset( ${$_field['key']} ) ? ${$_field['key']} : '';

		$_options = array(
			'NONE'	=> 'None, show fullsize',
			'CROP'	=> 'Crop to size',
			'SCALE'	=> 'Fit within boundary'
		);

		echo form_field_dropdown( $_field, $_options );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= 'width';
		$_field['label']		= 'Width';
		$_field['default']		= isset( ${$_field['key']} ) ? ${$_field['key']} : '';
		$_field['placeholder']	= 'The maximum width of the image, in pixels.';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= 'height';
		$_field['label']		= 'Height';
		$_field['default']		= isset( ${$_field['key']} ) ? ${$_field['key']} : '';
		$_field['placeholder']	= 'The maximum height of the image, in pixels.';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= 'linking';
		$_field['label']	= 'Linking';
		$_field['class']	= 'select2';
		$_field['default']	= isset( ${$_field['key']} ) ? ${$_field['key']} : '';

		$_options = array(
			'NONE'		=> 'Do not link',
			'FULLSIZE'	=> 'Link to fullsize',
			'CUSTOM'	=> 'Custom URL'
		);

		echo form_field_dropdown( $_field, $_options );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= 'url';
		$_field['label']		= 'URL';
		$_field['default']		= isset( ${$_field['key']} ) ? ${$_field['key']} : '';
		$_field['placeholder']	= 'http://www.example.com';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field				= array();
		$_field['key']		= 'target';
		$_field['label']	= 'Target';
		$_field['class']	= 'select2';
		$_field['default']	= isset( ${$_field['key']} ) ? ${$_field['key']} : '';

		$_options = array(
			''			=> 'None',
			'_blank'	=> 'New window/tab',
			'_parent'	=> 'Parent window/tab'
		);

		echo form_field_dropdown( $_field, $_options );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= 'img_attr';
		$_field['label']		= 'Attributes';
		$_field['default']		= isset( ${$_field['key']} ) ? ${$_field['key']} : '';
		$_field['placeholder']	= 'Any additional attributes to include in the image tag.';

		echo form_field( $_field );

		// --------------------------------------------------------------------------

		$_field					= array();
		$_field['key']			= 'link_attr';
		$_field['label']		= 'Link Attributes';
		$_field['default']		= isset( ${$_field['key']} ) ? ${$_field['key']} : '';
		$_field['placeholder']	= 'Any additional attributes to include in the link tag.';

		echo form_field( $_field );
	?>
</div>