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

		?>
		</div>
	</body>
</html>