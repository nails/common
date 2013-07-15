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
				<th class="user">Author</th>
				<th class="datetime">Modified</th>
				<th class="actions">Actions</th>
			</tr>
		</thead>
		<tbody>
		<?php
		
			if ( $posts ) :

				$_date_format = active_user( 'pref_date_format' );
				$_time_format = active_user( 'pref_time_format' );
			
				foreach ( $posts AS $post ) :
				
					echo '<tr class="post" data-title="' . $post->title . '">';
					
					echo '<td class="image">';
					
					if ( $post->image_id ) :
					
						echo anchor( cdn_serve( $post->image_id ), img( cdn_scale( $post->image_id, 75, 75 ) ), 'class="fancybox"' );
					
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
						echo '<small class="nice-time">' . user_datetime( $post->published, 'Y-m-d', 'H:i:s' ) . '</small>';
						
					else :
					
						echo '<span class="no">No</span>';
						
					endif;
					
					echo '</td>';
					
					//	User common cells
					$this->load->view( 'admin/_utilities/table-cell-user',		$post->author );
					$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $post->modified ) );
					
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