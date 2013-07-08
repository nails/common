<div class="group-utilities user-access edit">
	<div class="system-alert message">
		<div class="padder">
			<p>
				<?=lang( 'utilities_edit_group_warning' )?>
			</p>
		</div>
	</div>
	
	<hr />
	
	<?=form_open()?>
	
		<!--	BASICS	-->
		<fieldset>
		
			<legend><?=lang( 'utilities_edit_group_basic_legend' )?></legend>
			<?php
			
				//	Display Name
				$_field					= array();
				$_field['key']			= 'display_name';
				$_field['label']		= lang( 'utilities_edit_group_basic_field_label_display' );
				$_field['default']		= $group->display_name;
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'utilities_edit_group_basic_field_placeholder_display' );
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				//	Name
				$_field					= array();
				$_field['key']			= 'name';
				$_field['label']		= lang( 'utilities_edit_group_basic_field_label_name' );
				$_field['default']		= $group->name;
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'utilities_edit_group_basic_field_placeholder_name' );
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				//	Description
				$_field					= array();
				$_field['key']			= 'description';
				$_field['type']			= 'textarea';
				$_field['label']		= lang( 'utilities_edit_group_basic_field_label_description' );
				$_field['default']		= $group->description;
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'utilities_edit_group_basic_field_placeholder_description' );
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				//	Default Homepage
				$_field					= array();
				$_field['key']			= 'default_homepage';
				$_field['label']		= lang( 'utilities_edit_group_basic_field_label_homepage' );
				$_field['default']		= $group->default_homepage;
				$_field['required']		= TRUE;
				$_field['placeholder']	= lang( 'utilities_edit_group_basic_field_placeholder_homepage' );
				
				echo form_field( $_field );
				
				// --------------------------------------------------------------------------
				
				//	Registration Redirect
				$_field					= array();
				$_field['key']			= 'registration_redirect';
				$_field['label']		= lang( 'utilities_edit_group_basic_field_label_registration' );
				$_field['default']		= $group->registration_redirect;
				$_field['required']		= FALSE;
				$_field['placeholder']	= lang( 'utilities_edit_group_basic_field_placeholder_registration' );
				
				echo form_field( $_field, lang( 'utilities_edit_group_basic_field_tip_registration' ) );
			
			?>
			
		</fieldset>
		
		<!--	PERMISSIONS	-->
		<fieldset id="permissions">
		
			<legend><?=lang( 'utilities_edit_group_permission_legend' )?></legend>
			
			<p class="system-alert message no-close">
				<?=lang( 'utilities_edit_group_permission_warn' )?>
			</p>
			<p>
				<?=lang( 'utilities_edit_group_permission_intro' )?>
			</p>
			
			<hr />
			
			<?php
			
				//	Require password update on log in
				$_field					= array();
				$_field['key']			= 'acl[superuser]';
				$_field['label']		= lang( 'utilities_edit_group_permissions_field_label_superuser' );
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
							echo '<small><a href="#" class="check-all" data-module="' . $_module_hash . '">' . lang( 'utilities_edit_group_permissions_toggle_all' ) . '</a></small>';
							echo '</span>';
							
						endif;
						
						$_sub_label = $module == 'dashboard' && $method == 'index' ? '<br /><small>' . lang( 'utilities_edit_group_permissions_dashboard_warn' ) . '</small>' : '';
						
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
						
						echo '</label>';
					
					endforeach;

					// --------------------------------------------------------------------------

					//	Any extra permissions to render?
					foreach ( $detail->extra_permissions AS $permission => $label ) :

						echo '<label>';
						echo '<span class="label">&nbsp;</span>';

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


<script tyle="text/javascript">
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