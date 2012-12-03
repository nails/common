<style type="text/css">

	th.actions, td.actions,
	th.id, td.id				{ width:50px; text-align:center; }

</style>

<p>
	The following videos are available to you.
</p>

<hr />

<table>
	<thead>
		<tr>
			<th class="id">Video ID</th>
			<th class="name-desc">Name and Description</th>
			<th class="actions">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		
			if ( $videos ) :
			
				foreach ( $videos AS $v ) :
				
				echo '<tr>';
				echo '<td class="id">' . $v->id . '</td>';
				echo '<td class="name-desc">';
				echo $v->title;
				echo '<small>' . $v->description . '</small>';
				echo '</td>';
				echo '<td class="actions">';
				echo '<a href="http://player.vimeo.com/video/' . $v->vimeo_id . '?autoplay=true" class="awesome small video-button">View</a>';
				echo '</td>';
				echo '</tr>';
				
				endforeach;
			
			else :
			
				echo '<tr>';
				echo '<td id="no_records" colspan="3"><p>No Records Found</p></td>';
				echo '</tr>';
			
			endif;
		
		?>
	</tbody>
</table>

<script tyle="text/javascript">
<!--//

	$(function(){
	
		$( 'a.video-button' ).fancybox({ type : 'iframe' });
	
	});

//-->
</script>