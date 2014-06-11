<li class="list-group-item">
	<p class="data-switch" id="data-<?=$index?>">
		<code>$<?=$item->variable?></code>
		<em>&nbsp;&mdash;&nbsp; <?=$item->description?></em>
	</p>
	<?php

		echo '<pre class="data-render" id="data-' . $index . '-pre">';
		echo print_r( ${$item->variable}, TRUE );
		echo '</pre>';

	?>
</li>