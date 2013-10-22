<div class="group-shop manage index">
	<p class="system-alert no-close">
		Choose which Manager you'd like to utilise.
	</p>
	<ul>
		<li><?=anchor( 'admin/shop/manage/attributes?' . $_SERVER['QUERY_STRING'], 'Attributes' )?></li>
		<li><?=anchor( 'admin/shop/manage/brands?' . $_SERVER['QUERY_STRING'], 'Brands' )?></li>
		<li><?=anchor( 'admin/shop/manage/categories?' . $_SERVER['QUERY_STRING'], 'Categories' )?></li>
		<li><?=anchor( 'admin/shop/manage/collections?' . $_SERVER['QUERY_STRING'], 'Collections' )?></li>
		<li><?=anchor( 'admin/shop/manage/ranges?' . $_SERVER['QUERY_STRING'], 'Ranges' )?></li>
		<li><?=anchor( 'admin/shop/manage/tags?' . $_SERVER['QUERY_STRING'], 'Tags' )?></li>
		<li><?=anchor( 'admin/shop/manage/tax_rates?' . $_SERVER['QUERY_STRING'], 'Tax Rates' )?></li>
		<li><?=anchor( 'admin/shop/manage/types?' . $_SERVER['QUERY_STRING'], 'Product Types' )?></li>
	</ul>
</div>