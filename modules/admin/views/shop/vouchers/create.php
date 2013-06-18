<div class="group-shop vouchers create">
	<p>
		Use the following form to create a new voucher for use in the shop.
	</p>
	<?=form_open()?>
	<fieldset id="create-voucher-basic">
		<legend>Basic Information</legend>
		<?php

			//	Voucher type
			$_field				= array();
			$_field['key']		= 'type';
			$_field['label']	= 'Type';
			$_field['class']	= 'chosen';
			$_field['required']	= TRUE;

			$_options = array(
				'NORMAL'		=> 'Normal',
				'LIMITED_USE'	=> 'Limited use',
				'GIFT_CARD'		=> 'Gift Card'
			);
			
			echo form_field_dropdown( $_field, $_options );

			// --------------------------------------------------------------------------

			//	Code
			$_field					= array();
			$_field['key']			= 'code';
			$_field['label']		= 'Code';
			$_field['sub_label']	= '<a href="#" id="generate-code">Generate Valid Code</a>';
			$_field['placeholder']	= 'Define the code for this voucher or generate one using the link on the left.';
			$_field['required']		= TRUE;
			
			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Label
			$_field					= array();
			$_field['key']			= 'label';
			$_field['label']		= 'Label/Description';
			$_field['placeholder']	= 'The label is shown to the user when the voucher is applied.';
			$_field['required']		= TRUE;
			
			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Discount type
			$_field				= array();
			$_field['key']		= 'discount_type';
			$_field['label']	= 'Discount Type';
			$_field['class']	= 'chosen';
			$_field['required']	= TRUE;

			$_options = array(
				'PERCENTAGE'	=> 'Percentage',
				'AMOUNT'		=> 'Specific amount'
			);
			
			echo form_field_dropdown( $_field, $_options );

			// --------------------------------------------------------------------------

			//	Discount value
			$_field					= array();
			$_field['key']			= 'discount_value';
			$_field['label']		= 'Discount Value';
			$_field['placeholder']	= 'Define the value of the discount as appropriate (i.e percentage or amount)';
			$_field['required']		= TRUE;
			
			echo form_field( $_field, 'If Discount Type is Percentage then specify a number 1-100, if it\'s a Specific Amount then define the amount.' );

			// --------------------------------------------------------------------------

			//	Discount application
			$_field				= array();
			$_field['key']		= 'discount_application';
			$_field['label']	= 'Applies to';
			$_field['class']	= 'chosen';
			$_field['required']	= TRUE;

			$_options = array(
				'PRODUCTS'		=> 'Purchases Only',
				'PRODUCT_TYPES'	=> 'Certain Type of Product Only',
				'SHIPPING'		=> 'Shipping Costs Only',
				'ALL'			=> 'Both Products and Shipping'
			);
			
			echo form_field_dropdown( $_field, $_options );

			// --------------------------------------------------------------------------

			//	Valid from
			$_field					= array();
			$_field['key']			= 'valid_from';
			$_field['label']		= 'Valid From';
			$_field['default']		= date( 'Y-m-d H:i:s', strtotime( 'TODAY' ) );
			$_field['placeholder']	= 'YYYY-MM-DD HH:MM:SS';
			$_field['class']		= 'datetime1';
			$_field['required']		= TRUE;
			
			echo form_field( $_field );

			// --------------------------------------------------------------------------

			//	Valid To
			$_field					= array();
			$_field['key']			= 'valid_to';
			$_field['label']		= 'Valid To';
			$_field['sub_label']	= 'Leave blank for no expiry date';
			$_field['placeholder']	= 'YYYY-MM-DD HH:MM:SS';
			$_field['class']		= 'datetime2';
			
			echo form_field( $_field, 'If left blank then the voucher will not expire (unless another expiring condition is met).' );

		?>
	</fieldset>

	<fieldset id="create-voucher-meta">
		<legend>Extended Data</legend>
		<div id="no-extended-data" style="display:block;">
			<p class="system-alert message no-close">
				<strong>Note:</strong> More options may become available depending on your choices above.
			</p>
		</div>
		<div id="type-limited" style="display:none;">
			<?php

			//	Limited Use Limit
			$_field					= array();
			$_field['key']			= 'limited_use_limit';
			$_field['label']		= 'Limit number of uses';
			$_field['placeholder']	= 'Define the number of times this voucher can be used.';
			$_field['required']		= TRUE;
			
			echo form_field( $_field );

			?>
		</div>
		<div id="application-product_types" style="display:none;">
			<?php

			//	Product Types application
			$_field					= array();
			$_field['key']			= 'product_type_id';
			$_field['label']		= 'Limit to products of type';
			$_field['required']		= TRUE;
			$_field['class']		= 'chosen';
			
			echo form_field_dropdown( $_field, $product_types );

			?>
		</div>
	</fieldset>
	<p>
		<?=form_submit( 'submit', lang( 'action_create' ) )?>
	</p>
	<?=form_close()?>
</div>

<script type="text/javascript">
	$( function(){

		var _shop_voucher = new NAILS_Admin_Shop_Vouchers;
		_shop_voucher.init_create();
		
	})
</script>