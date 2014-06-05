<div class="container">
	<div class="row">
		<div class="jumbotron <?=BS_COL_LG_10?> <?=BS_COL_LG_OFFSET_1?>">
			<h3 class="text-center">
				So, you want to sell some stuff?
			</h3>
			<h4 class="text-center">
				You've come to the right place, but it's a little empty round here.
			</h4>
			<hr />
			<p>
				The Shop module will set everything up for you and handle the
				checkout process etc, but it's up to you, beautiful developer, to
				make it shine.
			</p>
			<p>
				This is the shop's front page, the window if you will. This page
				should be used to showcase featured products, categories, sales,
				collections and ranges. Think sexy.
			</p>
			<hr />
			<p>
				You'll want to override this view in your app by placing a view here:
			</p>
			<?php

				echo '<pre>';
				echo str_replace( NAILS_PATH, FCPATH . APPPATH , __FILE__ );
				echo '</pre>';

			?>
			<h5>
				Available Data
			</h5>
			<ul class="list-group">
			<?php

				$_data_available					= array();
				$_data_available[0]					= new stdClass();
				$_data_available[0]->variable		= 'categories';
				$_data_available[0]->description	= 'An array of the categories containing active items, including count.';

				$_data_available[1]					= new stdClass();
				$_data_available[1]->variable		= 'tags';
				$_data_available[1]->description	= 'An array of the tags containing active items, including count.';

				$_data_available[2]					= new stdClass();
				$_data_available[2]->variable		= 'products_featured';
				$_data_available[2]->description	= 'An array of the featured products.';

				// --------------------------------------------------------------------------

				foreach( $_data_available AS $index => $item ) :

					$this->load->view( 'shop/front/_utilities/variable', array( 'index' => $index, 'item' => $item ) );

				endforeach;

			?>
			</ul>
			<?php

				if (
					shop_setting( 'page_brand_listing') ||
					shop_setting( 'page_category_listing') ||
					shop_setting( 'page_collection_listing') ||
					shop_setting( 'page_range_listing') ||
					shop_setting( 'page_sale_listing') ||
					shop_setting( 'page_tag_listing')
				) :

					echo '<h5>Other Pages</h5>';
					echo '<p>Here are some handy links to other pages handled by the Shop module:</p>';

					echo '<ul class="list-unstyled">';

						echo shop_setting( 'page_brand_listing' )		? '<li>&rsaquo; ' . anchor( shop_setting( 'shop_url' ) . 'brand',		'Brand listing page' ) . '</li>'		: '';
						echo shop_setting( 'page_category_listing' )	? '<li>&rsaquo; ' . anchor( shop_setting( 'shop_url' ) . 'category',	'Category listing page' ) . '</li>'		: '';
						echo shop_setting( 'page_collection_listing' )	? '<li>&rsaquo; ' . anchor( shop_setting( 'shop_url' ) . 'collection',	'Collection listing page' ) . '</li>'	: '';
						echo shop_setting( 'page_range_listing' )		? '<li>&rsaquo; ' . anchor( shop_setting( 'shop_url' ) . 'range',		'Range listing page' ) . '</li>'		: '';
						echo shop_setting( 'page_sale_listing' )		? '<li>&rsaquo; ' . anchor( shop_setting( 'shop_url' ) . 'sale',		'Sale listing page' ) . '</li>'			: '';
						echo shop_setting( 'page_tag_listing' )			? '<li>&rsaquo; ' . anchor( shop_setting( 'shop_url' ) . 'tag',			'Tag listing page' ) . '</li>'			: '';

					echo '</ul>';

				endif;

			?>
		</div>
	</div>
</div>
<?php

	$this->load->view( 'shop/front/_utilities/css_js' );