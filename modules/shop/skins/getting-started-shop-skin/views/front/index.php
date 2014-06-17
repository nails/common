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

				$_data_available[2]					= new stdClass();
				$_data_available[2]->variable		= 'tags';
				$_data_available[2]->description	= 'An array of the tags containing active items, including count.';

				$_data_available[3]					= new stdClass();
				$_data_available[3]->variable		= 'products_featured';
				$_data_available[3]->description	= 'An array of the featured products.';

				// --------------------------------------------------------------------------

				foreach( $_data_available AS $index => $item ) :

					$this->load->view( $skin->path . 'front/_components/variable', array( 'index' => $index, 'item' => $item ) );

				endforeach;

			?>
			</ul>
			<?php

				if (
					app_setting( 'page_brand_listing', 'shop' ) ||
					app_setting( 'page_category_listing', 'shop' ) ||
					app_setting( 'page_collection_listing', 'shop' ) ||
					app_setting( 'page_range_listing', 'shop' ) ||
					app_setting( 'page_sale_listing', 'shop' ) ||
					app_setting( 'page_tag_listing', 'shop' )
				) :

					echo '<h5>Other Pages</h5>';
					echo '<p>Here are some handy links to other pages handled by the Shop module:</p>';

					echo '<ul class="list-unstyled">';

						echo app_setting( 'page_brand_listing', 'shop' )		? '<li>&rsaquo; ' . anchor( app_setting( 'url', 'shop' ) . 'brand',			'Brand listing page' ) . '</li>'		: '';
						echo app_setting( 'page_category_listing', 'shop' )		? '<li>&rsaquo; ' . anchor( app_setting( 'url', 'shop' ) . 'category',		'Category listing page' ) . '</li>'		: '';
						echo app_setting( 'page_collection_listing', 'shop' )	? '<li>&rsaquo; ' . anchor( app_setting( 'url', 'shop' ) . 'collection',	'Collection listing page' ) . '</li>'	: '';
						echo app_setting( 'page_range_listing', 'shop' )		? '<li>&rsaquo; ' . anchor( app_setting( 'url', 'shop' ) . 'range',			'Range listing page' ) . '</li>'		: '';
						echo app_setting( 'page_sale_listing', 'shop' )			? '<li>&rsaquo; ' . anchor( app_setting( 'url', 'shop' ) . 'sale',			'Sale listing page' ) . '</li>'			: '';
						echo app_setting( 'page_tag_listing', 'shop' )			? '<li>&rsaquo; ' . anchor( app_setting( 'url', 'shop' ) . 'tag',			'Tag listing page' ) . '</li>'			: '';

					echo '</ul>';

				endif;

			?>
		</div>
	</div>
</div>
<?php

	$this->load->view( $skin->path . 'front/_components/css_js' );