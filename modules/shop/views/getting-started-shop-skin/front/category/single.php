<div class="container">
	<div class="row">
		<div class="jumbotron <?=BS_COL_LG_10?> <?=BS_COL_LG_OFFSET_1?>">
			<h3 class="text-center">
				A single Category
			</h3>
			<h4 class="text-center">
				Just one: <?=$category->label?>
			</h4>
			<hr />
			<p>
				This is the category's homepage, it should list all the products which feature this category.
			</p>
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
				$_data_available[1]->variable		= 'category';
				$_data_available[1]->description	= 'The category object';

				$_data_available[2]					= new stdClass();
				$_data_available[2]->variable		= 'products';
				$_data_available[2]->description	= 'An array of products which feature this category.';

				// --------------------------------------------------------------------------

				foreach( $_data_available AS $index => $item ) :

					$this->load->view( $skin->path . 'front/_utilities/variable', array( 'index' => $index, 'item' => $item ) );

				endforeach;

			?>
			</ul>
		</div>
	</div>
</div>
<?php

	$this->load->view( $skin->path . 'front/_utilities/css_js' );