<?php

	//	New Year?
	if ( $_year != date( 'Y', strtotime( $post->published ) ) )  :

		echo '<li class="year">';
			$this->load->view( 'blog/_components/archive_year', array( 'post' => &$post, '_year' => &$_year ) );
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	New Month?
	if ( $_month != date( 'm', strtotime( $post->published ) ) )  :

		echo '<li class="month">';
			$this->load->view( 'blog/_components/archive_month', array( 'post' => &$post, '_month' => &$_month ) );
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	//	Post
	echo '<li class="post">';
		$this->load->view( 'blog/_components/archive_post', array( 'post' => &$post ) );
	echo '</li>';