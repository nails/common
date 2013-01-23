<div class="system-alert message">
	<div class="padder">
		<p>
			While we'll do our best to validate the content you set sometimes a valid combination can render an
			entire group useless. Please be extra careful and only change things when you know what you're doing.
			Remember that you won't see the effect of changing the permissions of a
			group other than your own, check that your changes have worked before considering the job done!
		</p>
	</div>
</div>

<hr />

<?=form_open()?>

	<!--	BASICS	-->
	<fieldset>
	
		<legend>Basics</legend>
		<?php
		
			//	Display Name
			$_field					= array();
			$_field['key']			= 'display_name';
			$_field['label']		= 'Display Name';
			$_field['default']		= $group->display_name;
			$_field['required']		= TRUE;
			$_field['placeholder']	= 'Type the group\'s display name here.';
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			//	Name
			$_field					= array();
			$_field['key']			= 'name';
			$_field['label']		= 'Name';
			$_field['default']		= $group->name;
			$_field['required']		= TRUE;
			$_field['placeholder']	= 'Type the group\'s name here.';
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			//	Name
			$_field					= array();
			$_field['key']			= 'description';
			$_field['type']			= 'textarea';
			$_field['label']		= 'Description';
			$_field['default']		= $group->description;
			$_field['required']		= TRUE;
			$_field['placeholder']	= 'Type the group\'s description here.';
			
			echo form_field( $_field );
			
			// --------------------------------------------------------------------------
			
			//	Name
			$_field					= array();
			$_field['key']			= 'default_homepage';
			$_field['label']		= 'Homepage';
			$_field['default']		= $group->default_homepage;
			$_field['required']		= TRUE;
			$_field['placeholder']	= 'Type the group\'s homepage here.';
			
			echo form_field( $_field );
		
		?>
		
	</fieldset>
	
	<!--	PERMISSIONS	-->
	<fieldset id="permissions">
	
		<legend>Permissions</legend>
		
		<p>
			Superusers have full, unrestricted access to admin.
		</p>
		<p>
			For non-superuser groups you may also grant a access to the administration area by selecting which admin modules they have
			permission to access. <strong>It goes without saying that you should be careful with these options.</strong>
		</p>
		
		<?php
		
			//	Require password update on log in
			$_field					= array();
			$_field['key']			= 'acl[superuser]';
			$_field['label']		= 'Is Superuser';
			$_field['default']		= FALSE;
			$_field['required']		= FALSE;
			
			$_options = array();
			$_options[] = array(
				'value'		=> 'TRUE',
				'label'		=> '',
				'selected'	=>	isset( $group->acl['superuser'] ) && $group->acl['superuser'] ? TRUE : FALSE
			);
			
			echo form_field_checkbox( $_field, $_options );
			
			// --------------------------------------------------------------------------
		
			foreach ( $admin_modules AS $module => $detail ) : 
			
				$_module_hash = md5( serialize( $module ) );
				
				if ( $detail->name == 'Dashboard' ) :
				
					$_dashmd5 = $_module_hash;
				
				endif;
			
				$_field					= array();
				$_field['label']		= $detail->name;
				$_field['default']		= FALSE;
				
				//	Build the field. Sadly, can't use the form helper due to the crazy multidimensional array
				//	that we're building here.
				
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
						echo '<small><a href="#" class="check-all" data-module="' . $_module_hash . '">Toggle all</a></small>';
						echo '</span>';
						
					endif;
					
					$_sub_label = $module == 'dashboard' && $method == 'index' ? '<br /><small>If any admin method is selected then this must also be selected.</small>' : '';
					
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
			
		?>
		
	</fieldset>
	
	<p>
		<?=form_submit( 'submit', 'Save Changes' )?>
	</p>
	
<?=form_close()?>


<script tyle="text/javascript">
<!--//

	$(function(){
	
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