<!DOCTYPE html>
<html lang="en">
<head>
<title>Error <?= $status_code . ' - ' . $heading?></title>
<?php

	require_once NAILS_PATH . 'errors/_styles.php';

?>
</head>
<body>
	<div id="container">
	<?php

		echo '<h1>';
		echo '<span>' . $status_code . '</span>';
		echo $heading;
		echo '</h1>';
		echo $message;

		//	Custom 'small' messages
		if ( isset( get_instance()->session ) ) :

			$_admin = get_instance()->session->userdata( 'admin_recovery' );
			if ( get_instance()->uri->segment( 1 ) == 'admin' && $_admin ) :

				echo '<small>';
				echo 'You\'re getting this error because you are currently logged in as ' . active_user( 'email' ) . ', a user who does not have access to Administration. ';
				echo 'If you\'d like to log back in as ' . $_admin->email . ' then please click ' . anchor( 'auth/override/login_as/' . $_admin->id . '/' . $_admin->hash, 'here' ) . '.';
				echo '</small>';

			endif;

		endif;

	?>
	</div>
</body>
</html>