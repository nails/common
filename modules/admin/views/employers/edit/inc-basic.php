<div class="box specific" id="box_employer_edit_basic">

	<h2>
		Basic Information
		<a href="#" class="toggle">close</a>
	</h2>
	
	<div class="container" style="padding:0 12px;">
					
		<table class="blank">
		
			<!--	Status	-->
			<tr>
				<td align="right">
					<strong>Active</strong>:
				</td>
				<td>
					<select name="active">
						<option value=""<?php if ($employer->active==NULL): echo ' selected="selected"'; endif; ?>>No</option>
						<option value="1"<?php if ($employer->active==1): echo ' selected="selected"'; endif; ?>>Yes</option>
					</select>
					<small style="display:block;">
						Pablo: I think this is deprecated in favour of packages, however, left in just in case.
					</small>
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><hr style="margin:4px 0 6px 0;"></td>
			</tr>
			
			
			<tr>
				<td align="right" width="170">
					<strong>Name*</strong>:
				</td>
				<td>
					<input type="text" name="name" value="<?=set_value( 'name', $employer->name )?>" placeholder="e.g: BBC" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Long Name</strong>:
				</td>
				<td>
					<input type="text" name="name_long" value="<?=set_value( 'name_long', $employer->name_long )?>" placeholder="e.g: British Broadcasting Company" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Slug*</strong>:
				</td>
				<td>
					<input type="text" name="url_id" value="<?=set_value( 'url_id', $employer->url_id )?>" placeholder="e.g: british_broadcasting_company" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Main website URL</strong>:
				</td>
				<td>
					<input type="text" name="url_main" value="<?=set_value( 'url_main', $employer->url_main )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Graduate / Careers URL</strong>:
				</td>
				<td>
					<input type="text" name="url_careers" value="<?=set_value( 'url_careers', $employer->url_careers )?>"/>
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Company Logo</strong>:
				</td>
				<td>
					<?php if($employer->logo) : ?>
						<?=img( cdn_scale( 'employer_images', $employer->logo, 160, 160 ) )?>
						<br />
					<?php endif; ?>
					<input type="file" name="userfile" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Description</strong>:
				</td>
				<td>
					<textarea name="description"><?=set_value( 'description', $employer->description )?></textarea>
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Description Short</strong>:
				</td>
				<td>
					<textarea name="description_short" placeholder="e.g. Broadcast Media Company"><?=set_value( 'description_short', $employer->description_short )?></textarea>
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Twitter Username</strong>:
				</td>
				<td>
					<input type="text" name="twitter" value="<?=set_value( 'twitter', $employer->twitter )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Facebook Page</strong>:
				</td>
				<td>
					<input type="text" name="facebook" value="<?=set_value( 'facebook', $employer->facebook )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>HR Email</strong>:
				</td>
				<td>
					<input type="text" name="email" value="<?=set_value( 'email', $employer->email )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Main Telephone</strong>:
				</td>
				<td>
					<input type="text" name="telephone" value="<?=set_value( 'telephone', $employer->telephone )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Address Street</strong>:
				</td>
				<td>
					<input type="text" name="address_street" value="<?=set_value( 'address_street', $employer->address_street )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Address Town</strong>:
				</td>
				<td>
					<input type="text" name="address_town" value="<?=set_value( 'address_town', $employer->address_town )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Address Area</strong>:
				</td>
				<td>
					<input type="text" name="address_area" value="<?=set_value( 'address_area', $employer->address_area )?>" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<strong>Address Postcode</strong>:
				</td>
				<td>
					<input type="text" name="address_postcode" value="<?=set_value( 'address_postcode', $employer->address_postcode )?>" />
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