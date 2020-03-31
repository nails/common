<?php

namespace Tests\Common\Resource\DateTime;

use Exception;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource\DateTime;
use Nails\Factory;
use PHPUnit\Framework\TestCase;

class IsAfterTest extends TestCase
{
    /**
     * @covers \Nails\Common\Resource\DateTime::isAfter
     * @throws FactoryException
     */
    public function test_throws_exception_with_invalid_type()
    {
        $oNow = new \DateTime();
        /** @var DateTime $oDate */
        $oDate = Factory::resource('DateTime', null, ['raw' => $oNow->format('Y-m-d H:i:s')]);
        $this->expectException(\InvalidArgumentException::class);
        $oDate->isAfter(null);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\DateTime::isAfter
     * @throws FactoryException
     * @throws Exception
     */
    public function test_returns_false_with_no_date()
    {
        /** @var DateTime $oDate */
        $oDate = Factory::resource('DateTime', null, ['raw' => null]);

        $oCompareWithNative = new \DateTime();
        $oCompareWithNails  = Factory::resource('DateTime', null, ['raw' => $oCompareWithNative->format('Y-m-d H:i:s')]);

        $this->assertFalse($oDate->isAfter($oCompareWithNative));
        $this->assertFalse($oDate->isAfter($oCompareWithNails));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\DateTime::isAfter
     * @throws FactoryException
     * @throws Exception
     */
    public function test_returns_false_with_future_date()
    {
        $oNow = new \DateTime();

        /** @var DateTime $oDate */
        $oDate = Factory::resource('DateTime', null, ['raw' => $oNow->format('Y-m-d H:i:s')]);

        $oCompareWithNative = new \DateTime('+1 hour');
        $oCompareWithNails  = Factory::resource('DateTime', null, ['raw' => $oCompareWithNative->format('Y-m-d H:i:s')]);

        $this->assertFalse($oDate->isAfter($oCompareWithNative));
        $this->assertFalse($oDate->isAfter($oCompareWithNails));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\DateTime::isAfter
     * @throws FactoryException
     * @throws Exception
     */
    public function test_returns_true_with_past_date()
    {
        $oNow = new \DateTime();

        /** @var DateTime $oDate */
        $oDate = Factory::resource('DateTime', null, ['raw' => $oNow->format('Y-m-d H:i:s')]);

        $oCompareWithNative = new \DateTime('-1 hour');
        $oCompareWithNails  = Factory::resource('DateTime', null, ['raw' => $oCompareWithNative->format('Y-m-d H:i:s')]);

        $this->assertTrue($oDate->isAfter($oCompareWithNative));
        $this->assertTrue($oDate->isAfter($oCompareWithNails));
    }
}
