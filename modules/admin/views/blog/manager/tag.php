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
		Use tags to group specific post topics together. For example, a tag might be 'New Year <?=date( 'Y')?>', or 'Coursework'. For broader subjects (e.g 'Music' or 'Travel') consider using a <?=anchor( 'admin/blog/manager_category' . $_is_fancybox, 'category' )?>.
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

	if ( $_is_fancybox && $rebuild ) :

		echo '<script type="text/javascript">';
		echo 'parent.rebuild_select( \'tags\', ' . json_encode( $tags ) . ');';
		echo '</script>';

	endif;