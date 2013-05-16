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
		Use categories to group broad post topics together. For example, a category might be 'Music', or 'Travel'. For specific details (e.g New Year <?=date( 'Y')?>) consider using a <?=anchor( 'admin/blog/manager_tag' . $_is_fancybox, 'tag' )?>.
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

			echo '<p class="system-alert message no-close">';
			echo '<strong>No Categories!</strong> Create a category using the form above.';
			echo '</p>';

		endif;

	?>
</div>

<?php

	if ( $_is_fancybox && $rebuild ) :

		echo '<script type="text/javascript">';
		echo 'parent.rebuild_select( \'categories\', ' . json_encode( $categories ) . ');';
		echo '</script>';

	endif;