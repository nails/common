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

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * Excuted before every test
     */
    public function setUp()
    {
        //  @todo (Pablo - 2018-11-16) - Start a transaction
        parent::setUp();
    }

    // --------------------------------------------------------------------------

    /**
     * Excuted after every test
     */
    public function tearDown()
    {
        //  @todo (Pablo - 2018-11-16) - Rollback transaction
        parent::tearDown();
    }
}
