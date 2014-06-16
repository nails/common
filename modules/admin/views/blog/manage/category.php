<?php

	$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=true' : '';

?>
<div class="group-blog manager category <?=$_is_fancybox ? 'is-fancybox' : ''?>">
	<?php

		if ( $this->input->get( 'is_fancybox' ) ) :

			echo '<h1>Category Manager</h1>';

		endif;
	?>
	<p>
		Use categories to group broad post topics together. For example, a category might be 'Music', or 'Travel'.
		<?php

			if ( app_setting( 'tags_enabled', 'blog' ) ) :

				echo 'For specific details (e.g New Year ' . date( 'Y') . ') consider using a ' . anchor( 'admin/blog/manage/tags' . $_is_fancybox, 'tag' ) . '.';

			endif;

		?>
	</p>
	<p><strong>Create new category</strong></p>
	<?php

		echo form_open( uri_string() . $_is_fancybox );
		echo form_input( 'category', NULL, 'placeholder="Type category name and hit enter"' );
		echo form_close();

	?>
	<p><strong>Current Categories</strong></p>
	<?php

		if ( $categories ) :

			echo '<ul class="current">';

			foreach ( $categories AS $cat ) :

				echo '<li class="category">';
				echo anchor( 'admin/blog/delete_category/' . $cat->id . $_is_fancybox, lang( 'action_delete'), 'class="remove"' );
				echo $cat->label;
				echo '<span class="counter">' . $cat->post_count . '</span>';
				echo '</li>';

			endforeach;

			echo '<li class="clear">&nbsp;</li>';
			echo '</ul>';

		else :

			echo '<p class="system-alert message">';
			echo '<strong>No Categories!</strong> Create a category using the form above.';
			echo '</p>';

		endif;

	?>
</div>

<?php

	echo '<script type="text/javascript">';

	//	This variable holds the current state of objects and is read
	//	by the calling script when closing the fancybox, feel free to
	//	update this during the lietime of the fancybox.

	$_data					= array();				//	An array of objects in the format {id,label}

	foreach ( $categories AS $cat ) :

		$_temp			= new stdClass();
		$_temp->id		= $cat->id;
		$_temp->label	= $cat->label;

		$_data[] = $_temp;

	endforeach;

	echo 'var _DATA = ' . json_encode( $_data ) . ';';
	echo '</script>';

	// --------------------------------------------------------------------------