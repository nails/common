<div class="blog single container">
<?php

	echo '<ul class="posts ' . BS_COL_MD_9 . ' ' . BS_COL_MD_PUSH_3 . ' list-unstyled">';

		echo '<li class="post clearfix">';
			$this->load->view( 'blog/_components/single' );
		echo '</li>';

	echo '</ul>';

	$this->load->view( 'blog/_components/sidebar' );

?>
</div>