<div class="group-members all">
	<p>
		<?=isset( $page->description ) ? $page->description : 'This section lists all users registered on site. You can browse or search this list using the search facility below.' ?>
	</p>
	
	<?php
	
		$this->load->view( 'admin/accounts/utilities/search' );
		$this->load->view( 'admin/accounts/utilities/pagination' );
	
	?>
	
	<table>
		<thead>
			<tr>
				<th class="id">User ID</th>
				<th class="details">User</th>
				<th class="group">Group</th>
				<?php
				
					foreach ( $columns AS $col ) :
					
						echo isset( $col['class'] ) ? '<th class="' . $col['class'] . '">' : '<th>';
						echo $col['label'];
						echo '</th>';
						
					endforeach;
				
				?>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php
			
				if ( $users->data ) :
				
					foreach ( $users->data AS $member ) :
					
						$_data = array(
							'member'	=> &$member
						);
						$this->load->view( 'admin/accounts/utilities/user_row', $_data );
						
					endforeach;
					
				else :
					?>
					<tr>
						<td colspan="<?=(4+count($columns))?>" class="no-data">
							<p>No Users found</p>
						</td>
					</tr>
					<?php
				endif;
			
			?>
		</tbody>
	</table>
	
	<?php
	
		$this->load->view( 'admin/accounts/utilities/pagination' );
	
	?>
</div>

<script style="text/javascript">
<!--//

	$(function(){
	
		//	Hijack the fancybox links and inform the target the view is inline.
		$( 'a.fancybox-max' ).each( function() {
		
			$(this).fancybox({
				'type'		: 'iframe',
				'autoSize'	: false,
				'autoScale'	: false,
				'width'		: '85%',
				'height'	: '85%',
				'href'		: $(this).attr( 'href' ) + '&inline=true'
			});
		});
	
	});

//-->
</script>