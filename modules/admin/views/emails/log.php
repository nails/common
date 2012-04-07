<!--	CUSTOM CSS	-->
<style type="text/css">
	
	td.vars, td.options { width: 100px; text-align:center; }
	td#no_records { height: 75px; text-align: center; color: #aaa; text-transform: uppercase; }
	.vars_note { font-weight:bold;padding-bottom:10px;margin-bottom:10px;border-bottom:1px dashed #ccc; }

</style>

<!--	CUSTOM JS-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$( 'a.fancybox' ).fancybox( { 'width':900, 'height':600, 'type':'iframe' } );
		$( 'a.delete' ).click( function() {
		
			return confirm( 'Are you sure? This cannot be undone.' );
		
		});
	
	});
	
//-->
</script>


<h1>Email Logs</h1>
<p>
	Every time the system queues or sends an email it records exactly what's going on. You can view the daily logs here.
	<?=anchor( 'admin/emails/log/delete_all', 'DELETE ALL', 'class="delete right a-button a-button-red a-button-small"' )?>
</p>

<table>

	<thead>
	
		<tr>
			
			<th>Date</th>
			<th>Logfile</th>
			<th>Size</th>
			<th>Options</th>
		
		</tr>
	
	</thead>
	
	<tbody>
	
		<?php if ( count( $logs ) ) : ?>
		<?php foreach ( $logs AS $l ) : ?>
	
			<tr>
			
				<td><?=$l['date']?></td>
				<td><?=$l['logfile']?></td>
				<td><?=format_bytes( $l['size'] )?></td>
				<td class="options">
					
					<?=anchor( 'admin/emails/log/view?logfile='.urlencode( $l['logfile'] ), 'View', 'class="fancybox a-button a-button-small"' ) ?>
					<?=anchor( 'admin/emails/log/delete?logfile='.urlencode( $l['logfile'] ), 'Delete', 'class="delete a-button a-button-small a-button-red"' ) ?>
														
				</td>
			
			</tr>
		
		<?php endforeach; ?>
		<?php else: ?>
	
			<tr>
				<td colspan="4" id="no_records">
				
					<p>No logs found</p>
				
				</td>
			</tr>
		
		<?php endif;?>
	
	</tbody>

</table>