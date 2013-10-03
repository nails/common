<a href="#">
	<?php

		if ( isset( $product->variations[0]->gallery[0] ) ) :

			echo '<div class="image" style="background:url(' . cdn_thumb( $product->variations[0]->gallery[0], 200, 200 ) . ')"></div>';

		endif;


	?>
	<p class="title"><?=$product->title?></p>
	<p class="excerpt"><?=$product->description_short?></p>
	<div class="footer">
		<p class="basket">Add to Basket</p>
		<p class="price">&pound;1,450</p>
		<div class="clearfix"></div>
	</div>
</a>