<ul class="sidebar four columns last">
<?php

	if ( $widget->latest_posts ) :

		echo '<li class="widget latest-posts">';
		echo $widget->latest_posts;
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	if ( $widget->categories ) :

		echo '<li class="widget latest-posts">';
		echo $widget->latest_posts;
		echo '</li>';

	endif;

	// --------------------------------------------------------------------------

	if ( $widget->tags ) :

		echo '<li class="widget latest-posts">';
		echo $widget->latest_posts;
		echo '</li>';

	endif;


	?>
	<!-- <li class="widget categories">
		<h5>Categories</h5>
		<ul>
			<li>&rsaquo; <a href="#">Investment Banking</a></li>
			<li>&rsaquo; <a href="#">Private Equity</a></li>
			<li>&rsaquo; <a href="#">Venture Capital</a></li>
		</ul>
	</li>
	<li class="widget tags">
		<h5>Tags</h5>
		<ul class="clearfix">
			<li><a href="#">Interviews</a></li>
			<li><a href="#">Preparation</a></li>
			<li><a href="#">Current Affairs</a></li>
		</ul>
	</li> -->
</ul>