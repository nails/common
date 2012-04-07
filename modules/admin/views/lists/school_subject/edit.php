<?php
	
	$i = $school_subject->row();
	
?>

<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Manage School Subjects &rsaquo; Edit Subject</h1>

<p>
	You can edit this subject using the form below. Please note that any modification of this subject will be shown across the website
	and will affect the records shown below.
</p>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;width:300px;">

	<div class="box">
	
		<h2>Edit Subject</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/lists/school_subject_edit/' . $i->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if ( form_error( 'name' ) ) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error('name');
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

