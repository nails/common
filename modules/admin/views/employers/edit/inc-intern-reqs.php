<div class="box specific" id="box_employer_edit_intern-reqs">

	<h2>
		Intern Requirements
		<a href="#" class="toggle">close</a>
	</h2>
	
	<div class="container" style="padding:0 12px;">
					
		<table class="blank">
		
			<tr>
				<td align="right">
					<strong>Company Size</strong>:
				</td>
				<td>
				
					<?php
					
						$size_options = array(
							''		=> 'Please Select...',
							'1'	=> 'Small: Less than 20 Employees',
							'2'	=> 'Medium: Between 20-100 Employees',
							'3'	=> 'Large: Over 100 Employees'
						);
							
					?>
				
					<?=form_dropdown('company_size', $size_options, $employer->company_size)?>
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Recruitment Timings</strong>:
				</td>
				<td>
					<?php
				
						$timing_options = array(
								''		=> 'Please Select...',
								'1'	=> 'Year Round',
								'2'	=> 'Summer',
								'3'	=> 'Autumn',
								'4'	=> 'Spring',
								'5'	=> 'Winter',
								'6'	=> 'Irregularly'
							);	
					
					?>
					<?=form_dropdown('annual_intern_timings', $timing_options, $employer->annual_intern_timings)?>			
				
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Education Level of Interns</strong>:
				</td>
				<td>
					
					<?php
				
						$employerducation_options = array(
							''		=> 'Please Select...',
							'1'	=> 'Student',
							'2'	=> 'Student with Work Experience',
							'3'	=> 'Graduate',
							'4'	=> 'Graduate with Work Experience',
							'5'	=> 'Professional / Experienced',
							'6'	=> 'Not important'
						);
					
					?>	
					<?=form_dropdown('education_level', $employerducation_options, $employer->education_level)?>
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Annual Intern Usage</strong>:
				</td>
				<td>
					<input type="text" name="annual_intern_usage" value="<?=set_value( 'annual_intern_usage', $employer->annual_intern_usage )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Typical Salary</strong>:
				</td>
				<td>
					<input type="text" name="typical_salary" value="<?=set_value( 'typical_salary', $employer->typical_salary )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Typical Duration</strong>:
				</td>
				<td>
					<input type="text" name="typical_duration" value="<?=set_value( 'typical_duration', $employer->typical_duration )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Purpose of Account</strong>:
				</td>
				<td>
					<input type="text" name="why" value="<?=set_value( 'why', $employer->why )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Assessment Process</strong>:
				</td>
				<td>
					<input type="text" name="assessment" value="<?=set_value( 'assessment', $employer->assessment )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Future Interest?</strong>:
				</td>
				<td>
					<input type="text" name="interest" value="<?=set_value( 'interest', $employer->interest )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Recruitment Budget</strong>:
				</td>
				<td>
					<input type="text" name="annual_recruitment_budget" value="<?=set_value( 'annual_recruitment_budget', $employer->annual_recruitment_budget )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Staff Hours Spent Recruiting</strong>:
				</td>
				<td>
					<input type="text" name="staff_time" value="<?=set_value( 'staff_time', $employer->staff_time )?>" />
				</td>
			</tr>
			
			
			<tr>
				<td colspan="2"><hr style="margin:4px 0 6px 0;"></td>
			</tr>
			<tr>
				<td></td>
				<td><span id="edit_button"><input type="submit" value="Update"></span></td>
			</tr>
	
		</table>
	
	</div>
	
</div>			