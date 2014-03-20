<div class="row">
	<div class="well well-lg col-sm-6 col-sm-offset-3 text-center">
		<p>
			<?=lang( 'auth_twofactor_answer_body' )?>
		</p>
		<hr />
		<h4 style="margin-bottom:1.25em;">
			<strong><?=$question->question?></strong>
		</h4>
		<?=form_open()?>
			<p>
				<?=form_password( 'answer', NULL, 'class="form-control" placeholder="Type your answer here"' )?>
			</p>
			<hr />
			<button class="btn btn-lg btn-primary" type="submit"><?=lang( 'action_continue' )?></button>
		<?=form_close()?>
	</div>
</div>