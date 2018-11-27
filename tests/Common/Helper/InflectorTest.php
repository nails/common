<?php

namespace Nails\Common\Helper;

use Nails\Common\Helper\Inflector;
use PHPUnit\Framework\TestCase;

class InflectorTest extends TestCase
{
    /**
     * Construct InflectorTest
     */
    public function __construct()
    {
        require_once dirname(__FILE__) . '/../../../vendor/codeigniter/framework/system/helpers/inflector_helper.php';
    }

    // --------------------------------------------------------------------------

    /**
     * Tests that possessive correctly appends the ', 's or 'S to a given string.
     */
    public function testPossessive()
    {
        //  Lowercase tests
        $sTestString = 'Rachel';
        $sPossessive = Inflector::possessive($sTestString);
        $this->assertEquals($sPossessive, "Rachel's");

        $sTestString = 'Ross';
        $sPossessive = Inflector::possessive($sTestString);
        $this->assertEquals("Ross'", $sPossessive);

        //  Uppercase tests
        $sTestString = 'RACHEL';
        $sPossessive = Inflector::possessive($sTestString);
        $this->assertEquals("RACHEL'S", $sPossessive);

        $sTestString = 'ROSS';
        $sPossessive = Inflector::possessive($sTestString);
        $this->assertEquals("ROSS'", $sPossessive);
    }

    // --------------------------------------------------------------------------

    /**
     * Tests that pluralise correctly pluralises words, taking into consideration English grammar
     */
    public function testPluralise()
    {
        //  Words NOT ending in vowel + y
        //  Maintain singular
        $sTestString = 'aeroplane';
        $sPluralised = Inflector::pluralise(1, $sTestString);
        $this->assertEquals('aeroplane', $sPluralised);

        //  Convert to plural
        $sPluralised = Inflector::pluralise(2, $sTestString);
        $this->assertEquals('aeroplanes', $sPluralised);

        //  Convert to specified plural
        $sPluralised = Inflector::pluralise(2, $sTestString, 'specified plural');
        $this->assertEquals('specified plural', $sPluralised);


        //  Words ending in vowel + y
        $sTestString = 'factory';
        $sPluralised = Inflector::pluralise(1, $sTestString);
        $this->assertEquals('factory', $sPluralised);

        //  Convert to plural
        $sPluralised = Inflector::pluralise(2, $sTestString);
        $this->assertEquals('factories', $sPluralised);

        //  Convert to specified plural
        $sPluralised = Inflector::pluralise(2, $sTestString, 'specified plural');
        $this->assertEquals('specified plural', $sPluralised);
    }
}
