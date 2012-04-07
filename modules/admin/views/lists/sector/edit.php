<?php
	
	$s = $sector[0];
		
?>

<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Manage Sector &rsaquo; Edit Sector</h1>

<p>
	You can edit this sector using the form below. Please note that any modification of this sector will be shown across the website
	and will affect the records shown below.
</p>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;width:300px;">

	<div class="box">
	
		<h2>Edit Sector</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/lists/sector_edit/' . $s->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'title' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error('title');
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Title</strong>*:</td>
						<td>
							<input type="text" name="title" value="<?=$s->title?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Short Title (Optional)</strong>:</td>
						<td>
							<input type="text" name="title_short" value="<?=$s->title_short?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td></td>
						<td><div id="search_warning" style="color:#ff0000;font-weight:bold"></div></td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong></strong></td>
						<td><span id="edit_button"><input type="submit" value="Update"></span></td>
					
					</tr>
				
				</table>
			
			</form>
	
		</div>
	
	</div>

</div>

