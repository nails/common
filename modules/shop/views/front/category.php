<div class="shopfront">
	<div class="sidebar">
		<div class="categories">
			<h2>Categories</h2>
			<ul>
			<?php

				echo shop_nested_categories_html( $categories, TRUE );

			?>
			</ul>
		</div>
	</div>
	<div class="items">
		<ul>
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