<?php

namespace Tests\Common\Helper;

use Nails\Common\Helper\Inflector;
use PHPUnit\Framework\TestCase;

class InflectorTest extends TestCase
{
    const TEST_STRING_RACHEL           = 'Rachel';
    const TEST_STRING_ROSS             = 'Ross';
    const TEST_STRING_AEROPLANE        = 'aeroplane';
    const TEST_STRING_AEROPLANES       = 'aeroplanes';
    const TEST_STRING_FACTORY          = 'factory';
    const TEST_STRING_QUIZ             = 'quiz';
    const TEST_STRING_SPECIFIED_PLURAL = 'specified_plural';

    // --------------------------------------------------------------------------

    /**
     * Construct InflectorTest
     */
    public static function setUpBeforeClass(): void
    {
        require_once dirname(__FILE__) . '/../../../helpers/inflector.php';
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Inflector::possessive()
     */
    public function test_possessive_appends_s_to_string(): void
    {
        $sPossessive = Inflector::possessive(static::TEST_STRING_RACHEL);
        $this->assertEquals(static::TEST_STRING_RACHEL . "'s", $sPossessive);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Inflector::possessive()
     */
    public function test_possessive_appends_s_to_string_ending_in_s(): void
    {
        $sPossessive = Inflector::possessive(static::TEST_STRING_ROSS);
        $this->assertEquals(static::TEST_STRING_ROSS . "'", $sPossessive);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Inflector::possessive()
     */
    public function test_possessive_maintains_case(): void
    {
        $sTestString = strtoupper(static::TEST_STRING_RACHEL);
        $sPossessive = Inflector::possessive($sTestString);
        $this->assertEquals($sTestString . "'S", $sPossessive);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Inflector::pluralise()
     */
    public function test_puralise_does_not_pluralise_when_count_is_one(): void
    {
        $sPluralised = Inflector::pluralise(1, static::TEST_STRING_AEROPLANE);
        $this->assertEquals(static::TEST_STRING_AEROPLANE, $sPluralised);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Inflector::pluralise()
     */
    public function test_puralise_pluralsies_words_when_count_is_greater_than_one(): void
    {
        $sPluralised = Inflector::pluralise(2, static::TEST_STRING_AEROPLANE);
        $this->assertEquals(static::TEST_STRING_AEROPLANE . 's', $sPluralised);

        $sPluralised = Inflector::pluralise(2, static::TEST_STRING_AEROPLANES);
        $this->assertEquals(static::TEST_STRING_AEROPLANES, $sPluralised);

        $sPluralised = Inflector::pluralise(2, static::TEST_STRING_FACTORY);
        $this->assertEquals(substr(static::TEST_STRING_FACTORY, 0, -1) . 'ies', $sPluralised);

        $sPluralised = Inflector::pluralise(2, static::TEST_STRING_QUIZ);
        $this->assertEquals(static::TEST_STRING_QUIZ . 'zes', $sPluralised);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Inflector::pluralise()
     */
    public function test_puralise_uses_specified_plural_when_count_is_greater_than_one(): void
    {
        $sPluralised = Inflector::pluralise(2, static::TEST_STRING_AEROPLANE, static::TEST_STRING_SPECIFIED_PLURAL);
        $this->assertEquals(static::TEST_STRING_SPECIFIED_PLURAL, $sPluralised);

    }
}
