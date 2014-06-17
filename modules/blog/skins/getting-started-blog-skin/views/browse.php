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
				This view renders the list of posts. It can be viewed from the default context, a category context or a tag context.
			</p>
			<hr />
			<h5>
				Available Data
			</h5>
			<ul class="list-group">
			<?php

				$_data_available					= array();

				$_var				= new stdClass();
				$_var->variable		= 'skin';
				$_var->description	= 'The skin\'s config.';
				$_data_available[]	= $_var;

				$_var				= new stdClass();
				$_var->variable		= 'posts';
				$_var->description	= 'An array of the posts.';
				$_data_available[]	= $_var;

				$_var				= new stdClass();
				$_var->variable		= 'pagination';
				$_var->description	= 'Pagination data';
				$_data_available[]	= $_var;

				$_var				= new stdClass();
				$_var->variable		= 'widget';
				$_var->description	= 'Sidebar widgets';
				$_data_available[]	= $_var;

				if ( ! empty( $archive_title ) ) :

					$_var				= new stdClass();
					$_var->variable		= 'archive_title';
					$_var->description	= 'Title of the "archive" page currently being viewed. Archive pages are produced when viewing categories &amp; tags.';
					$_data_available[]	= $_var;

				endif;

				if ( ! empty( $category ) ) :

					$_var				= new stdClass();
					$_var->variable		= 'category';
					$_var->description	= 'The currently viewed category.';
					$_data_available[]	= $_var;

				endif;

				if ( ! empty( $tag ) ) :

					$_var				= new stdClass();
					$_var->variable		= 'tag';
					$_var->description	= 'The currently viewed tag.';
					$_data_available[]	= $_var;

				endif;

				// --------------------------------------------------------------------------

				foreach( $_data_available AS $index => $item ) :

					$this->load->view( $skin->path . 'views/_components/variable', array( 'index' => $index, 'item' => $item ) );

				endforeach;

			?>
			</ul>
		</div>
	</div>
</div>
<?php

	$this->load->view( $skin->path . 'views/_components/css_js' );
