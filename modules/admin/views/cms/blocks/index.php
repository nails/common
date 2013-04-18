<div class="group-cms blocks overview">
	<p>
		CMS Blocks allow you to update a single block of content. Blocks might appear in more than one place so any updates will be reflected across
		all instances. Blocks must have an <?=APP_DEFAULT_LANG_NAME?> value defined, but translations can also be created, when viewing the site in another language (if
		supported for this site) the appropriate language will be used.
	</p>
	
	<hr />
	
	<div class="search">
		<div class="search-text">
			<input type="text" name="search" value="" placeholder="Search block titles by typing in here...">
		</div>
	</div>
	
	<hr />
	
	<table>
		<thead>
			<tr>
				<th class="title">Block Title &amp; Description</th>
				<th class="default"><?=APP_DEFAULT_LANG_NAME?> Value</th>
				<th class="translations">Translations</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
			if ( $blocks ) :
			
				foreach ( $blocks AS $block ) :
				
					echo '<tr class="block" data-title="' . $block->title . '">';
					
					echo '<td class="title">';
					echo $block->title;
					echo '<small>';
					echo 'Description: ' . $block->description . '<br />';
					echo 'Located: ' . $block->located . '<br />';
					echo 'Type: ' . $block_types[$block->type] . '<br />';
					echo '</small>';
					echo '</td>';
					
					// --------------------------------------------------------------------------
					
					echo '<td class="default">';
					echo character_limiter( strip_tags( $block->default_value ), 100 );
					echo '</td>';
					
					// --------------------------------------------------------------------------
					
					echo '<td class="translations">';
					echo '<ul>';
					foreach ( $block->translations AS $variation ) :
					
						if ( $variation->lang->safename == APP_DEFAULT_LANG_SAFE )
							continue;
						
						// --------------------------------------------------------------------------
						
						echo '<li>';
						echo '<span class="lang" title="' . $variation->lang->name . '">' . $variation->lang->name . '</span>';
						echo '<span class="value">' . $variation->value . '</span>';
						echo '</li>';
					
					endforeach;
					echo '</ul>';
					echo'</td>';
					
					// --------------------------------------------------------------------------
					
					echo '<td class="actions">';
					echo anchor( 'admin/cms/blocks/edit/' . $block->id, 'Edit', 'class="awesome small"' );
					echo '</td>';
					
					echo '</tr>';
				
				endforeach;
			
			else :
			
					echo '<tr>';
					echo '<td colspan="3" class="no-data">';
					echo 'No editable blocks found';
					echo '</td>';
					echo '</tr>';
			
			endif;
		
		?>
		</tbody>
	</table>
	
	<?php
	
		if ( $user->is_superuser() ) :
		
			echo '<p class="new-block">';	
			echo anchor( 'admin/cms/blocks/create', 'Create New Block', 'class="awesome small"' );
			echo '</p>';
			
		endif;
		
	?>
</div>

<script style="text/javascript">
<!--//

	$(function(){
	
		var CMS_Blocks = new NAILS_Admin_CMS_Blocks;
		CMS_Blocks.init_search();
		
	
	});

//-->
</script>