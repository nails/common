<?php

/**
 * This class is the main base for all tests in a Nails application and provides
 * common functionality and convenience methods.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Tests
 * @author      Nails Dev Team
 */

namespace Nails\Common\Tests;

use Nails\Factory;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Executed before every test
     */
    public function setUp(): void
    {
        parent::setUp();
        $oDb = Factory::service('Database');
        $oDb->transaction()->start();
    }

    // --------------------------------------------------------------------------

    /**
     * Executed after every test
     */
    public function tearDown(): void
    {
        parent::tearDown();
        $oDb = Factory::service('Database');
        $oDb->transaction()->rollback();
    }
}
