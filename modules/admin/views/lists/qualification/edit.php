<?php
	
	$i = $qualification->row();
	
?>

<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Manage Qualifications &rsaquo; Edit Qualification</h1>

<p>
	You can edit this qualification using the form below. Please note that any modification of this qualification will be shown across the website
	and will affect the records shown below.
</p>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;width:300px;">

	<div class="box">
	
		<h2>Edit Qualification</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/lists/qualification_edit/' . $i->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'name' ) || form_error( 'description' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error( 'name' );
					echo form_error( 'description' );
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Name</strong>*:</td>
						<td>
							<input type="text" name="name" value="<?=$i->name?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Description</strong>*:</td>
						<td>
							<input type="text" name="description" value="<?=$i->description?>">
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

