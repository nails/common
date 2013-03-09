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
			
			//	Name
			$_field					= array();
			$_field['key']			= 'description';
			$_field['type']			= 'textarea';
			$_field['label']		= lang( 'utilities_edit_group_basic_field_label_description' );
			$_field['default']		= $group->description;
			$_field['required']		= TRUE;
			$_field['placeholder']	= lang( 'utilities_edit_group_basic_field_placeholder_description' );
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			//	Name
			$_field					= array();
			$_field['key']			= 'default_homepage';
			$_field['label']		= lang( 'utilities_edit_group_basic_field_label_homepage' );
			$_field['default']		= $group->default_homepage;
			$_field['required']		= TRUE;
			$_field['placeholder']	= lang( 'utilities_edit_group_basic_field_placeholder_homepage' );
			
			echo form_field( $_field );
		
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
			$_field['default']		= FALSE;
			$_field['required']		= FALSE;
			
			$_options = array();
			$_options[] = array(
				'id'		=> 'super-check',
				'value'		=> 'TRUE',
				'label'		=> '',
				'selected'	=>	isset( $group->acl['superuser'] ) && $group->acl['superuser'] ? TRUE : FALSE
			);
			
			echo form_field_checkbox( $_field, $_options );
			
			// --------------------------------------------------------------------------
			
			$_visible = $_options[0]['selected'] ? 'none' : 'block';
			echo '<div id="toggle-superuser" style="display:' . $_visible . ';">';
			
			foreach ( $admin_modules AS $module => $detail ) : 
			
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
					echo '<div class="clear"></div>';
				
				endforeach;
				
				echo '</div>';
		
			endforeach;
			
			echo '</div>';
			
		?>
		
	</fieldset>
	
	<p>
		<?=form_submit( 'submit', lang( 'action_save_changes' ) )?>
	</p>
	
<?=form_close()?>


<script tyle="text/javascript">
<!--//

	$(function(){
	
		$( '#super-check-0' ).on( 'click', function() {
			
			console.log($( '#super-check-0:checked' ).length);
			if ( $( '#super-check-0:checked' ).length )
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