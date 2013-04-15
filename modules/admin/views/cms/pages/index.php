<div class="group-cms pages overview">

	<p>
		Listed below are all the editable pages on site. You use this page manager to edit page content or to change page layout.
	</p>
	
	<?php if ( $user->is_superuser() ) : ?>
	<div class="system-alert message no-close">
		<p style="margin-bottom:1em;">
			<strong>How CMS pages will work</strong>
		</p>
		<p style="margin-bottom:1em;">
			CMS Pages will be blank canvases which admins will be able to update and alter themselves. Pages will be made up of widgets
			and will be editable in a fancy drag-drop interface. Widgets will come in two flavours: full width and and half width.
		</p>
		<p style="margin-bottom:1em;">
			There will be default widgets supplied by Nails, these might include: text, HTML, slider, list. The individual apps must also
			be able to provide widget functionality and add to, extend or overwrite Nails widgets.
		</p>
		<p style="margin-bottom:1em;">
			I expect a widget will be a self contained class with various common methods, the most important two being setup() and render()
			which will configure the widget and return HTML respectively.
		</p>
		<p style="margin-bottom:1em;">
			Another important aspect to note is that the page module will write routes to a file. Apps should be configured to include these
			routes within the normal CI routes file. This will allow the module to place pages anywhere.
		</p>
		<p style="margin-bottom:1em;">
			Something to be considered is nesting and how that might work.
		</p>
		<p>
			Obviously, this is a long way off and even v1 is a mission, but it'll be awesome.
		</p>
	</div>
	<?php endif; ?>
	
	<hr />
	
	<table>
		<thead>
			<tr>
				<th class="title">Page</th>
				<th class="modified">Modified</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
			if ( $pages ) :
			
				foreach ( $pages AS $page ) :
				
					echo '<tr>';
					echo '<td class="title">';
					echo '<span class="title">';
					if ( $page->title || $page->second_column_title ) :
					
						if ( $page->title ) :
						
							echo $page->title;
						
						else :
						
							echo $page->second_column_title;
						
						endif;
					
					else :
					
						echo '<span class="no-data">&mdash;</span>';
					
					endif;
					echo '</span>';
					echo '<span class="url">' . site_url( $page->url ) . '</span>';
					echo '</td>';
					echo '<td class="modified">';
					echo nice_time( $page->modified );
					echo $page->user_id ? '<small>by ' . anchor( 'admin/accounts/edit/' . $page->user_id, $page->first_name . ' ' . $page->last_name ) . '</small>' : '';
					echo '</td>';
					echo '<td class="actions">';
					
					echo anchor( 'admin/cms/edit/' . $page->id, 'Edit', 'class="awesome small"' );
					echo anchor( $page->url, 'View', 'class="awesome small"' );
					echo $page->revision_count ? anchor( 'admin/cms/revisions/' . $page->id, 'Revisions', 'class="awesome small"' ) : '';
					
					echo '</td>';
					echo '</tr>';
				
				endforeach;
			
			else :
			
					echo '<tr>';
					echo '<td colspan="4" class="no-data">';
					echo 'No editable pages found';
					echo '</td>';
					echo '</tr>';
			
			endif;
		
		?>
		</tbody>
	</table>
	
</div>