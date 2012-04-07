<div class="box specific" id="box_edit_basic_info">

	<h2>
		Basic Information
		<a href="#" class="toggle">close</a>
	</h2>
	
	<div class="container" style="padding:0 12px;">
					
		<table class="blank" style="width:625px">
		
			<tr>
				<td align="right">
					<label>Account Type:</label>
				</td>
				<td>	
			
					<select name="group_id" class="form_select">
						<?php $checked = ($user_edit->group_id == 0)? TRUE : FALSE ?>
						<option value="-1">---</option>
						
						<?php foreach ($groups AS $g) : ?>
							<?php $checked = ($user_edit->group_id == $g->id)? TRUE : FALSE ?>
							<option value="<?=$g->id?>" <?=set_select('group_id', $g->id, $checked)?>><?=title_case( str_replace( '_', ' ', $g->name ) )?></option>
						<?php endforeach; ?>
					</select>
					
				</td>
			</tr>

			<tr>
				<td colspan="2"><hr style="margin:4px 0 6px 0;"></td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Reset Password:</label>
				</td>
				<td>	
					<input type="checkbox" class="reset_pass_check" name="reset_pass" value="1" <?=set_checkbox('reset_pass', '1', FALSE)?>/>
					<span class="define_password_span" style="color:#aaa;display:none;">Type password to use below</span>
				</td>
			</tr>
			
			<?php $display = (set_checkbox('reset_pass', '1', FALSE) == ' checked="checked"') ? FALSE : 'display:none;' ?>

			<tr class="define_password" style="<?=$display?>">
				<td></td>
				<td>
				
					<input name="password" id="password" value="" type="password" class="inp-form<?=(form_error('password'))?"-error":NULL;?>" />
					<br /><a href="#" class="jsonly" onclick="generate_pw('password', this);return false;" style="font-size:0.8em;position:relative;top:2px;">Generate random password <span></span></a>
				
				</td>
			</tr>
			
			<tr class="define_password" style="<?=$display?>">
				<td></td>
				<td>
					<input type="checkbox" id="password_temp_check" name="temp_pw" value="1" <?=set_checkbox('temp_pw', '1', TRUE)?>/>
					<span class="define_password_span" style="color:#aaa;display:none;">Require password update on login</span>
				</td>
			</tr>
								
			<tr>
				<td colspan="2"><hr style="margin:4px 0 6px 0;"></td>
			</tr>		
		
			<tr>
				<td align="right" width="170">
					<label>First Name:</label>
				</td>
				<td>
					<input name="first_name" value="<?=set_value('first_name', $user_edit->first_name)?>" placeholder="e.g: Terry" type="text" class="inp-form<?=(form_error('first_name'))?"-error":NULL;?>" />
				</td>
			</tr>

			<tr>
				<td align="right">
					<label>Last Name:</label>
				</td>
				<td>
					<input name="last_name" value="<?=set_value('last_name', $user_edit->last_name)?>" placeholder="e.g: Wogan" type="text" class="inp-form<?=(form_error('last_name'))?"-error":NULL;?>" />
				</td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Email:</label>
				</td>
				<td>
					<input name="email" value="<?=set_value('email', $user_edit->email)?>" type="text" class="inp-form<?=(form_error('email'))?"-error":NULL;?>" />
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><hr style="margin:4px 0 6px 0;"></td>
			</tr>	
			
			<tr>
				<td align="right">
					<label>Username:</label>
				</td>
				<td>
					<input name="username" value="<?=set_value('username', $user_edit->username)?>" type="text" />
					<?=form_error( 'username', '<p class="error">', '</p>' )?>
				</td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Auth Method:</label>
				</td>
				<td><input name="auth_method" readonly value="<?=set_value('auth_method', $user_edit->auth_type)?>" type="text" class="inp-form<?=(form_error('auth_method'))?"-error":NULL;?>" /></td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Facebook ID:</label>
				</td>
				<td><input name="fb_id" readonly value="<?=set_value('fb_id', $user_edit->fb_id)?>" type="text" class="inp-form<?=(form_error('fb_id'))?"-error":NULL;?>" /></td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Facebook Access Token:</label>
				</td>
				<td><input name="fb_token" readonly value="<?=set_value('fb_token', $user_edit->fb_token)?>" type="text" class="inp-form<?=(form_error('fb_token'))?"-error":NULL;?>" /></td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Registered IP:</label>
				</td>
				<td><input name="ip_address" readonly value="<?=set_value('ip_address', $user_edit->ip_address)?>" type="text" class="inp-form<?=(form_error('ip_address'))?"-error":NULL;?>" /></td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Created On:</label>
				</td>
				<td><input name="created_on" readonly value="<?=set_value('created_on', date( 'jS M Y @ H:i', $user_edit->created_on))?>" type="text" class="inp-form<?=(form_error('created_on'))?"-error":NULL;?>" /></td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Last Login:</label>
				</td>
				<td><input name="last_login" readonly value="<?=set_value('last_login', date( 'jS M Y @ H:i', $user_edit->last_login))?>" type="text" class="inp-form<?=(form_error('last_login'))?"-error":NULL;?>" /></td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Referral Code:</label>
				</td>
				<td>
					<input name="referral" readonly value="<?=set_value('referral', $user_edit->referral)?>" type="text" class="inp-form<?=(form_error('referral'))?"-error":NULL;?>" />
				</td>
			</tr>
			
			<tr>
				<td align="right">
					<label>Referred By:</label>
				</td>
				<td>
					<input name="referred_by" readonly value="<?=set_value('referred_by', $user_edit->referred_by)?>" type="text" class="inp-form<?=(form_error('referred_by'))?"-error":NULL;?>" />
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