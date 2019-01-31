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
    public function setUp()
    {
        parent::setUp();
        $oDb = Factory::service('Database');
        $oDb->trans_begin();
    }

    // --------------------------------------------------------------------------

    /**
     * Executed after every test
     */
    public function tearDown()
    {
        parent::tearDown();
        $oDb = Factory::service('Database');
        $oDb->trans_rollback();
    }
}
