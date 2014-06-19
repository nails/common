<div class="group-shop manage index">
	<p class="<?=$is_fancybox ? 'system-alert' : ''?>">
		Choose which Manager you'd like to utilise.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul>
		<li><?=anchor( 'admin/shop/manage/attributes' . $is_fancybox, 'Attributes' )?></li>
		<li><?=anchor( 'admin/shop/manage/brands' . $is_fancybox, 'Brands' )?></li>
		<li><?=anchor( 'admin/shop/manage/categories' . $is_fancybox, 'Categories' )?></li>
		<li><?=anchor( 'admin/shop/manage/collections' . $is_fancybox, 'Collections' )?></li>
		<li><?=anchor( 'admin/shop/manage/ranges' . $is_fancybox, 'Ranges' )?></li>
		<li><?=anchor( 'admin/shop/manage/tags' . $is_fancybox, 'Tags' )?></li>
		<li><?=anchor( 'admin/shop/manage/tax_rates' . $is_fancybox, 'Tax Rates' )?></li>
		<li><?=anchor( 'admin/shop/manage/types' . $is_fancybox, 'Product Types' )?></li>
	</ul>
</div>