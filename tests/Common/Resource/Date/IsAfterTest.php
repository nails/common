<?php

namespace Tests\Common\Resource\Date;

use DateTime;
use Exception;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource\Date;
use Nails\Factory;
use PHPUnit\Framework\TestCase;

class IsAfterTest extends TestCase
{
    /**
     * @covers \Nails\Common\Resource\Date::isAfter
     * @throws FactoryException
     */
    public function test_throws_exception_with_invalid_type()
    {
        $oNow = new DateTime();
        /** @var Date $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => $oNow->format('Y-m-d')]);
        $this->expectException(\InvalidArgumentException::class);
        $oDate->isAfter(null);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\Date::isAfter
     * @throws FactoryException
     * @throws Exception
     */
    public function test_returns_false_with_no_date()
    {
        /** @var Date $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => null]);

        $oCompareWithNative = new DateTime();
        $oCompareWithNails  = Factory::resource('Date', null, ['raw' => $oCompareWithNative->format('Y-m-d')]);

        $this->assertFalse($oDate->isAfter($oCompareWithNative));
        $this->assertFalse($oDate->isAfter($oCompareWithNails));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\Date::isAfter
     * @throws FactoryException
     * @throws Exception
     */
    public function test_returns_false_with_future_date()
    {
        $oNow = new DateTime();

        /** @var Date $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => $oNow->format('Y-m-d')]);

        $oCompareWithNative = new DateTime('+1 day');
        $oCompareWithNails  = Factory::resource('Date', null, ['raw' => $oCompareWithNative->format('Y-m-d')]);

        $this->assertFalse($oDate->isAfter($oCompareWithNative));
        $this->assertFalse($oDate->isAfter($oCompareWithNails));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\Date::isAfter
     * @throws FactoryException
     * @throws Exception
     */
    public function test_returns_true_with_past_date()
    {
        $oNow = new DateTime();

        /** @var Date $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => $oNow->format('Y-m-d')]);

        $oCompareWithNative = new DateTime('-1 day');
        $oCompareWithNails  = Factory::resource('Date', null, ['raw' => $oCompareWithNative->format('Y-m-d')]);

        $this->assertTrue($oDate->isAfter($oCompareWithNative));
        $this->assertTrue($oDate->isAfter($oCompareWithNails));
    }
}
