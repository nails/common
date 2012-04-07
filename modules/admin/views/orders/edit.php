<h1>Orders: Edit</h1>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;">

	<div class="box">
	
		<h2>Edit Order</h2>
		
			<div style="padding:0 12px;">
														
			<?=form_open_multipart( 'admin/orders/edit/' . $order->id . '/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if (
					form_error( 'employer_id' ) 	||
					form_error( 'user_id' )			||
					form_error( 'internship_id' )	||
					form_error( 'date_created' )	||
					form_error( 'cost' )			||
					form_error( 'ref' )				||
					form_error( 'status' )			
					) :
				
					echo '<div class="error" style="text-align:center">';
					echo form_error('employer_id');
					echo form_error('user_id');
					echo form_error('internship_id');
					echo form_error('date_created');
					echo form_error('cost');
					echo form_error('ref');
					echo form_error('status');
					echo $error;
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:auto;">
				
					<tr>
					
						<td align="right"><strong>Employer</strong>*:</td>
						<td>
							<input type="text" name="employer_id" value="<?=$order->employer_id?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>User</strong>*:</td>
						<td>
							<input type="text" name="user_id" value="<?=$order->user_id?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Internship</strong>*:</td>
						<td>
							<input type="text" name="internship_id" value="<?=$order->internship_id?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Date Created</strong>*:</td>
						<td>
							<input type="text" name="date_created" value="<?=$order->date_created?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Cost</strong>*:</td>
						<td>
							<input type="text" name="cost" value="<?=$order->cost?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Reference</strong>*:</td>
						<td>
							<input type="text" name="ref" value="<?=$order->ref?>">
						</td>
					
					</tr>
					
					<tr>
					
						<td colspan="2">
							<hr>
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Status</strong>*:</td>
						<td>
							<select name="status">
								<option value="1"<?php if ($order->status==1): echo ' selected="selected"'; endif; ?>>Pending</option>
								<option value="2"<?php if ($order->status==2): echo ' selected="selected"'; endif; ?>>Processing</option>
								<option value="3"<?php if ($order->status==3): echo ' selected="selected"'; endif; ?>>Closing</option>
								<option value="4"<?php if ($order->status==4): echo ' selected="selected"'; endif; ?>>Closed</option>
								<option value="5"<?php if ($order->status==5): echo ' selected="selected"'; endif; ?>>Ready</option>
								<option value="6"<?php if ($order->status==6): echo ' selected="selected"'; endif; ?>>Complete</option>
								<option value="7"<?php if ($order->status==7): echo ' selected="selected"'; endif; ?>>Declined</option>
								<option value="8"<?php if ($order->status==8): echo ' selected="selected"'; endif; ?>>Cancelled</option>
								<option value=""<?php if ($order->status==NULL): echo ' selected="selected"'; endif; ?>>Unknown / NULL</option>
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

