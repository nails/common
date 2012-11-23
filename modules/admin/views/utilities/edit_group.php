<div class="system-alert message">
	<p>
		<strong style="text-transform:uppercase;">Please be very careful</strong>
	</p>
	<p>
		While we'll do our best to validate the content you set sometimes a valid combination can render an
		entire group useless. Please be extra careful and only change things when you know what you're doing.
		Remember that you won't see the effect of changing the permissions of a
		group other than your own, check that your changes have worked before considering the job done!
	</p>
</div>

<hr />

<?=form_open()?>

	<!--	BASICS	-->
	<div class="fieldset">
	
		<div class="legend">Basics</div>
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
		
	</div>
	
	<!--	PERMISSIONS	-->
	<div class="fieldset" id="permissions">
	
		<div class="legend">Permissions</div>
		
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
			
				$_field					= array();
				$_field['key']			= 'acl[admin][]';
				$_field['label']		= $detail->name;
				$_field['default']		= FALSE;
				
				$_value = $module =='dashboard' ? '<small>If any admin module is selected this must also be selected.</small>' : '';
				
				
				$_options = array();
				$_options[] = array(
					'value'		=> $detail->class_name,
					'label'		=> $_value,
					'selected'	=> isset( $group->acl['admin'] ) && array_search( $module , $group->acl['admin'] ) !== FALSE ? TRUE : FALSE
				);
				
				echo form_field_checkbox( $_field, $_options );
		
			endforeach;
			
		?>
		
	</div>
	
	<p>
		<?=form_submit( 'submit', 'Save Changes' )?>
	</p>
	
<?=form_close()?>


<script tyle="text/javascript">
<!--//

	$(function(){
	
		$( '#permissions input[name*=acl]' ).click( function() {
		
			$( '#permissions input[name*=acl]:checked' ).each( function() {
				
				if (this.name == 'acl[admin][]' )
				{
					//	At least one is checked, check the dashboard
					$( '#permissions input[name*=acl][value=dashboard]' ).attr( 'checked', 'checked' );
				}
				
			});
		
		});
	
	});

//-->
</script>