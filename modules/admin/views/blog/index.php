<div class="group-blog manage">
	<p>
		This page shows all the posts on site and allows you to manage them.
	</p>
	
	<hr />
	
	<div class="search">
		<div class="search-text">
			<input type="text" name="search" value="" autocomplete="off" placeholder="Search post titles by typing in here...">
		</div>
	</div>
	
	<hr />
	
	<table>
		<thead>
			<tr>
				<th class="image">Image</th>
				<th class="title">Details</th>
				<th class="status">Published</th>
				<th class="author">Author</th>
				<th class="modified">Modified</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
			if ( $posts ) :
			
				foreach ( $posts AS $post ) :
				
					echo '<tr class="post" data-title="' . $post->title . '">';
					
					echo '<td class="image">';
					
					if ( $post->image ) :
					
						echo anchor( cdn_serve( 'blog', $post->image ), img( cdn_scale( 'blog', $post->image, 75, 75 ) ), 'class="fancybox"' );
					
					else :
					
						echo img( NAILS_URL . 'img/admin/blog/image-icon.png' );
					
					endif;
					
					echo '</td>';
					
					echo '<td class="title">';

						//	Title
						echo $post->title;

						//	URL
						echo '<small>' . anchor( $post->url, $post->url, 'target="_blank"' ) . '</small>';

						//	Exceprt
						echo '<small>' . $post->excerpt . '</small>';

					echo '</td>';
					
					echo '<td class="status">';
					if ( $post->is_published ) :
					
						echo '<span class="yes">Yes</span>';
						echo '<small class="nice-time">' . $post->published . '</small>';
						
					else :
					
						echo '<span class="no">No</span>';
						
					endif;
					
					echo '</td>';
					
					echo '<td class="author">' . $post->author->first_name . ' ' . $post->author->last_name . '</td>';
					echo '<td class="modified"><span class="nice-time">' . $post->modified . '</span></td>';
					
					echo '<td class="actions">';
					echo anchor( 'admin/blog/edit/' . $post->id, lang( 'action_edit' ), 'class="awesome small"' );
					echo anchor( 'admin/blog/delete/' . $post->id, lang( 'action_delete' ), 'class="awesome small red confirm" data-confirm="Are you sure you want to delete this post?"' );
					echo '</td>';
					
					echo '</tr>';
				
				endforeach;
			
			else :
			
				echo '<tr>';
				echo '<td colspan="6" class="no-data">';
				echo 'No Posts found';
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
	
		var Blog = new NAILS_Admin_Blog;
		Blog.init_search();
		
	
	});

//-->
</script>