
<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});
</script>


<h1>Internships: Edit</h1>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;">

	<div class="box">
	
		<h2>Edit Internship</h2>
		
			<div style="padding:0 12px;">
							
			<?php if ( $internship->order_id ): ?>		
			
				<div class="notice">Please note, this internship is associated with an intern order, reference <?=anchor( 'admin/orders/edit/' . $internship->order_id, $internship->order_ref );?>.</div>
			
			<?php endif; ?>		
							
			<?=form_open_multipart( 'admin/internships/edit/' . $internship->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if (
					form_error( 'job_title' ) 		||
					form_error( 'job_description' )	||
					form_error( 'company' )			||
					form_error( 'sector_id' )		||
					form_error( 'location' )		||
					form_error( 'post_code' )		||
					form_error( 'date_added' )		||
					form_error( 'date_deadline' )	||
					form_error( 'date_start' )		||
					form_error( 'pay_rate' )		||
					form_error( 'pay_frequency' )	||
					form_error( 'internal' )		) :
				
				
					echo '<div class="error" style="text-align:center">';
					echo form_error('job_title');
					echo form_error('job_description');
					echo form_error('company');
					echo form_error('sector_id');
					echo form_error('location');
					echo form_error('post_code');
					echo form_error('date_added');
					echo form_error('date_deadline');
					echo form_error('date_start');
					echo form_error('pay_rate');
					echo form_error('pay_frequency');
					echo form_error('internal');
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Title</strong>*:</td>
						<td>
							<input type="text" name="job_title" value="<?=$internship->job_title?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Description</strong>*:</td>
						<td>
							<textarea name="job_description" style="height:100px;width:300px;"><?php if ( $this->input->post('job_description') ) : echo $this->input->post('job_description'); else: echo $internship->job_description; endif;?></textarea>
						</td>
					
					</tr>
										
					<tr>
					
						<td align="right"><strong>Employer</strong>*:</td>
						<td>
							<select name="company">
								<option value="">Please Select...</option>
								<?php foreach($employers AS $e): ?>
									<option value="<?=$e->id?>"<?php if ($e->id==$internship->company): echo ' selected="selected"'; endif; ?>><?=$e->name?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>

					<tr>
					
						<td align="right"><strong>Location</strong>*:</td>
						<td>
							<select name="location">
								<option value="">Please Select...</option>
								<?php foreach($locations AS $id => $name): ?>
									<option value="<?=$id?>"<?php if ( $id == $internship->location ): echo ' selected="selected"'; endif; ?>><?=$name?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Sector</strong>*:</td>
						<td>
							<select name="sector_id">
								<option value="">Please Select...</option>
								<?php foreach($sectors AS $s): ?>
									<option value="<?=$s->id?>"<?php if ($s->id==$internship->sector_id): echo ' selected="selected"'; endif; ?>><?=$s->title?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>

					<tr>
					
						<td align="right"><strong>Post Code</strong>*:</td>
						<td>
							<input type="text" name="post_code" value="<?=$internship->post_code?>">
						</td>
					
					</tr>	
					
					<tr>
					
						<td align="right"><strong>Date Added</strong>*:</td>
						<td>
							<input type="text" name="date_added" value="<?=$internship->date_added?>">
						</td>
					
					</tr>	

					<tr>
					
						<td align="right"><strong>Start Date</strong>*:</td>
						<td>
							<input type="text" name="date_start" value="<?=$internship->date_start?>">
						</td>
					
					</tr>	
					
					<tr>
					
						<td align="right"><strong>Date Deadline</strong>*:</td>
						<td>
							<input type="text" name="date_deadline" value="<?=$internship->date_deadline?>">
						</td>
					
					</tr>	
					
					<tr>
					
						<td align="right"><strong>Duration</strong>*:</td>
						<td>
							<input type="text" name="duration" value="<?=$internship->duration?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Duration Term</strong>*:</td>
						<td>
							<select name="duration_term">
								<option value="">Please Select...</option>
								<?php foreach($duration_terms AS $p): ?>
									<option value="<?=$p->id?>"<?php if ($p->id==$internship->duration_term): echo ' selected="selected"'; endif; ?>><?=$p->rate?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>

					<tr>
					
						<td align="right"><strong>Internal / External</strong>*:</td>
						<td>
							<select name="internal">
								<option value="1"<?php if ($internship->internal==1): echo ' selected="selected"'; endif; ?>>Internal</option>
								<option value="2"<?php if ($internship->internal==2): echo ' selected="selected"'; endif; ?>>External</option>
							</select>
						</td>
					
					</tr>

					<tr>
					
						<td align="right"><strong>Rate of Pay &pound;</strong>*:</td>
						<td>
							<input type="text" name="pay_rate" value="<?=$internship->pay_rate?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Pay Frequency</strong>*:</td>
						<td>
							<select name="pay_frequency">
									<option value="">Please Select...</option>
								<?php foreach($pay_frequencies AS $p): ?>
									<option value="<?=$p->id?>"<?php if ($p->id==$internship->pay_frequency): echo ' selected="selected"'; endif; ?>><?=$p->rate?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>
					
					<tr>
					
						<td colspan="2">
							<hr>
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Active</strong>*:</td>
						<td>
							<select name="active">
								<option value=""<?php if ($internship->active==''): echo ' selected="selected"'; endif; ?>>No</option>
								<option value="1"<?php if ($internship->active==1): echo ' selected="selected"'; endif; ?>>Yes</option>
							</select>
						</td>
					
					</tr>
						
					
					<tr>
					
						<td colspan="2">
							<hr>
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

