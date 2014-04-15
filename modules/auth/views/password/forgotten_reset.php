<div class="row">
	<div class="well well-lg <?=BS_COL_SM_6?> <?=BS_COL_SM_OFFSET_3?>">
		<p>
			<?=lang( 'auth_forgot_reset_ok' )?>
		</p>
		<div class="row">
			<div class="<?=BS_COL_MD_OFFSET_4?> <?=BS_COL_MD_4?>">
				<input type="text" value="<?=htmlentities( $new_password )?>" class="form-control" id="temp-password" style="font-size:1.5em;text-align:center;" />
			</div>
		</div>
		<p style="margin-top:1em;">
			<?=anchor( 'auth/login', lang( 'auth_forgot_action_proceed' ), 'class="btn btn-primary"' )?>
		</p>
	</div>
</div>
<script type="text/javascript">

	var textBox = document.getElementById( 'temp-password' );
	textBox.onfocus = function()
	{
		textBox.select();

		// Work around Chrome's little problem
		textBox.onmouseup = function()
		{
			// Prevent further mouseup intervention
			textBox.onmouseup = null;
			return false;
		};
	};
</script>