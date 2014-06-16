<div class="container">
	<div class="row">
		<div class="jumbotron <?=BS_COL_LG_10?> <?=BS_COL_LG_OFFSET_1?>">
			<h3 class="text-center">
				Categories
			</h3>
			<h4 class="text-center">
				For him? For her?
			</h4>
			<hr />
			<p>
				This page is shown when no particular category is requested. It should probably
				list all the categories available in your store; good for SEO, maybe.
			</p>
			<p>
				This page can be enabled/disabled in Shop Settings.
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
				$_data_available[1]->variable		= 'categories';
				$_data_available[1]->description	= 'An array of the categories containing active items, including count.';

				// --------------------------------------------------------------------------

				foreach( $_data_available AS $index => $item ) :

					$this->load->view( $skin->path . 'front/_utilities/variable', array( 'index' => $index, 'item' => $item ) );

				endforeach;

			?>
			</ul>
			<?php

				if ( ! empty( ${$_data_available[1]->variable}[0]->slug ) ) :

					echo '<h5>Other Pages</h5>';
					echo '<p>Here are some handy links to other pages handled by the Shop module:</p>';

					echo '<ul class="list-unstyled">';

						echo '<li>&rsaquo; ' . anchor( $this->shop_category_model->format_url( ${$_data_available[1]->variable}[0]->slug ), 'Single Category page' ) . '</li>';

					echo '</ul>';

				endif;

			?>
		</div>
	</div>
</div>
<?php

	$this->load->view( $skin->path . 'front/_utilities/css_js' );