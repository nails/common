<div class="box specific" id="box_employer_edit_notes">

	<h2>
		Notes (Private)
		<a href="#" class="toggle">close</a>
	</h2>

	<div class="container" style="padding:0 12px;">
	
		<table class="blank">
		
			<tr>
				<td align="right" width="170">
					<strong>Account Notes</strong>:
				</td>
				<td>
					<textarea name="private_notes" style="width:98%;height:140px;"><?=set_value( 'private_notes', $employer->private_notes )?></textarea>
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