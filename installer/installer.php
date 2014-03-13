<?php

/*
 | --------------------------------------------------------------------
 | NAILS INSTALLER
 | --------------------------------------------------------------------
 |
 | This tiny class is the kick point for installing a new Nails application
 | or installing modules into an existing application.
 |
 | Lead Developer: Pablo de la Peña	(p@shedcollective.org, @hellopablo)
 | Lead Developer: Gary Duncan		(g@shedcollective.org, @gsdd)
 |
 | Documentation: http://docs.nailsapp.co.uk
 |
 */


class CORE_NAILS_Installer
{
	protected $_errors;
	protected $_app_file;
	protected $_has_app_file;
	protected $_deploy_file;
	protected $_has_deploy_file;


	// --------------------------------------------------------------------------


	public function __construct()
	{
		$this->_errors			= '';
		$this->_app_file		= '';
		$this->_has_app_file	= FALSE;
		$this->_deploy_file		= '';
		$this->_has_deploy_file	= FALSE;
	}

	// --------------------------------------------------------------------------


	public function start()
	{
		/**
		 *	First, we need to see if we know where the Nails. installation is, i.e is
		 *	NAILS_PATH defined and valid? If it's not then we're likely installing a
		 *	fresh application so will need to ask the user where it is (unless, of course
		 *	we manage to auto locate it using some command line magic)
		 **/

		//	In case this file is access directly, include the config files
		if ( file_exists( $this->_app_file ) ) :

			$this->_has_app_file = TRUE;
			require_once $this->_app_file;

		endif;

		if ( file_exists( $this->_deploy_file ) ) :

			$this->_has_deploy_file = TRUE;
			require_once $this->_deploy_file;

		endif;

		//	Check Nails is there
		if ( ! defined( 'NAILS_PATH' ) ) :

			$_NAILS_PATH = './vendor/shed/nails/';

		else :

			$_NAILS_PATH = NAILS_PATH;

		endif;

		if ( ! file_exists( $_NAILS_PATH . 'core/CORE_NAILS_Controller.php' ) ) :

			$this->_errors = '<strong>CANNOT FIND NAILS.</strong><br />Make sure you have run Composer.';

		endif;


		if ( defined( 'DEPLOY_INSTALLER_PW' ) && DEPLOY_INSTALLER_PW ) :

			//	Installer is locked
			if ( $_POST && DEPLOY_INSTALLER_PW == md5( $_POST['password'] . DEPLOY_PRIVATE_KEY ) ) :

				if ( isset( $_POST['action'] ) && $_POST['action'] != 'unlock' ) :

					$this->_run_installer();

				else :

					$this->_request_config();

				endif;

			elseif ( $_POST && DEPLOY_INSTALLER_PW != md5( $_POST['password'] . DEPLOY_PRIVATE_KEY ) ) :

				$this->_errors = 'Incorrect password';
				$this->_request_password();

			else :

				$this->_request_password();

			endif;

		else :

			//	Installer is not locked
			if ( $_POST ) :

				$this->_run_installer();

			else :

				$this->_request_config();

			endif;

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _request_config()
	{
		$this->_view_header();

		?>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" id="container" class="rounded" onsubmit="return validateForm()">
		<?=isset( $_POST['password'] ) ? '<input type="hidden" name="password" value="' . $_POST['password'] . '" />' : ''?>
			<input type="hidden" name="action" value="configure" />
			<h1>Welcome to your new Nails. app!</h1>
			<?php if ( $this->_errors ) : ?>
			<p class="error rounded">
				<?=$this->_errors?>
			</p>
			<?php endif; ?>
			<p>
				This page will get you started on the way to configuring your application properly. Once the app is talking nicely to the Nails. installation we'll make sure everything is in order and get the modules configured.
			</p>
			<!-- APP NAME -->
			<div class="config-item">
				<p>
					Firstly, what's your app called?
				</p>
				<p>
				<?php

					if ( isset( $_POST['app_name'] ) ) :

						$_default = $_POST['app_name'];

					elseif ( defined( 'APP_NAME' ) ) :

						$_default = APP_NAME;

					else :

						$_default = '';

					endif;

					$_readonly = $this->_has_app_file ? 'readonly=""' : '';


					echo '<input type="text" class="rounded" name="app_name" ' . $_readonly . ' value="' . $_default . '" placeholder="What\'s your app called?">';
					echo $_readonly ? '<small class="readonly">To change this value, please update config/app.php manually.</small>' : '';

				?>
				</p>
			</div>

			<!-- BASE URL -->
			<div class="config-item">
				<p>
					What's your app's URL (for this deployment)?
				</p>
				<p>
				<?php

					if ( isset( $_POST['base_url'] ) ) :

						$_default = $_POST['base_url'];

					elseif( defined( 'BASE_URL' ) ) :

						$_default = BASE_URL;
					else :

						$_default = isset( $_SERVER['SERVER_NAME'] ) ? 'http://' . $_SERVER['SERVER_NAME'] : '';

					endif;

					echo '<input type="text" class="rounded" name="base_url" value="' . $_default . '" placeholder="http://www.example.com/">';

				?>
				</p>
			</div>

			<!-- DEVELOPER NOTIFICATIONS -->
			<div class="config-item">
				<p>
					Who should receive developer notifications?
				</p>
				<p>
				<?php

					if ( isset( $_POST['app_developer_email'] ) ) :

						$_default = $_POST['app_developer_email'];

					elseif( defined( 'APP_DEVELOPER_EMAIL' ) ) :

						$_default = APP_DEVELOPER_EMAIL;

					else :

						$_default = '';

					endif;

					$_readonly = $this->_has_app_file ? 'readonly=""' : '';

					echo '	<input type="text" class="rounded" name="app_developer_email" ' . $_readonly . ' value="' .$_default . '" placeholder="you@example.com">';
					$_readonly ? '<small class="readonly">To change this value, please update config/app.php manually.</small>' : ''

				?>
				</p>
			</div>

			<!-- DEFAULT TIMEZONE -->
			<div class="config-item">
				<p>
					What's the default timezone for this app?
				</p>
				<p>
				<?php

					if ( isset( $_POST['app_default_timezone'] ) ) :

						$_default = $_POST['app_default_timezone'];

					elseif( defined( 'APP_DEFAULT_TIMEZONE' ) ) :

						$_default = APP_DEFAULT_TIMEZONE;

					else :

						$_default = date_default_timezone_get();

					endif;

					if ( $this->_has_app_file ) :

						echo '<input type="text" class="rounded" name="app_default_timezone" readonly="readonly" value="' . $_default . '">';
						echo $_readonly ? '<small class="readonly">To change this value, please update config/app.php manually.</small>' : '';

					else :

						echo '<select name="">';

							$_timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

							foreach ( $_timezones AS $tz ) :

								$_selected = $tz == $_default ? 'selected="selected"' : '';
								echo '<option value="' . $tz. '" ' . $_selected . '>' . $tz . '</option>';

							endforeach;

						echo '</select>';

					endif;

				?>
				</p>
			</div>

			<!-- DATABASE -->
			<div class="config-item">
				<p>
					Specify the details for conencting to MySQL. You'll need to have an empty database already set up.
				</p>
				<fieldset>
					<p>
					<?php

						if ( isset( $_POST['deploy_db_host'] ) ) :

							$_default = $_POST['deploy_db_host'];

						elseif( defined( 'DEPLOY_DB_HOST' ) ) :

							$_default = DEPLOY_DB_HOST;

						else :

							$_default = 'localhost';

						endif;

						echo '<strong>Host</strong>';
						echo '<input type="text" class="rounded" name="deploy_db_host" value="' . $_default . '" placeholder="The host to connect to MySQL">';

					?>
					</p>
					<p>
					<?php

						if ( isset( $_POST['deploy_db_username'] ) ) :

							$_default = $_POST['deploy_db_username'];

						elseif( defined( 'DEPLOY_DB_USERNAME' ) ) :

							$_default = DEPLOY_DB_USERNAME;

						else :

							$_default = '';

						endif;

						echo '<strong>Username</strong>';
						echo '<input type="text" class="rounded" name="deploy_db_username" value="' . $_default . '" placeholder="The database username">';

					?>
					</p>
					<p>
					<?php

						if ( isset( $_POST['deploy_db_password'] ) ) :

							$_default = $_POST['deploy_db_password'];

						elseif( defined( 'DEPLOY_DB_PASSWORD' ) ) :

							$_default = DEPLOY_DB_PASSWORD;

						else :

							$_default = '';

						endif;

						echo '<strong>Password</strong>';
						echo '<input type="password" class="rounded" name="deploy_db_password" value="' . $_default . '" placeholder="The database password">';

					?>
					</p>
					<p>
					<?php

						if ( isset( $_POST['deploy_db_database'] ) ) :

							$_default = $_POST['deploy_db_database'];

						elseif( defined( 'DEPLOY_DB_DATABASE' ) ) :

							$_default = DEPLOY_DB_DATABASE;

						else :

							$_default = '';

						endif;

						echo '<strong>Database</strong>';
						echo '<input type="text" class="rounded" name="deploy_db_database" value="' . $_default . '" placeholder="The database name">';

					?>
					</p>
				</fieldset>
			</div>

			<!-- ENVIRONMENT -->
			<div class="config-item">
				<p>
					Please tell me what environment this deployment is:
				</p>
				<p>
				<?php

					if ( isset( $_POST['environment'] ) ) :

						$_environment = $_POST['environment'];

					elseif( defined( 'ENVIRONMENT' ) ) :

						$_environment = ENVIRONMENT;
					else :

						$_environment = 'development';

					endif;

					echo '<select name="environment">';
						$_selected = $_environment == 'development' ? 'selected="selected"' : '';
						echo '<option value="development" ' . $_selected . '>Development - Normally a local development build</option>';

						$_selected = $_environment == 'staging' ? 'selected="selected"' : '';
						echo '<option value="staging" ' . $_selected . '>Staging - A mirror of the production environment</option>';

						$_selected = $_environment == 'production' ? 'selected="selected"' : '';
						echo '<option value="production" ' . $_selected . '>Production - The live website; errors are suppressed.</option>';
					echo '</select>';

				?>
				</p>
			</div>

			<?php if ( ! $this->_has_app_file ) : ?>
			<!-- FIRST USER -->
			<div class="config-item">
				<p>
					Almost done! Please define the details for the first user (you!).
				</p>
				<fieldset>
					<p>
					<?php

						if ( isset( $_POST['first_user_first_name'] ) ) :

							$_default = $_POST['first_user_first_name'];

						else :

							$_default = '';

						endif;

						echo '<strong>First Name</strong>';
						echo '<input type="text" class="rounded" name="first_user_first_name" value="' . $_default . '" placeholder="The user\'s first name">';

					?>
					</p>
					<p>
					<?php

						if ( isset( $_POST['first_user_last_name'] ) ) :

							$_default = $_POST['first_user_last_name'];

						else :

							$_default = '';

						endif;

						echo '<strong>Surame</strong>';
						echo '<input type="text" class="rounded" name="first_user_last_name" value="' . $_default . '" placeholder="The user\'s surname">';

					?>
					</p>
					<p>
					<?php

						if ( isset( $_POST['first_user_email'] ) ) :

							$_default = $_POST['first_user_email'];

						else :

							$_default = '';

						endif;

						echo '<strong>Email</strong>';
						echo '<input type="text" class="rounded" name="first_user_email" value="' . $_default . '" placeholder="The user\'s email">';

					?>
					</p>
					<p>
					<?php

						if ( isset( $_POST['first_user_password'] ) ) :

							$_default = $_POST['first_user_password'];

						else :

							$_default = '';

						endif;

						echo '<strong>Password</strong>';
						echo '<input type="password" class="rounded" name="first_user_password" value="' . $_default . '" placeholder="The user\'s password">';

					?>
					</p>
				</fieldset>
			</div>
			<?php endif; ?>

			<!-- INSTALLER PASSWORD -->
			<div class="config-item">
				<p>
					Finally, please set an installer password
				</p>
				<p>
					<input type="password" id="password" class="rounded" name="install_password" value="<?=isset( $_POST['install_password'] ) ? $_POST['install_password'] : '' ?>" placeholder="Optional, set a password">
				</p>
				<p>
					<small>This will prevent unauthorised users from running the installer.</small>
				</p>
			</div>

			<p class="submit">
				<input type="submit" name="submit" class="awesome rounded" value="Continue &rsaquo;" />
			</p>
		</form>
		<script type="text/javascript">
			function validateForm()
			{
				if ( document.getElementById( 'password' ).value.length == 0 )
				{
					return confirm( 'Are you sure you do not want to set an Installer password?\n\nANY user visiting the installer will be directed to the app\'s configuration page.\n\nWe highly recommend setting a secure password, but at the end of the day - you\'re the boss!' );
				}

				return true;
			}
		</script>
		<?php

		$this->_view_footer();
	}


	// --------------------------------------------------------------------------


	protected function _run_installer()
	{
		//	Form Validation
		//	TODO

		//	Database credential validation
		$_db_host		= ! empty( $_POST['deploy_db_host'] ) ? $_POST['deploy_db_host'] : '';
		$_db_username	= ! empty( $_POST['deploy_db_username'] ) ? $_POST['deploy_db_username'] : '';
		$_db_password	= ! empty( $_POST['deploy_db_password'] ) ? $_POST['deploy_db_password'] : '';
		$_db_database	= ! empty( $_POST['deploy_db_database'] ) ? $_POST['deploy_db_database'] : '';

		$_db = new DB();
		if ( ! $_db->connect( $_db_host, $_db_username, $_db_password, $_db_database ) ) :

			$this->_errors = 'Could not connect to MySQL.';
			$this->_request_config();
			return FALSE;

		endif;

		// --------------------------------------------------------------------------

		//	Attempt to create the app.php and deploy.php file

		//	Only try to create the app.php config file if it doesn't exist; don't want to overwrite anything.
		if ( ! $this->_has_app_file ) :

			$_app_name				= isset( $_POST['app_name'] ) ? $_POST['app_name'] : '';
			$_app_lang				= 'english';
			$_app_key				= md5( uniqid() );
			$_app_developer_email	= isset( $_POST['app_developer_email'] ) ? $_POST['app_developer_email'] : '';
			$_app_default_timezone	= isset( $_POST['app_default_timezone'] ) ? $_POST['app_default_timezone'] : '';

			$_app_str  = '<?php' . "\n";
			$_app_str .= 'define( \'APP_NAME\',	\'' . str_replace( "'", "\'", $_app_name ) . '\' );' . "\n";
			$_app_str .= 'define( \'APP_DEFAULT_LANG_SLUG\',	\'' . str_replace( "'", "\'", $_app_lang ) . '\' );' . "\n";
			$_app_str .= 'define( \'APP_PRIVATE_KEY\',	\'' . $_app_key . '\' );' . "\n";
			$_app_str .= 'define( \'APP_DEVELOPER_EMAIL\',	\'' . $_app_developer_email . '\' );' . "\n";
			$_app_str .= 'define( \'APP_DEFAULT_TIMEZONE\',	\'' . $_app_default_timezone . '\' );' . "\n";
			$_app_str .= 'define( \'APP_NAILS_MODULES\',	\'*\' );' . "\n";

			$_fh = @fopen( $this->_app_file, 'w' );

			if ( $_fh ) :

				if ( @fwrite( $_fh, $_app_str ) ) :

					$_app_ok = TRUE;

				else :

					$_app_ok = FALSE;
					@unlink( $this->_app_file );

				endif;

				fclose( $_fh );

			else :

				$_app_ok = FALSE;
				@unlink( $this->_app_file );

			endif;

		else :

			$_app_ok = TRUE;

		endif;

		// --------------------------------------------------------------------------

		$_environment	= isset( $_POST['environment'] ) ? $_POST['environment'] : 'developmet';
		$_base_url		= isset( $_POST['base_url'] ) ? $_POST['base_url'] : '/';
		$_deploy_key	= ! defined( 'DEPLOY_PRIVATE_KEY' ) ? md5( uniqid() ) : DEPLOY_PRIVATE_KEY;
		$_password		= ! empty( $_POST['install_password'] ) ? md5( $_POST['install_password'] . $_deploy_key ) : '';

		// --------------------------------------------------------------------------

		//	Sanitize base url
		if ( ! $_base_url ) :

			$_base_url = isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '/';

		endif;

		$_base_url  = substr( $_base_url, 0, 7 ) != 'http://' ? 'http://' . $_base_url : $_base_url;
		$_base_url .= substr( $_base_url, -1 ) != '/' ? '/' : '';

		// --------------------------------------------------------------------------

		$_deploy_str  = '<?php' . "\n";
		$_deploy_str .= 'define( \'ENVIRONMENT\',	\'' . $_environment . '\' );' . "\n";
		$_deploy_str .= 'define( \'BASE_URL\',	\'' . $_base_url . '\' );' . "\n";
		$_deploy_str .= 'define( \'DEPLOY_PRIVATE_KEY\',	\'' . $_deploy_key . '\' );' . "\n";
		$_deploy_str .= 'define( \'DEPLOY_INSTALLER_PW\',	\'' . $_password . '\' );' . "\n";

		$_deploy_str .= 'define( \'DEPLOY_DB_HOST\',		\'' . $_db_host . '\' );' . "\n";
		$_deploy_str .= 'define( \'DEPLOY_DB_USERNAME\',	\'' . $_db_username . '\' );' . "\n";
		$_deploy_str .= 'define( \'DEPLOY_DB_PASSWORD\',	\'' . $_db_password . '\' );' . "\n";
		$_deploy_str .= 'define( \'DEPLOY_DB_DATABASE\',	\'' . $_db_database . '\' );' . "\n";

		$_fh = @fopen( $this->_deploy_file, 'w' );

		if ( $_fh ) :

			if ( @fwrite( $_fh, $_deploy_str ) ) :

				$_deploy_ok = TRUE;

			else :

				$_deploy_ok = FALSE;
				@unlink( $this->_deploy_file );

			endif;

			fclose( $_fh );

		else :

			$_deploy_ok = FALSE;
			@unlink( $this->_deploy_file );

		endif;

		// --------------------------------------------------------------------------

		if ( $_app_ok && $_deploy_ok ) :

			//	All ok, bring database up to date
			$_abpath = $this->_abpath;
			$_abpath = str_replace( ' ', '\ ', $_abpath );
			$_abpath = str_replace( '.', '\.', $_abpath );
			$_abpath = str_replace( '[', '\[', $_abpath );
			$_abpath = str_replace( ']', '\]', $_abpath );

			$_files = glob( $_abpath . '/vendor/shed/nails/migrations/*_*.php' );

			$_file_count = count( $_files );

			for ( $i = 0; $i < $_file_count; $i++ ) :

				// Mark wrongly formatted files as false for later filtering
				$name = basename( $_files[$i], '.php' );
				if ( ! preg_match('/^\d{3}_(\w+)$/', $name ) ) :

					$_files[$i] = FALSE;

				endif;

			endfor;

			$_files			= array_filter( $_files );
			$_file_count	= count( $_files );

			//	Attempt migrations
			for ( $i = 0; $i < $_file_count; $i++ ) :

				//	TODO
				//	- instanciate class
				//	- execute up()

			endfor;

			//	Add the initial user
			//	TODO

			//	All ok, ask user to log in.
			header( 'Location: auth/login?return_to=' . urlencode( 'system/nails/configure' ) );

		else :

			$this->_view_header();
			?>
			<div id="container">
				<h1>Nails. Installer</h1>
				<p>
					Unfortunately I wasn't able to create the appropriate configuration files.
				</p>
				<p>
					Please ensure that the <code>config/</code> directory is writeable by the webserver and run this installer again.
				</p>
			</div>
			<?php
			$this->_view_footer();

		endif;
	}


	// --------------------------------------------------------------------------


	protected function _request_password()
	{
		$this->_view_header();
		?>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST" id="container" class="rounded">
			<input type="hidden" name="action" value="unlock" />
			<h1>
				<img src="data:image/png;base64,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" />
				Installer locked
			</h1>
			<?php if ( isset( $this->_errors ) && $this->_errors ) : ?>
			<p class="error rounded">
				<?=$this->_errors?>
			</p>
			<?php endif; ?>
			<p>
				Please provide the installer password for this deployment.
			</p>
			<ul>
				<li>
					<label class="rounded">
					<input type="password" class="rounded" name="password" value="" placeholder="Type your password">
					</label>
				</li>
			</ul>
			<p class="submit">
				<input type="submit" name="submit" class="awesome rounded" value="Continue &rsaquo;" />
			</p>
		</form>
		<?php
		$this->_view_footer();
	}


	// --------------------------------------------------------------------------


	protected function _view_header()
	{
		?>
		<html>
		<head>
			<title>Nails. - Installer</title>
			<style type="text/css">
				html,body
				{
					font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
					font-size:16px;
					font-weight: 300;
					background:#F9F9F9;
					line-height:1.75em;
					padding:10px;
				}

				#container
				{
					width:60%;
					margin:25px auto;
					padding:10px 20px;
					border:1px solid #EEE;
					-moz-box-sizing:border-box;
					box-sizing:border-box;
					background:#FFF;
				}

				@media (max-width: 750px) {

					html,body
					{
						margin:0;
						padding:0;
					}

					#container
					{
						width:100%;
						margin:0;
					}

				}

				.rounded
				{
					-webkit-border-radius:3px;
					-moz-border-radius:3px;
					-o-border-radius:3px;
					border-radius:3px;
				}

				h1
				{
					text-align:center;
					margin:0.25em 0;
					margin-bottom:1em;
					padding-bottom:0.75em;
					line-height:1.25em;
					border-bottom:1px dotted #EEE;
				}

				h1 img
				{
					position:relative;
					top:2px;
					margin-right:5px;
				}

				p
				{
					margin:0;
					margin-bottom:1em;;
				}

				p small
				{
					display:block;
					color:#CCC;
				}

				p.error
				{
					background:#FFEBE8;
					border:1px solid #CC0010;
					padding:10px;
					color:#CC0010;
				}

				p.submit
				{
					text-align:right;
				}

				fieldset
				{
					border:1px solid #CCC;
					padding:10px;
					background:#EFEFEF;
					margin-bottom:1em;
					-webkit-border-radius:3px;
					-moz-border-radius:3px;
					-o-border-radius:3px;
					border-radius:3px;
				}

				fieldset p:last-of-type
				{
					margin-bottom:0;
				}

				input[type=text],
				input[type=password]
				{
					padding:15px;
					border:1px solid #CCC;
					width:100%;
					font-size:1em;
				}

				input[type="submit"]
				{
					font-family:'Helvetica', Arial, sans-serif;
					font-size: 14px;
					text-transform: none;
					background: #555;
					color: #fff !important;
					padding: 8px 14px 6px 14px;
					font-weight: normal !important;
					line-height: 1em !important;
					text-align: center;
					position:relative;
					display: inline-block;
					cursor:pointer;
					text-decoration: none;
					margin-right:5px;
					border-left:0px;
					border-right:0px;
					border-top:0px;
					border-bottom:0px;
					text-shadow:none;
					border:none;
				}
				input[type="submit"]:hover
				{
					background: #333 !important;
				}
				input[type="submit"]:active
				{
					top:1px;
				}

				select
				{
					width:100%;
					font-size:1em;
				}

				input[readonly=readonly]
				{
					color:#888;
					background:#F5F5F5;
					bottom:25px;
				}

				#footer
				{
					text-align:Center;
					font-size:0.7em;
					margin-bottom:2em;
				}

				#footer a
				{
					color:inherit;
				}
			</style>
		</head>
		<body>
		<?php
	}


	// --------------------------------------------------------------------------


	protected function _view_footer()
	{
		?>
		<div id="footer">
			Powered by <a href="http://nailsapp.co.uk">Nails.</a> form <a href="http://shedcollective.org">Shed Collective</a>
		</div>
		</body>
		</html>
		<?php
	}
}


// --------------------------------------------------------------------------


//	Define some utility classes to aid us with the database migration
class CORE_NAILS_Migration {}
class DB
{
	private $_db;

	// --------------------------------------------------------------------------

	public function connect( $host, $user, $pass, $db )
	{
		$this->_db = @new mysqli( $host, $user, $pass, $db );

		if ( $this->_db->connect_errno ) :

			return FALSE;

		endif;

		return TRUE;
	}


	// --------------------------------------------------------------------------


	public function query( $query )
	{
		$this->_db->query( $query );
	}
}