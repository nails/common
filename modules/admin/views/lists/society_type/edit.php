<?php
	
	$i = $society_type->row();
	
?>

<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Manage Society Types &rsaquo; Edit Type</h1>

<p>
	You can edit this type using the form below. Please note that any modification of this type will be shown across the website
	and will affect the records shown below.
</p>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;width:300px;">

	<div class="box">
	
		<h2>Edit Type</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/lists/society_type_edit/' . $i->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'type' ) || form_error( 'group' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error('type');
					echo form_error('group');
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Type</strong>*:</td>
						<td>
							<input type="text" name="type" value="<?=$i->type?>">
						</td>
					
					</tr>
						
					<tr>
					
						<td align="right"><strong>Category</strong>*:</td>
						<td>
							<select name="group">
								<option value="">Please Select...</option>
								<option value="sports"<?php if ( $i->group == 'sports' ): echo ' selected="selected"'; endif; ?>>sports</option>
								<option value="music & drama"<?php if ( $i->group == 'music & drama' ): echo ' selected="selected"'; endif; ?>>music & drama</option>
								<option value="arts & language"<?php if ( $i->group == 'arts & language' ): echo ' selected="selected"'; endif; ?>>arts & language</option>
								<option value="military"<?php if ( $i->group == 'military' ): echo ' selected="selected"'; endif; ?>>military</option>
								<option value="political & business"<?php if ( $i->group == 'political & business' ): echo ' selected="selected"'; endif; ?>>political & business</option>
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

