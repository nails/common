<?php

namespace Nails\Common\Helper;

use \Nails\Common\Helper\Inflector;

class InflectorTest extends \PHPUnit_Framework_TestCase
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
     * Tests that possessionise correctly appends the ', 's or 'S to a given string.
     */
    public function testPossessionise()
    {
        //  Lowercase tests
        $sTestString     = 'Rachel';
        $sPossessionised = Inflector::possessionise($sTestString);
        $this->assertEquals($sPossessionised, "Rachel's");

        $sTestString     = 'Ross';
        $sPossessionised = Inflector::possessionise($sTestString);
        $this->assertEquals("Ross'", $sPossessionised);

        //  Uppercase tests
        $sTestString     = 'RACHEL';
        $sPossessionised = Inflector::possessionise($sTestString);
        $this->assertEquals("RACHEL'S", $sPossessionised);

        $sTestString     = 'ROSS';
        $sPossessionised = Inflector::possessionise($sTestString);
        $this->assertEquals("ROSS'", $sPossessionised);
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
