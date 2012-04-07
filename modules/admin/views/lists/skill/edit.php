<?php
	
	$i = $skill->row();
	
?>

<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Manage Skills &rsaquo; Edit Skill</h1>

<p>
	You can edit this skill using the form below. Please note that any modification of this skill will be shown across the website
	and will affect the records shown below.
</p>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;width:300px;">

	<div class="box">
	
		<h2>Edit Skill</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/lists/skill_edit/' . $i->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'skill' ) || form_error( 'category' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error('skill');
					echo form_error('category');
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Skill</strong>*:</td>
						<td>
							<input type="text" name="skill" value="<?=$i->skill?>">
						</td>
					
					</tr>
						
					<tr>
					
						<td align="right"><strong>Category</strong>*:</td>
						<td>
							<select name="category">
								<option value="">Please Select...</option>
								<?php
									foreach ( $skill_categories->result() AS $ll ) :
										$selected = '';
										if ( $i->category == $ll->id ):
											$selected = ' selected="selected"';
										endif;
										echo '<option value="'.$ll->id.'"'.$selected.'>'.$ll->category.'</option>';
									endforeach;
								?>
							</select>
						</td>
					
					</tr>
					
										
					<tr>
					
						<td align="right"><strong>Active?</strong>:</td>
						<td>
							<input type="checkbox" name="active"<?php if ( $i->active==1 ) : echo ' checked="checked"'; endif;?>>
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

