<h1>Frequently Asked Questions: Edit</h1>

<p>
	You can edit this FAQ using the form below.
</p>

<style type="text/css">
	
	.mceEditor { 
		margin-bottom:10px;
		display:block;
	}
	.specific input[type=text], textarea {
		width:250px;
	}
	.specific textarea[name=body] {
		height:250px;
		width: 540px;
	}
	.error p {
		width:250px;
	}
	
</style>

<?=form_open_multipart( 'admin/faq/edit/' . $faq->id )?>
<?=form_hidden( 'update', TRUE )?>
<?=form_hidden( 'id', $faq->id )?>

<?php

	if ( 
		form_error( 'slug' ) 		||
		form_error( 'label' ) 		||
		form_error( 'body' ) 		||
		form_error( 'order' )
		) :
	
			echo '<div class="error">';
			echo form_error('slug');
			echo form_error('label');
			echo form_error('body');
			echo form_error('order');
			echo $error;
			echo '</div>';
	
	endif;

?>

<hr>

<div style="margin-left:10px;margin-right:10px;">


	<div class="box specific" style="width:680px;padding-bottom:10px;">
	
		<h2>Edit</h2>
		
		<div style="padding:0 12px;">
						
			<table class="blank" style="width:660px">
			
				<tr>
					<td align="right" width="70">
						<strong>Slug</strong>:
					</td>
					<td>
						<input type="text" name="slug" value="<?=set_value( 'slug', $faq->slug )?>" placeholder="e.g: what-is-intern-avenue" />
					</td>
				</tr>
				<tr>
					<td align="right">
						<strong>Label</strong>:
					</td>
					<td>
						<input type="text" name="label" value="<?=set_value( 'label', $faq->label )?>" placeholder="e.g: What is Intern Avenue?" />
					</td>
				</tr>
				<tr>
					<td align="right">
						<strong>Content</strong>:
					</td>
					<td>
						<textarea name="body"><?=set_value( 'body', htmlentities( $faq->body ) )?></textarea>
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
			
	</form>	
	
</div>
