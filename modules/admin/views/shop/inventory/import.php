<div class="group-shop inventory import">
	<p>
		This facility allows multiple shop items to be imported at once. You must use the <?=anchor( '', APP_NAME . ' Shop Import Template' )?>.
	</p>
	<hr />
	<?=form_open()?>
		<p>
			Select your completed import spreadsheet below and click Continue to proceed to import verification.
		</p>
		<p class="import-field">
			<?=form_upload( 'dataimport' )?>
		</p>
		<p>
			<?=form_submit( 'submit', lang( 'action_continue' ), 'class="awesome green"' )?>
		</p>
	<?=form_close()?>
</div>