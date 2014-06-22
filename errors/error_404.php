<!DOCTYPE html>
<html lang="en">
	<head>
		<title>404 Page Not Found</title>
		<?php

			require_once NAILS_COMMON_PATH . 'errors/_styles.php';

		?>
	</head>
	<body>
		<div id="container">
			<h1>
				<span>404</span>
				Page Not Found
			</h1>
			<?php echo $message; ?>
		</div>
	</body>
</html>