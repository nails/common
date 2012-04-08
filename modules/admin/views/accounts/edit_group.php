<style type="text/css">

	input[type=text]
	{
		width:330px;
	}
	
	
	textarea
	{
		width:330px;
	}

</style>

<h1>Edit a user group: "<?=$group->display_name?>"</h1>
<div class="message">
	<p>
		<strong style="text-transform:uppercase;">Please be very careful</strong>
	</p>
	<p>
		While we'll do our best to validate the content you set sometimes a valid combination can render an entire group useless. Please don't be stupid and only change things when you know what you're doing.
	</p>
</div>

<hr />

<?=form_open()?>
	
<div style="margin-left:10px;margin-right:10px;">


	<div class="box specific" style="padding-bottom:10px; width:500px;float:left; margin-right:30px;">
	
		<h2>Basics</h2>
		
		<div style="padding:0 12px;">
						
			<table class="blank">
			
				<tr>
					<td style="text-align:right;width:150px;">
						<strong>Display Name</strong>:
					</td>
					<td>
						<?=form_input( 'display_name', set_value( 'display_name', $group->display_name ) )?>
					</td>
				</tr>
				<?php if ( form_error( 'display_name' ) ) : ?>
				<tr>
					<td>&nbsp;</td>
					<td>
						<?=form_error( 'display_name', '<p class="error">', '</p>' )?>
					</td>
				</tr>
				<?php endif; ?>
				
				<tr>
					<td style="text-align:right;width:150px;">
						<strong>Slug</strong>:
					</td>
					<td>
						<?=form_input( 'name', set_value( 'name', $group->name ) )?>
					</td>
				</tr>
				
				<tr>
					<td style="text-align:right;width:150px;">
						<strong>Description</strong>:
					</td>
					<td>
						<?=form_textarea( 'description', set_value( 'description', $group->description ) )?>
					</td>
				</tr>
				
				<tr>
					<td style="text-align:right;width:150px;">
						<strong>Default Homepage</strong>:
					</td>
					<td>
						<?=form_input( 'default_homepage', set_value( 'default_homepage', $group->default_homepage ) )?>
					</td>
				</tr>
			</table>
		
		</div>
		
	</div>
	
</div>


<div style="margin-left:10px;margin-right:10px;">


	<div class="box specific" style="padding-bottom:10px; width:500px;float:left;">
	
		<h2>Permissions</h2>
		
		<div style="padding:0 12px;">
						
			<table class="blank">
			
				<?php if ( form_error( 'acl[]' ) ) :?>
				<p class="error">
					You must specify at least one permission.
				</p>
				<?php endif; ?>
				
				<tbody>
					<tr>
						<td style="text-align:right;width:150px;">
							<strong>Superuser</strong>:
						</td>
						<td>
							<?php
								
								$_checked = ( isset( $group->acl['superuser'] ) && $group->acl['superuser'] ) ? TRUE : FALSE;
								echo form_checkbox( 'acl[superuser]', TRUE, set_checkbox( 'acl[superuser]', TRUE, $_checked ) );
								
							?>
						</td>
					</tr>
				</tbody>
			</table>
			
			<hr />
			
			<p>
				You can grant a group access to the administration area of <?=APP_NAME?> by selecting which admin modules they have
				permission to access. <strong>It goes without saying that you should be careful with these options.</strong>
			</p>
			
			<table class="blank">
			
				<tbody>
				<?php foreach ( $admin_modules AS $module => $detail ) : ?>
					<tr>
						<td style="text-align:right;width:150px;">
							<strong><?=ucfirst( $detail->name )?></strong>:
						</td>
						<td>
							<?php
								
								$_checked = ( isset( $group->acl['admin'] ) && array_search( $module , $group->acl['admin'] ) !== FALSE ) ? TRUE : FALSE;
								echo form_checkbox( 'acl[admin][]', $module, set_checkbox( 'acl[admin][]', $module, $_checked ), 'class="admin_check"' );
								
								if ( $module == 'dashboard' )
									echo '<small>If any admin module is selected this must also be selected.</small>';
							?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				
			</table>
			
		</div>
		
	</div>
	
</div>

<hr />

<?=form_submit( 'submit', 'Save' )?>
	
<?=form_close()?>


<script tyle="text/javascript">
<!--//

	$(function(){
	
		$( '.admin_check' ).click( function() {
		
			//	Check to see if ANY of the checkboxes are checked, if they
			//	are dashboard MUST be checked.
			
			if ( $( '.admin_check:checked[value!=dashboard]' ).length )
			{
				$( '.admin_check[value=dashboard]' ).attr( 'checked', 'checked' );
			}
			
			$.uniform.update( '.admin_check' );
		
		});
	
	});

//-->
</script>