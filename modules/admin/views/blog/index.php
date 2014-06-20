<div class="group-blog manage">
	<p>
		This page shows all the posts on site and allows you to manage them.
	</p>

	<hr />

	<?php

		$this->load->view( 'admin/blog/_utilities/search' );
		$this->load->view( 'admin/_utilities/pagination' );

	?>

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

						echo img( NAILS_ASSETS_URL . 'img/admin/modules/blog/image-icon.png' );

					endif;

					echo '</td>';

					echo '<td class="title">';

						//	Title
						echo $post->title;

						//	URL
						echo '<small>' . anchor( $post->url, $post->url, 'target="_blank"' ) . '</small>';

						//	Exceprt
						if ( app_setting( 'use_excerpt', 'blog' ) ) :

							echo '<small>' . $post->excerpt . '</small>';

						endif;

					echo '</td>';

					if ( $post->is_published ) :

						echo '<td class="status success">';
						echo '<span class="ion-checkmark-circled"></span>';
						echo '<small class="nice-time">' . user_datetime( $post->published, 'Y-m-d', 'H:i:s' ) . '</small>';
						echo '</td>';

					else :

						echo '<td class="status error">';
						echo '<span class="ion-close-circled"></span>';
						echo '</td>';

					endif;


					//	User common cells
					$this->load->view( 'admin/_utilities/table-cell-user',		$post->author );
					$this->load->view( 'admin/_utilities/table-cell-datetime',	array( 'datetime' => $post->modified ) );

					echo '<td class="actions">';
					echo anchor( 'admin/blog/edit/' . $post->id, lang( 'action_edit' ), 'class="awesome small"' );
					echo anchor( 'admin/blog/delete/' . $post->id, lang( 'action_delete' ), 'class="awesome small red confirm" data-title="Confirm Delete" data-body="Are you sure you want to delete this post?"' );
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

	<?php

		$this->load->view( 'admin/_utilities/pagination' );

	?>
</div>