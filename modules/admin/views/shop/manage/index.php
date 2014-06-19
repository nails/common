<div class="group-shop manage index">
	<p class="<?=$is_fancybox ? 'system-alert' : ''?>">
		Choose which Manager you'd like to utilise.
	</p>
	<?=$is_fancybox ? '' : '<hr />'?>
	<ul>
		<li><?=anchor( 'admin/shop/manage/attribute' . $is_fancybox, 'Attributes' )?></li>
		<li><?=anchor( 'admin/shop/manage/brand' . $is_fancybox, 'Brands' )?></li>
		<li><?=anchor( 'admin/shop/manage/category' . $is_fancybox, 'Categories' )?></li>
		<li><?=anchor( 'admin/shop/manage/collection' . $is_fancybox, 'Collections' )?></li>
		<li><?=anchor( 'admin/shop/manage/range' . $is_fancybox, 'Ranges' )?></li>
		<li><?=anchor( 'admin/shop/manage/tag' . $is_fancybox, 'Tags' )?></li>
		<li><?=anchor( 'admin/shop/manage/tax_rate' . $is_fancybox, 'Tax Rates' )?></li>
		<li><?=anchor( 'admin/shop/manage/product_type' . $is_fancybox, 'Product Types' )?></li>
	</ul>
</div>