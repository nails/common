<p>
	Manage how groups of user's can interface with the site, e.g: grant a specific group access to admin and specify which parts of admin they can view.
</p>

<hr />

<table>
	<thead>
		<tr>
			<th>Name</th>
			<th>Default Homepage</th>
			<th>Permissions</th>
			<th>Options</th>
		</tr>
	</thead>
	
	<tbody>
	
	<?php foreach ( $groups AS $group ) : ?>
	
		<tr>
			<td style="vertical-align:top;">
				<strong><?=$group->display_name?></strong>
				<small style="display:block;"><?=$group->description?></small>
			</td>
			<td style="vertical-align:top;">
				<span style="color:#ccc"><?=substr( site_url(), 0, -1 )?></span><?=$group->default_homepage?>
			</td>
			<td class="permissions" style="vertical-align:top;min-width:250px;">
				<ul>
				<?php
				
					if ( $group->acl ) :
					
						foreach( $group->acl AS $key => $value ) :
						
							if ( is_bool( $value ) ) :
							
								$value = ( $value ) ? 'TRUE' : 'FALSE';
							
							endif;
							
							if ( ! is_array( $value ) ) :
							
								echo '<li><strong>' . $key . ':</strong> ' . $value . '</li>';
								
							else :
							
								echo '<li><strong>' . $key . ':</strong><ul>';
								
									foreach ( $value AS $key_1 => $value_1 ) :
									
										if ( is_bool( $value_1 ) ) :
										
											$value_1 = ( $value_1 ) ? 'TRUE' : 'FALSE';
										
										endif;
										
										echo '<li>&rsaquo; <strong>' . $key_1 . ':</strong> ' . $value_1 . '</li>';
									
									endforeach;
								
								echo '</ul></li>';
							
							endif;
						
						endforeach;
					
					endif;
					
				?>
				</ul>
			</td>
			<td>
				<?=anchor( 'admin/utilities/edit_group/' . $group->id, 'Edit', 'class="awesome small"' )?>
				<?=anchor( 'admin/utilities/delete_group', 'Delete', 'class="awesome small red"' )?>
			</td>
		</tr>
	
	<?php endforeach; ?>
	
	</tbody>
</table>