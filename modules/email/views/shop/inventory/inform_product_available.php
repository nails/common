<p>
	You have received this email because you asked us to inform you
	when <strong><?=$product->label?></strong> was back in stock.
</p>
<p class="text-center">
	<?=anchor( $product->url, 'Click here to view this item at ' . APP_NAME, 'class="button"' )?>
</p>
<hr />
<?php

	if ( $product->featured_img ) :

		echo '<p class="text-center">';
			echo img( array( 'src' => cdn_scale( $product->featured_img, 250, 250  ), 'class' => 'thumbnail ' ) );
		echo '</p>';

	endif;

	echo '<p class="text-center">';
		echo '<strong>' . $product->label . '</strong>';
	echo '</p>';

	echo $product->description;
