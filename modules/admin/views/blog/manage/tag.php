<?php

	$_is_fancybox = $this->input->get( 'is_fancybox' ) ? '?is_fancybox=true' : '';

?>
<div class="group-blog manager tag <?=$_is_fancybox ? 'is-fancybox' : ''?>">
	<?php

		if ( $this->input->get( 'is_fancybox' ) ) :

			echo '<h1>Tag Manager</h1>';

		endif;
	?>
	<p>
		Use tags to group specific post topics together. For example, a tag might be 'New Year <?=date( 'Y')?>', or 'Coursework'.
		<?php

			if ( app_setting( 'categories_enabled', 'blog' ) ) :

				echo 'For broader subjects (e.g "Music" or "Travel") consider using a ' . anchor( 'admin/blog/manage/categories' . $_is_fancybox, 'category' ) . '.';

			endif;

		?>
	</p>
	<p><strong>Create new Tag</strong></p>
	<?php

		echo form_open( uri_string() . $_is_fancybox );
		echo form_input( 'tag', NULL, 'placeholder="Type category name and hit enter"' );
		echo form_close();

	?>
	<p><strong>Current Tags</strong></p>
	<?php

		if ( $tags ) :

			echo '<ul class="current">';

			foreach ( $tags AS $tag ) :

				echo '<li class="tag">';
				echo anchor( 'admin/blog/delete_tag/' . $tag->id . $_is_fancybox, lang( 'action_delete'), 'class="remove"' );
				echo $tag->label;
				echo '<span class="counter">' . $tag->post_count . '</span>';
				echo '</li>';

			endforeach;

			echo '<li class="clear">&nbsp;</li>';
			echo '</ul>';

		else :

			echo '<p class="system-alert message no-close">';
			echo '<strong>No Tags!</strong> Create a tag using the form above.';
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

	foreach ( $tags AS $tag ) :

		$_temp			= new stdClass();
		$_temp->id		= $tag->id;
		$_temp->label	= $tag->label;

		$_data[] = $_temp;

	endforeach;

	echo 'var _DATA = ' . json_encode( $_data ) . ';';
	echo '</script>';

	// --------------------------------------------------------------------------