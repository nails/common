<h1>Frequently Asked Questions</h1>

<p>
	Manage the site's FAQ's from this page.
	<?=anchor( 'admin/faq/create' , 'New FAQ', 'class="a-button a-button-small right"' )?>
</p>

<hr />

<section class="filter-box">
	<p style="margin:0;padding:0;">
		<form method="get" action="/admin/faq" class="form" style="margin:0;padding:0;width:400px;">
		<label>Search:</label>
		<input type="text" name="search" value="<?php if ( $this->input->get('search') ) : echo $this->input->get('search'); endif;?>">
		<input type="image" src="/assets/app/img/icons/search.png" style="vertical-align:middle">
		</form>
	</p>
</section>

<!--	CUSTOM JS	-->
<script type="text/javascript">
<!--// Hide from old browsers
	
	$(function() {
	
		$('.delete').click(function(){
		
			return confirm( 'This will delete the selected FAQ from the database. This cannot be undone.\n\nContinue?' );
		
		});
	
	});
	
//-->
</script>

<style type="text/css">

	td.options, th.options	{ text-align: center; }

</style>


<hr>

<table>

	<thead>
	
		<tr>
			
			<th>ID</th>
			<th>Slug</th>
			<th>Label</th>
			<th class="options" width="100">Options</th>
		
		</tr>
	
	</thead>
	
	<tbody>
	
		<?php foreach ( $faq AS $f ) : ?>
	
		<tr>
		
			<td><?=number_format( $f->id )?></td>
			<td><?=anchor( 'admin/faq/edit/' . $f->id, $f->slug )?></td>
			<td><?=anchor( 'admin/faq/edit/' . $f->id, $f->label )?></td>
			<td class="options">
				
				<?=anchor( 'admin/faq/edit/' . $f->id, 'Edit', 'class="a-button a-button-small"')?>
				<?=anchor( 'admin/faq/delete/' . $f->id, 'Delete', 'class="a-button a-button-small a-button-red delete"')?>
													
			</td>
		
		</tr>
		
		<?php endforeach; ?>
	
	</tbody>

</table>