<?php

/**
 * The Clean Cron task
 *
 * @package  App
 * @category Task
 */

namespace Nails\Common\Cron\Task\Logs;

use Nails\Cron\Task\Base;

/**
 * Class Clean
 *
 * @package Nails\Common\Cron\Task\Logs
 */
class Clean extends Base
{
    /**
     * The task description
     *
     * @var string
     */
    const DESCRIPTION = 'Deletes old log files';

    /**
     * The cron expression of when to run
     *
     * @var string
     */
    const CRON_EXPRESSION = '0 0 * * *';

    /**
     * The console command to execute
     *
     * @var string
     */
    const CONSOLE_COMMAND = 'logs:clean';
}
