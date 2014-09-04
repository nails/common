<?php

/**
 * ---------------------------------------------------------------
 * NAILS CONSOLE
 * ---------------------------------------------------------------
 *
 * This is the console application for Nails.
 *
 * Lead Developer: Pablo de la Peña	(p@shedcollective.org, @hellopablo)
 * Lead Developer: Gary Duncan		(g@shedcollective.org, @gsdd)
 *
 * Documentation: http://nailsapp.co.uk/console
 */

//	CLI only please
if ( php_sapi_name() != "cli" ) :

	echo "This tool can only be used on the command line.";
	exit(1);

endif;

//	Set to run indefinitely
set_time_limit(0);

//	Make sure we're running on UTC
date_default_timezone_set('UTC');

//	Include the composer autoloader and the apps
require_once 'vendor/autoload.php';
require_once 'vendor/nailsapp/common/console/apps/install.php';
require_once 'vendor/nailsapp/common/console/apps/migrate.php';

//	Import the Symfony Console Application
use Symfony\Component\Console\Application;

//	Instantiate and run the application
$app = new Application();
$app->add(new Nails\Console\Apps\CORE_NAILS_Install());
$app->add(new Nails\Console\Apps\CORE_NAILS_Migrate());
$app->run();

/* End of file console.php */
/* Location: ./nailsapp/common/console/console.php */