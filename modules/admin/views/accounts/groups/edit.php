<div class="group-accounts groups edit">
	<div class="system-alert message">
		<div class="padder">
			<p>
				<?=lang( 'accounts_groups_edit_warning' )?>
			</p>
		</div>
	</div>

	<hr />

	<?=form_open()?>

		<!--	BASICS	-->
		<fieldset>

			<legend><?=lang( 'accounts_groups_edit_basic_legend' )?></legend>
			<?php

				//	Display Name
				$_field					= array();
				$_field['key']			= 'label';
				$_field['label']		= lang( 'accounts_groups_edit_basic_field_label_label' );
				$_field['default']		= $group->label;
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'accounts_groups_edit_basic_field_placeholder_label' );

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Name
				$_field					= array();
				$_field['key']			= 'slug';
				$_field['label']		= lang( 'accounts_groups_edit_basic_field_label_slug' );
				$_field['default']		= $group->slug;
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'accounts_groups_edit_basic_field_placeholder_slug' );

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Description
				$_field					= array();
				$_field['key']			= 'description';
				$_field['type']			= 'textarea';
				$_field['label']		= lang( 'accounts_groups_edit_basic_field_label_description' );
				$_field['default']		= $group->description;
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'accounts_groups_edit_basic_field_placeholder_description' );

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Default Homepage
				$_field					= array();
				$_field['key']			= 'default_homepage';
				$_field['label']		= lang( 'accounts_groups_edit_basic_field_label_homepage' );
				$_field['default']		= $group->default_homepage;
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'accounts_groups_edit_basic_field_placeholder_homepage' );

				echo form_field( $_field );

				// --------------------------------------------------------------------------

				//	Registration Redirect
				$_field					= array();
				$_field['key']			= 'registration_redirect';
				$_field['label']		= lang( 'accounts_groups_edit_basic_field_label_registration' );
				$_field['default']		= $group->registration_redirect;
				$_field['required']		= FALSE;
				$_field['placeholder']	= lang( 'accounts_groups_edit_basic_field_placeholder_registration' );

				echo form_field( $_field, lang( 'accounts_groups_edit_basic_field_tip_registration' ) );

			?>

		</fieldset>

		<!--	PERMISSIONS	-->
		<fieldset id="permissions">

			<legend><?=lang( 'accounts_groups_edit_permission_legend' )?></legend>

			<p class="system-alert message">
				<?=lang( 'accounts_groups_edit_permission_warn' )?>
			</p>
			<p>
				<?=lang( 'accounts_groups_edit_permission_intro' )?>
			</p>

			<hr />

			<?php

				//	Require password update on log in
				$_field					= array();
				$_field['key']			= 'acl[superuser]';
				$_field['label']		= lang( 'accounts_groups_edit_permissions_field_label_superuser' );
				$_field['default']		= isset( $group->acl['superuser'] ) && $group->acl['superuser'] ? TRUE : FALSE;
				$_field['required']		= FALSE;
				$_field['id']			= 'super-user';

				echo form_field_boolean( $_field );

				// --------------------------------------------------------------------------

				$_visible = $_field['default'] ? 'none' : 'block';
				echo '<div id="toggle-superuser" style="display:' . $_visible . ';">';

				foreach ( $loaded_modules AS $module => $detail ) :

					$_module_hash = md5( serialize( $module ) );

					if ( $detail->name == 'Dashboard' ) :

						$_dashmd5 = $_module_hash;

					endif;

					$_field					= array();
					$_field['label']		= $detail->name;
					$_field['default']		= FALSE;

					//	Build the field. Sadly, can't use the form helper due to the crazy multidimensional array
					//	that we're building here. Saddest of the sad pandas.

					echo '<div class="field">';

					// Options
					$_acl_post	= $this->input->post( 'acl' );
					$_label		= '';

					foreach ( $detail->methods AS $method => $label ) :

						echo '<label>';

						//	Label
						if ( $_label == $_field['label'] ) :

							echo '<span class="label">&nbsp;</span>';

						else :

							$_label = $_field['label'];

							echo '<span class="label">';
							echo $_field['label'];
							echo '<small><a href="#" class="check-all" data-module="' . $_module_hash . '">' . lang( 'accounts_groups_edit_permissions_toggle_all' ) . '</a></small>';
							echo '</span>';

						endif;

						echo '<span class="input">';

						$_sub_label = $module == 'dashboard' && $method == 'index' ? '<br /><small>' . lang( 'accounts_groups_edit_permissions_dashboard_warn' ) . '</small>' : '';

						$_options = array(
							'key'		=> 'acl[admin][' . $module . '][' . $method . ']',
							'value'		=> TRUE,
							'label'		=> $label . $_sub_label,
							'selected'	=> isset( $group->acl['admin'][$module][$method] ) ? TRUE : FALSE
						);

						if ( $this->input->post() ) :

							$_selected = isset( $_acl_post['admin'][$module][$method] ) ? TRUE : FALSE;

						else :

							$_selected = isset( $group->acl['admin'][$module][$method] ) ? TRUE : FALSE;

						endif;

						echo form_checkbox( $_options['key'], TRUE, $_selected, 'class="admin_check method ' . $method . ' ' . $_module_hash  . '"' ) . '<span class="text">' . $_options['label'] . '</span>';

						echo '</span>';

						echo '</label>';

					endforeach;

					// --------------------------------------------------------------------------

					//	Any extra permissions to render?
					foreach ( $detail->extra_permissions AS $permission => $label ) :

						echo '<label>';
						echo '<span class="label">&nbsp;</span>';
						echo '<span class="input">';

						$_options = array(
							'key'		=> 'acl[admin][' . $module . '][' . $permission . ']',
							'value'		=> TRUE,
							'label'		=> $label,
							'selected'	=> isset( $group->acl['admin'][$module][$permission] ) ? TRUE : FALSE
						);

						if ( $this->input->post() ) :

							$_selected = isset( $_acl_post['admin'][$module][$permission] ) ? TRUE : FALSE;

						else :

							$_selected = isset( $group->acl['admin'][$module][$permission] ) ? TRUE : FALSE;

						endif;

						echo form_checkbox( $_options['key'], TRUE, $_selected, 'class="admin_check method ' . $permission . ' ' . $_module_hash  . '"' ) . '<span class="text">' . $label . '</span>';

						echo '</span>';
						echo '</label>';

					endforeach;

					echo '<div class="clear"></div>';
					echo '</div>';

				endforeach;

				echo '</div>';

			?>

		</fieldset>

		<p>
			<?=form_submit( 'submit', lang( 'action_save_changes' ) )?>
		</p>

	<?=form_close()?>
</div>


<script style="text/javascript">
<!--//

	$(function(){

		//$( '#super-user' ).on( 'change', function() {
		$('.field.boolean .toggle').on('toggle', function (e, active) {

			if ( active )
			{
				$( '#toggle-superuser' ).hide();
			}
			else
			{
				$( '#toggle-superuser' ).show();
			}

		});

		// --------------------------------------------------------------------------

		$( '.admin_check' ).on( 'click', function() {

			//	Check to see if ANY of the checkboxes are checked, if they
			//	are dashboard MUST be checked.

			if ( $( '.admin_check:checked:not(.<?=$_dashmd5?>.index)' ).length )
			{
				$( '.admin_check.<?=$_dashmd5?>.method.index' ).attr( 'checked', 'checked' );
			}

		});

		$( 'a.check-all' ).on( 'click', function() {

			var _hash = $(this).attr( 'data-module' );
			$( 'input.' + _hash ).click();

			return false;

		});

	});

//-->
</script>