<div class="group-stats">

	<p class="system-alert message no-close">
		<strong>TODO:</strong> Build an interactive event browser with search/filters.
	</p>

	<p>
		Browse recent events.
	</p>
	
	<hr />
	
	<div class="search">
		<div class="search-text">
			<input type="text" name="search" value="" autocomplete="off" placeholder="Search events by typing in here...">
		</div>
	</div>
	
	<hr />
	
	<table>
		<thead>
			<tr>
				<th class="created">Date</th>
				<th class="user">User</th>
				<th class="event">Type of Event</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
			if ( $events ) :
			
				foreach ( $events AS $event ) :

					echo '<tr class="event" data-title="' . $event['type']->name . '">';

					echo '<td>' . $event['created'] . '</td>';
					echo '<td>' . $event['creator']->first_name . ' ' . $event['creator']->last_name . '</td>';
					echo '<td>' . $event['type']->name . '</td>';
					echo '<td></td>';
					
					echo '</tr>';
				
				endforeach;
			
			else :
			
				echo '<tr>';
				echo '<td colspan="5" class="no-data">';
				echo 'No Events found';
				echo '</td>';
				echo '</tr>';
			
			endif;
		
		?>
		</tbody>
	</table>
</div>

<script style="text/javascript">
<!--//

	$(function(){
	
		var Articles = new Admin_Articles;
		Articles.init_search();
		
	
	});

//-->
</script>