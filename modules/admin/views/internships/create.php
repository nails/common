<!-- jQuery -->
<script type="text/javascript" charset="utf-8">
$(function () 
{

	
});


</script>


<h1>Internships: Create New External Internship</h1>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;">

	<div class="box">
	
		<h2>Edit Internship</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/internships/create/' )?>
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
							<input type="text" name="job_title" value="">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Description</strong>*:</td>
						<td>
							<textarea name="job_description" style="height:100px;width:300px;"></textarea>
						</td>
					
					</tr>
										
					<tr>
					
						<td align="right"><strong>Employer</strong>*:</td>
						<td>
							<select name="company">
								<option value="">Please Select...</option>
								<?php foreach($employers->result() AS $e): ?>
									<option value="<?=$e->id?>"><?=$e->name?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>

					<tr>
					
						<td align="right"><strong>Location</strong>*:</td>
						<td>
							<select name="location">
								<option value="">Please Select...</option>
								<?php foreach($locations->result() AS $l): ?>
									<option value="<?=$l->id?>"><?=$l->title?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Sector</strong>*:</td>
						<td>
							<select name="sector_id">
								<option value="">Please Select...</option>
								<?php foreach($sectors->result() AS $s): ?>
									<option value="<?=$s->id?>"><?=$s->title?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>

					<tr>
					
						<td align="right"><strong>Post Code</strong>*:</td>
						<td>
							<input type="text" name="post_code" value="">
						</td>
					
					</tr>	
					
					<tr>
					
						<td align="right"><strong>Date Added</strong>*:</td>
						<td>
							<input type="text" name="date_added" value="" placeholder="YYYY-MM-DD">
						</td>
					
					</tr>	

					<tr>
					
						<td align="right"><strong>Start Date</strong>*:</td>
						<td>
							<input type="text" name="date_start" value="" placeholder="YYYY-MM-DD">
						</td>
					
					</tr>	

					<tr>
					
						<td align="right"><strong>Date Deadline</strong>*:</td>
						<td>
							<input type="text" name="date_deadline" value="" placeholder="YYYY-MM-DD">
						</td>
					
					</tr>
					

					<tr>
					
						<td align="right"><strong>Rate of Pay &pound;</strong>*:</td>
						<td>
							<input type="text" name="pay_rate" value="">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Pay Frequency</strong>*:</td>
						<td>
							<select name="pay_frequency">
								<?php foreach($pay_frequencies->result() AS $p): ?>
									<option value="<?=$p->id?>"><?=$p->rate?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Duration</strong>*:</td>
						<td>
							<input type="text" name="duration" value="">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Duration Term</strong>*:</td>
						<td>
							<select name="duration_term">
								<option value="">Please Select...</option>
								<?php foreach($duration_terms->result() AS $p): ?>
									<option value="<?=$p->id?>"><?=$p->rate?></option>
								<?php endforeach ?>
							</select>
						</td>
					
					</tr>

					<tr>
					
						<td align="right"><strong>Internal / External</strong>*:</td>
						<td>
							<select name="internal">
								<option value="1">Internal</option>
								<option value="2">External</option>
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
								<option value="">No</option>
								<option value="1">Yes</option>
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
						<td><span id="edit_button"><input type="submit" value="Create"></span></td>
					
					</tr>
				
				</table>
			
			</form>
	
		</div>
	
	</div>

</div>

