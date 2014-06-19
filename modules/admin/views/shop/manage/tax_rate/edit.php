<div class="group-shop manage tax-rate edit">
	<?php

		if ( $is_fancybox ) :

			echo '<h1>' . $page->title . '</h1>';
			$_class = 'system-alert';

		else :

			$_class = '';

		endif;

		echo form_open( uri_string() . $is_fancybox );

	?>
	<p class="<?=$_class?>">
		Manage which tax rates the shop supports.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul class="tabs disabled">
		<li class="tab">
			<?=anchor( 'admin/shop/manage/tax_rate' . $is_fancybox, 'Overview', 'class="confirm" data-title="Are you sure?" data-body="Any unsaved changes will be lost."' )?>
		</li>
		<li class="tab active">
			<?=anchor( 'admin/shop/manage/tax_rate/create' . $is_fancybox, 'Create Tax Rate' )?>
		</li>
	</ul>
	<section class="tabs pages">
		<div class="tab page active">
			<fieldset>
				<legend>Basic Details</legend>
				<p>
					These fields describe the tax rate.
				</p>
				<?php

					$_field					= array();
					$_field['key']			= 'label';
					$_field['label']		= 'Label';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'The tax rate\'s label';
					$_field['default']		= isset( $tax_rate->label ) ? $tax_rate->label : '';

					echo form_field( $_field );

					// --------------------------------------------------------------------------

					$_field					= array();
					$_field['key']			= 'rate';
					$_field['label']		= 'Rate';
					$_field['required']		= TRUE;
					$_field['placeholder']	= 'The tax rate\'s rate';
					$_field['default']		= isset( $tax_rate->rate ) ? $tax_rate->rate : '';
					$_field['info']			= 'This should be expressed as a decimal between 0 and 1. For example, 17.5% would be entered as 0.175.';

					echo form_field( $_field );

				?>
			</fieldset>
			<p style="margin-top:1em;">
				<?=form_submit( 'submit', 'Save', 'class="awesome"' )?>
				<?=anchor( 'admin/shop/manage/tax_rate' . $is_fancybox, 'Cancel', 'class="awesome red confirm" data-title="Are you sure?" data-body="All unsaved changes will be lost."' )?>
			</p>
		</div>
	</section>
	<?=form_close();?>
</div>
<?php

	$this->load->view( 'admin/shop/manage/tax_rate/_footer' );