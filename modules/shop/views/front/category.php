<div id="shop" class="shopfront">
	<div class="sidebar four columns first">
		<h2 class="sidebar-title">Categories</h2>
		<?=shop_nested_categories_html( $categories, TRUE )?>
	</div>
	<div class="browse twelve columns last">

		<?php

			echo $page->title ? '<h1 class="page-title">' . $page->title . '</h1>' : '';
			echo $page->subtitle ? '<h6 class="page-subtitle">' . $page->subtitle . '</h6>' : '';


		?>
		<ul class="browse">
		<?php

			foreach( $products AS $product ) :

				echo '<li class="product">';
				$this->load->view( 'shop/front/_utilities/template-product-listing', array( 'product' => &$product ) );
				echo '</li>';

			endforeach;

		?>
		</ul>
	</div>
</div>