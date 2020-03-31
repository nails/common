<?php

namespace Tests\Common\Resource\DateTime;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource\DateTime;
use Nails\Factory;
use PHPUnit\Framework\TestCase;

class IsPastTest extends TestCase
{
    /**
     * @covers \Nails\Common\Resource\Date::isPast
     * @throws FactoryException
     */
    public function test_defaults_to_now()
    {
        $oNow = new \DateTime('+1 hour');

        /** @var DateTime $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => $oNow->format('Y-m-d H:i:s')]);

        $this->assertFalse($oDate->isPast());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\Date::isPast
     * @throws FactoryException
     */
    public function test_returns_true_when_self_is_past()
    {
        $oNow = new \DateTime('-1 hour');

        /** @var DateTime $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => $oNow->format('Y-m-d H:i:s')]);

        $this->assertTrue($oDate->isPast());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\Date::isPast
     * @throws FactoryException
     */
    public function test_returns_false_when_self_is_future()
    {
        $oNow = new \DateTime('+1 hour');

        /** @var DateTime $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => $oNow->format('Y-m-d H:i:s')]);

        $this->assertFalse($oDate->isPast());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\Date::isPast
     * @throws FactoryException
     */
    public function test_returns_false_when_supplied_is_past()
    {
        $oNow  = new \DateTime();
        $oPast = new \DateTime('-1 hour');

        /** @var DateTime $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => $oNow->format('Y-m-d H:i:s')]);

        $this->assertFalse($oDate->isPast($oPast));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Resource\Date::isPast
     * @throws FactoryException
     */
    public function test_returns_true_when_supplied_is_future()
    {
        $oNow  = new \DateTime();
        $oPast = new \DateTime('+1 hour');

        /** @var DateTime $oDate */
        $oDate = Factory::resource('Date', null, ['raw' => $oNow->format('Y-m-d H:i:s')]);

        $this->assertTrue($oDate->isPast($oPast));
    }
}
