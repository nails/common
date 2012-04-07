<?php
	
	$l = $location->row();
	
?>

<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Manage Locations &rsaquo; Edit Location</h1>

<p>
	You can edit this location using the form below. Please note that any modification of this location will be shown across the website
	and will affect the records shown below. Please exercise extreme caution when changing the parents of locations!
</p>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;width:300px;">

	<div class="box">
	
		<h2>Edit Location</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/lists/location_edit/' . $l->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'title' ) || form_error( 'parent' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error('title');
					echo form_error('parent');
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Title</strong>*:</td>
						<td>
							<input type="text" name="title" value="<?=$l->title?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Parent</strong>*:</td>
						<td>
							<select name="parent">
								<option value="">Please Select...</option>
								<?php
									foreach ( $locations->result() AS $ll ) :
										$selected='';
										if ( $ll->id == $l->parent ) :
											$selected=' selected="selected"';
										endif;
										echo '<option value="'.$ll->id.'"'.$selected.'>'.$ll->title.'</option>';
									endforeach;
								?>
							</select>
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

