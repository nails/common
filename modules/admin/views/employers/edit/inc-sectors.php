<div class="box specific" id="box_employer_edit_sectors">

	<h2>
		Sectors
		<a href="#" class="toggle">close</a>
	</h2>
	
	<div class="container" style="padding:0 12px;">
					
		<table class="blank" style="">
			<tr>
				<td>
					<?php
						$employers_arr = array();
						if ( $employer->sectors ) :
							foreach ( $employer->sectors AS $employers ) :
								$employers_arr[$employers->sectors_id] = $employers->title;
							endforeach;
						endif;
					?>
				
					<?php foreach ( $sectors AS $s ) : ?>
					
						<?php
							$checked = FALSE;
							if ( array_key_exists( $s->id, $employers_arr ) )
								$checked = TRUE;
						?>
						<?=form_checkbox('sector[]', $s->id, $checked);?>
						<?=$s->title?><br>
					
					<?php endforeach; ?>
				
				</td>
			</tr>
			<tr>
				<td><hr style="margin:4px 0 6px 0;"></td>
			</tr>
			<tr>
				<td><span id="edit_button"><input type="submit" value="Update"></span></td>
			</tr>
				
		</table>
		
	</div>

</div>