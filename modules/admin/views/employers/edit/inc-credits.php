<div class="box specific" id="box_employer_edit_credits">

	<h2>
		Package &amp; Credits
		<a href="#" class="toggle">close</a>
	</h2>

	<div class="container" style="padding:3px 12px; 2px;">
		
		<p>This employer currently has <?=$employer->credits?> credits remaining.</p>

		<p><a href="#" class="a-button a-button-small add-credits">Add Credits</a></p>		
		
		<div class="add-credits-box" style="display:none;margin-bottom:10px;">
		
			<p>
				<strong>Number of Credits to Add</strong>:
			</p>
			<p>
				<select name="add_credit">
					<option value="" selected="selected">Please Select...</option>
					<?php for ($i = 5; $i <= 1000; $i+=5) : ?>
						<option value="<?=$i?>"><?=$i?></option>
					<?php endfor; ?>
				</select>
			</p>
		
		</div>
		
		<hr />
		
		<p>
			<strong>Package</strong>:
		</p>
		<p>
		<?php
		
			$_packages = array( '' => 'None' ) + $packages;
			echo form_dropdown( 'package_id', $_packages, $employer->package_id );
			
		?>
		</p>
		
		<hr />
		
		<p>
			<span id="edit_button"><input type="submit" value="Update"></span>
		</p>
		
	</div>
	
</div>