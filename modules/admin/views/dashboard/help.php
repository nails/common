<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	$('.drop').click(function(){	
		$(this).next('.drop_content').slideToggle();
	});
	
});
</script>

<h1>Help</h1>

<!--	TABLE FILTERS:	ALL	-->
<section rel="tooltip-n" class="filter-box jsonly" title="Coming soon!">
	<p>
		<label>Search:</label>
		<?=form_input( 'search', NULL, 'disabled style="width:92%" placeholder="Start typing keywords..."' )?>
	</p>
</section>

<?php foreach ( $help AS $h ): ?>
	
	<div class="box" style="margin-left:10px;margin-right:10px;width:auto;">
		
		<h2 class="drop" style="cursor:pointer;"><?=$h->title?> <span class="right"><img src="/application/modules/admin/views/_assets/img/head_slide_down.png"></span><span class="right">#<?=$h->id?>&nbsp;</span></h2>
		
		<div class="drop_content" style="padding: 4px 12px 12px; display:none;" >
		
			<?=$h->content?>
		
		</div>
			
	</div>
	
<?php endforeach; ?>
