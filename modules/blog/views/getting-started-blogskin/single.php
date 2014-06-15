<div class="container">
	<div class="row">
		<div class="jumbotron <?=BS_COL_LG_10?> <?=BS_COL_LG_OFFSET_1?>">
			<h3 class="text-center">
				Something on your mind?
			</h3>
			<h4 class="text-center">
				You've come to the right place, but it's a little empty round here.
			</h4>
			<hr />
			<p>
				This view renders a single blog post.
			<hr />
			<h5>
				Available Data
			</h5>
			<ul class="list-group">
			<?php

				$_data_available					= array();
				$_data_available[0]					= new stdClass();
				$_data_available[0]->variable		= 'skin';
				$_data_available[0]->description	= 'The skin\'s config.';

				$_data_available[1]					= new stdClass();
				$_data_available[1]->variable		= 'post';
				$_data_available[1]->description	= 'An array of the posts.';

				// --------------------------------------------------------------------------

				foreach( $_data_available AS $index => $item ) :

					$this->load->view( $skin->path . '_utilities/variable', array( 'index' => $index, 'item' => $item ) );

				endforeach;

			?>
			</ul>
		</div>
	</div>
</div>
<?php

	$this->load->view( $skin->path . '_utilities/css_js' );
