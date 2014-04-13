<div class="blog single container">
<?php

	echo '<ul class="posts col-md-9 col-md-push-3 list-unstyled">';

		echo '<li class="post clearfix">';
			$this->load->view( 'blog/_components/single' );
		echo '</li>';

	echo '</ul>';

	$this->load->view( 'blog/_components/sidebar' );

?>
</div>