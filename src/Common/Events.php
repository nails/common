<?php

/**
 * The class provides a summary of the events fired by this module
 *
 * @package     Nails
 * @subpackage  module-common
 * @category    Events
 * @author      Nails Dev Team
 */

namespace Nails\Common;

use Nails\Common\Event\Listener;
use Nails\Common\Events\Base;
use Nails\Common\Factory\Component;
use Nails\Common\Interfaces\Database\Migration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Events
 *
 * @package Nails\Common
 */
class Events extends Base
{
    /**
     * Fired when the system starts, after Nails has initiated and routing complete,
     * but before the controller is constructed
     */
    const SYSTEM_STARTUP = 'SYSTEM:STARTUP';

    /**
     * Fired when the Base Nails controller is about to run
     */
    const SYSTEM_STARTING = 'SYSTEM:STARTING';

    /**
     * Fired when the system is ready and the controller is about to be constructed
     */
    const SYSTEM_READY = 'SYSTEM:READY';

    /**
     * Fired when the system shuts down, this is the last event to be fired
     */
    const SYSTEM_SHUTDOWN = 'SYSTEM:SHUTDOWN';

    /**
     * Firing this event will rewrite app routes
     */
    const ROUTES_UPDATE = 'ROUTES:UPDATE';

    /**
     * Fired immediately before output is sent to the browser
     */
    const OUTPUT_PRE = 'OUTPUT:PRE';

    /**
     * Fired immediate after output is sent to the browser
     */
    const OUTPUT_POST = 'OUTPUT:POST';

    /**
     * Fired before a view is loaded
     *
     * @param $sView         string The view which was loaded
     * @param $sResovledPath string The view's resolved path
     */
    const VIEW_PRE = 'VIEW:PRE';

    /**
     * Fired after a view is loaded
     *
     * @param $sView         string The view which was loaded
     * @param $sResovledPath string The view's resolved path
     */
    const VIEW_POST = 'VIEW:POST';

    /**
     * Fired before an error view is rendered
     *
     * @param int    $iCode The error code (404, 500, etc)
     * @param string $sType The type of error (html or cli)
     * @param array  $aData Data being passed to the view (as a reference)
     */
    const VIEW_ERROR_PRE = 'VIEW:ERROR:PRE';

    /**
     * Fired after an error view is rendered
     *
     * @param int    $iCode The error code (404, 500, etc)
     * @param string $sType The type of error (html or cli)
     * @param array  $aData Data being passed to the view (as a reference)
     */
    const VIEW_ERROR_POST = 'VIEW:ERROR:POST';

    /**
     * Fired before migrations are run
     *
     * @param array $aEnabledModules The available modules to be migrated
     */
    const DB_MIGRATE_PRE = 'DB:MIGRATE:PRE';

    /**
     * Fired before each migration
     *
     * @param Component $oModule    The module being migrated
     * @param Migration $oMigration The migration being executed
     */
    const DB_MIGRATE_BEFORE = 'DB:MIGRATE:BEFORE';

    /**
     * Fired after each migration
     *
     * @param Component $oModule    The module being migrated
     * @param Migration $oMigration The migration being executed
     */
    const DB_MIGRATE_AFTER = 'DB:MIGRATE:AFTER';

    /**
     * Fired after all migrations have executed successfully
     *
     * @param array $aEnabledModules The available modules to be migrated
     */
    const DB_MIGRATE_POST = 'DB:MIGRATE:POST';

    /**
     * Fired when migrations fail
     *
     * @param array      $aEnabledModules The available modules to be migrated
     * @param \Throwable $e               The exception which was caught
     */
    const DB_MIGRATE_FAIL = 'DB:MIGRATE:FAIL';

    /**
     * Fired before routes are generated
     *
     * @param string|null          $sModue  The module to rewrite routes for
     * @param OutputInterface|null $oOutput The console output interface
     */
    const ROUTES_REWRITE_PRE = 'ROUTES:REWRITE:PRE';

    /**
     * Fired after routes are generated
     *
     * @param bool                 $bResult Whether routes were successfully written, or not
     * @param string|null          $sModue  The module to rewrite routes for
     * @param OutputInterface|null $oOutput The console output interface
     */
    const ROUTES_REWRITE_POST = 'ROUTES:REWRITE:POST';
}
