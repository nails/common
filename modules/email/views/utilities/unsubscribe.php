<!DOCTYPE html>
<html lang="en">
<head>
<title>404 Page Not Found</title>
<style type="text/css">

	body
	{
		background:#EFEFEF;
		font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
		font-weight: 300;
		line-height:2em;
		text-align:center;
	}
	#container
	{
		border:1px solid #CCC;
		display:inline-block;
		background:#FFF;
		min-width:450px;
		max-width:550px;
		padding:0px;
		margin:50px auto 25px auto;
		border-radius:5px;
		box-shadow:0px 2px 5px #CCC;
		overflow:none;
	}

	h1
	{
		margin:0;
		padding:15px;
		border-bottom:1px dashed #EEE;
	}

	small
	{
		font-size:0.8em;
	}

</style>
</head>
<body>
	<div id="container">
		<?php if ( $this->input->get( 'undo' ) ) : ?>

			<h1>That's OK, we all make mistakes</h1>
			<p>
				We'll continue to send you these types of email. <?=anchor( 'email/unsubscribe?token=' . $this->input->get( 'token' ), 'Unsubscribe?' ) ?>
			</p>

		<?php else : ?>

			<h1>Successfully Unsubscribed</h1>
			<p>
				OK! We won't send you this type of email again. <?=anchor( 'email/unsubscribe?token=' . $this->input->get( 'token' ) . '&undo=1', 'Undo?' ) ?>
			</p>

		<?php endif; ?>
	</div>
	<p>
		<small><?=anchor( '', 'Back to ' . APP_NAME )?></small>
	</p>
</body>
</html>