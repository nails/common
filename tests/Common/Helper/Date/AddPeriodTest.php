<?php

namespace Tests\Common\Helper\Date;

use DateTime;
use Nails\Common\Helper\Date;
use PHPUnit\Framework\TestCase;

/**
 * Class AddPeriodTest
 *
 * @package Tests\Common\Helper
 */
class AddPeriodTest extends TestCase
{
    /**
     * Set up the test class
     */
    public static function setUpBeforeClass(): void
    {
        require_once dirname(__FILE__) . '/../../../../helpers/date.php';
    }

    // --------------------------------------------------------------------------

    /**
     * @covers ::dateAddPeriod
     */
    public function test_helper_method_exists()
    {
        $this->assertTrue(function_exists('dateAddPeriod'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_invalid_argument_thrown_for_invalid_period(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Date::addPeriod(new DateTime(), 'invalid', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_same_object_is_returned_zero_period(): void
    {
        $oDate = new DateTime();
        $this->assertSame(
            $oDate,
            Date::addPeriod($oDate, Date::PERIOD_DAY, 0)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_same_object_is_returned_period_day(): void
    {
        $oDate = new DateTime();
        $this->assertSame(
            $oDate,
            Date::addPeriod($oDate, Date::PERIOD_DAY, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_same_object_is_returned_period_month(): void
    {
        $oDate = new DateTime();
        $this->assertSame(
            $oDate,
            Date::addPeriod($oDate, Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_same_object_is_returned_period_year(): void
    {
        $oDate = new DateTime();
        $this->assertSame(
            $oDate,
            Date::addPeriod($oDate, Date::PERIOD_YEAR, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_unmodified_object_is_returned_for_zero_period(): void
    {
        $oDate = new DateTime();
        $this->assertEquals(
            $oDate->format('Y-m-d H:i:s'),
            Date::addPeriod(clone $oDate, Date::PERIOD_MONTH, 0)->format('Y-m-d H:i:s')
        );
    }

    /**
     * --------------------------------------------------------------------------
     *
     * The following tests check certain date scenarios for NON-LEAP years
     *
     * --------------------------------------------------------------------------
     */

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_13_days_to_31_jan_returns_13_feb(): void
    {
        $this->assertEquals(
            new DateTime('2019-02-13'),
            Date::addPeriod(new DateTime('2019-01-31'), Date::PERIOD_DAY, 13)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_29_days_to_31_jan_returns_1_march(): void
    {
        $this->assertEquals(
            new DateTime('2019-03-01'),
            Date::addPeriod(new DateTime('2019-01-31'), Date::PERIOD_DAY, 29)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_1_month_to_12_jan_returns_12_feb(): void
    {
        $this->assertEquals(
            new DateTime('2019-02-12'),
            Date::addPeriod(new DateTime('2019-01-12'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_1_month_to_29_jan_returns_28_feb(): void
    {
        $this->assertEquals(
            new DateTime('2019-02-28'),
            Date::addPeriod(new DateTime('2019-01-29'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_1_month_to_30_jan_returns_28_feb(): void
    {
        $this->assertEquals(
            new DateTime('2019-02-28'),
            Date::addPeriod(new DateTime('2019-01-30'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_1_month_to_31_jan_returns_28_feb(): void
    {
        $this->assertEquals(
            new DateTime('2019-02-28'),
            Date::addPeriod(new DateTime('2019-01-31'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_3_months_to_31_jan_returns_30_apr(): void
    {
        $this->assertEquals(
            new DateTime('2019-04-30'),
            Date::addPeriod(new DateTime('2019-01-31'), Date::PERIOD_MONTH, 3)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_4_months_to_31_jan_returns_31_may(): void
    {
        $this->assertEquals(
            new DateTime('2019-05-31'),
            Date::addPeriod(new DateTime('2019-01-31'), Date::PERIOD_MONTH, 4)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_1_month_to_28_feb_returns_28_mar(): void
    {
        $this->assertEquals(
            new DateTime('2019-03-28'),
            Date::addPeriod(new DateTime('2019-02-28'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_1_month_to_29_sep_returns_29_oct(): void
    {
        $this->assertEquals(
            new DateTime('2019-10-29'),
            Date::addPeriod(new DateTime('2019-09-29'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_1_month_to_30_oct_returns_30_nov(): void
    {
        $this->assertEquals(
            new DateTime('2019-11-30'),
            Date::addPeriod(new DateTime('2019-10-30'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_adding_1_year_to_31_jan_returns_31_jan(): void
    {
        $this->assertEquals(
            new DateTime('2020-01-31'),
            Date::addPeriod(new DateTime('2019-01-31'), Date::PERIOD_YEAR, 1)
        );
    }

    /**
     * --------------------------------------------------------------------------
     *
     * The following tests check certain date scenarios for LEAP years
     *
     * --------------------------------------------------------------------------
     */

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_leap_adding_1_day_to_28_feb_returns_29_feb(): void
    {
        $this->assertEquals(
            new DateTime('2020-02-29'),
            Date::addPeriod(new DateTime('2020-02-28'), Date::PERIOD_DAY, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_leap_adding_1_month_to_31_jan_returns_29_feb(): void
    {
        $this->assertEquals(
            new DateTime('2020-02-29'),
            Date::addPeriod(new DateTime('2020-01-31'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_leap_adding_1_month_to_30_jan_returns_29_feb(): void
    {
        $this->assertEquals(
            new DateTime('2020-02-29'),
            Date::addPeriod(new DateTime('2020-01-30'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_leap_adding_1_month_to_29_jan_returns_29_feb(): void
    {
        $this->assertEquals(
            new DateTime('2020-02-29'),
            Date::addPeriod(new DateTime('2020-01-29'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_leap_adding_1_month_to_29_feb_returns_29_mar(): void
    {
        $this->assertEquals(
            new DateTime('2020-03-29'),
            Date::addPeriod(new DateTime('2020-02-29'), Date::PERIOD_MONTH, 1)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Date::addPeriod()
     */
    public function test_leap_adding_1_year_to_29_feb_returns_28_feb(): void
    {
        $this->assertEquals(
            new DateTime('2021-02-28'),
            Date::addPeriod(new DateTime('2020-02-29'), Date::PERIOD_YEAR, 1)
        );
    }
}
