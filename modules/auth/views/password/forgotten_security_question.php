<p>
	<?=lang( 'auth_twofactor_answer_body' )?>
</p>
<p>
	<strong><?=$question->question?></strong>
</p>
<?=form_open()?>
	<p>
		<?=form_password( 'answer', NULL, 'placeholder="Type your answer here"' )?>
	</p>
	<?=form_submit( 'submit', lang( 'action_continue' ) )?>
<?=form_close()?>