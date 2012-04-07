<!--	PAGE TITLE	-->
<section>
	<h1>Compose Email to Smart List &rsaquo; "<?=$smartlist->title?>"</h1>	
</section>

<p>
	This form allows you to send a free text email to accounts matched by a smart list.
</p>

<?=form_open()?>
<?=form_hidden( 'send', TRUE )?>

<hr>

<div class="" style="margin-left:10px;margin-right:10px;">

	<div class="box">
	
		<h2>Edit Internship</h2>
		
			<div style="padding:0 12px;">
							
			<?=form_open_multipart( 'admin/internships/create/' )?>
			<?=form_hidden( 'save', TRUE )?>
			
			<?php
			
				if (
					form_error( 'subject' ) 		||
					form_error( 'body' )		) :
				
				
					echo '<div class="error">';
					echo form_error('subject');
					echo form_error('body');
					echo '</div>';
				
				endif;
			
			?>
			
				<table class="blank" style="width:100%;">
				
					<tr>
					
						<td align="right"><strong>Subject</strong>*:</td>
						<td>
							<?=form_input( 'subject', set_value( 'subject' ), 'style="width:100%;"' )?>
						</td>
						<td>
						
						</td>
					
					</tr>
					
					<tr>
					
						<td align="right"><strong>Email Body</strong>*:</td>
						<td>
							<br />Email is automatically started with "Hi {user's first name}," to confirm email is a legitimate IA email.<br />
							<?=form_textarea( 'body', set_value( 'body' ), 'style="width:100%;height:300px;"' )?>
							<br />
							<label>
								Automatically apply paragraphs and line breaks
								<?=form_checkbox( 'auto_html', set_checkbox( 'auto_html' ), TRUE )?>
							</label>
						</td>
						<td style="vertical-align:top;padding-left:30px;width:200px;padding-top:40px">
							<strong>HTML Enabled</strong>
							<br />The following tags are allowed:
							<br />&lt;a href="URL"&gt;LINK TEXT&lt;/a&gt;
							<br /><b>&lt;b&gt;bold text&lt;/b&gt;</b>
							<br /><i>&lt;i&gt;italicised text&lt;/i&gt;</i>
							<br /><u>&lt;u&gt;underlined text&lt;/u&gt;</u>
							<br /><strike>&lt;strike&gt;striked text&lt;/strike&gt;</strike>
							<br /><h1 style="display:inline;">&lt;h1&gt;header text&lt;h1&gt;</h1>
							<br />&lt;img src="IMAGE_URL" /&gt;
						</td>
					
					</tr>
					
					<tr>
					
						<td colspan="3">
							<hr>
						</td>
					
					</tr>	
					
					<tr>
					
						<td align="right"><strong></strong></td>
						<td>
							<span id="edit_button"><?=form_submit( 'send', 'Send Email to Smart List' )?></span>
							<span id="edit_button" style="opacity:0.5;-webkit-opacity:0.5;-moz-opacity:0.5;filter:alpha(opacity=50)" rel="tooltip-r" title="Coming in version 2!"><?=form_submit( 'preview', 'Preview', 'disabled' )?></span>
						</td>
					
					</tr>
				
				</table>
			
			</form>
	
		</div>
	
	</div>

</div>

<?=form_close()?>