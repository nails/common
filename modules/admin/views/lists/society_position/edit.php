<?php
	
	$i = $society_position->row();
	
?>

<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Manage Society Positions &rsaquo; Edit Position</h1>

<p>
	You can edit this position using the form below. Please note that any modification of this position will be shown across the website
	and will affect the records shown below.
</p>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;width:300px;">

	<div class="box">
	
		<h2>Edit Position</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/lists/society_position_edit/' . $i->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'position' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error( 'position' );
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Position</strong>*:</td>
						<td>
							<input type="text" name="position" value="<?=$i->position?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Description</strong>:</td>
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

