<?php

namespace Nails\Common\Console\Seed;

use Nails\Factory;

class Base
{
    /**
     * The database object
     *
     * @var \Nails\Console\Database
     */
    protected $oDb;

    // --------------------------------------------------------------------------

    /**
     * Base constructor.
     *
     * @param $oDb
     */
    public function __construct($oDb)
    {
        $this->oDb = $oDb;
    }

    // --------------------------------------------------------------------------

    /**
     * Execute any pre-seed setup in here
     */
    public function pre()
    {
    }

    // --------------------------------------------------------------------------

    /**
     * The main seeding method
     */
    public function execute()
    {
    }

    // --------------------------------------------------------------------------

    /**
     * Perform any post-seed cleaning up here
     */
    public function post()
    {
    }

    // --------------------------------------------------------------------------

    /**
     * Generate some random Lorem Ipsum words
     *
     * @param int $iNumWords The number of words to generate
     *
     * @return string
     */
    protected function loremWord($iNumWords = 5)
    {
        $aWords = [
            'lorem',
            'ipsum',
            'dolor',
            'sit',
            'amet',
            'consectetur',
            'adipiscing',
            'elit',
            'mauris',
            'venenatis',
            'metus',
            'volutpat',
            'hendrerit',
            'interdum',
            'nisi',
            'odio',
            'finibus',
            'ex',
            'eu',
            'congue',
            'mauris',
            'nisi',
            'in',
            'magna',
            'ut',
            'gravida',
            'neque',
            'at',
            'nulla',
            'viverra',
            'egestas',
            'vel',
            'et',
            'ante',
            'maecenas',
            'hendrerit',
            'sit',
            'amet',
            'urna',
            'posuere',
            'ultrices',
            'aenean',
            'quis',
            'velit',
            'velit',
            'suspendisse',
            'sit',
            'amet',
            'egestas',
            'tortor',
        ];

        $aOut = [];

        for ($i = 0; $i < $iNumWords; $i++) {
            $aOut[] = $aWords[array_rand($aWords)];
        }

        return implode(' ', $aOut);
    }

    // --------------------------------------------------------------------------

    /**
     * Generate some random Lorem Ipsum sentences
     *
     * @param int $iNumSentences The number of sentences to generate
     *
     * @return string
     */
    protected function loremSentence($iNumSentences = 1)
    {
        $aOut     = [];
        $aLengths = [5, 6, 8, 10, 12];

        for ($i = 0; $i < $iNumSentences; $i++) {
            $iLength = $aLengths[array_rand($aLengths)];
            $aOut[]  = ucfirst($this->loremWord($iLength));
        }

        return implode('. ', $aOut) . '.';
    }

    // --------------------------------------------------------------------------

    /**
     * Generate some random Lorem Ipsum paragraphs
     *
     * @param int $iNumParagraphs The number of paragraphs to generate
     *
     * @return string
     */
    protected function loremParagraph($iNumParagraphs = 1)
    {
        $aOut     = [];
        $aLengths = [5, 6, 8, 10, 12];

        for ($i = 0; $i < $iNumParagraphs; $i++) {
            $iLength = $aLengths[array_rand($aLengths)];
            $aOut[]  = $this->loremSentence($iLength);
        }

        return implode("\n\n", $aOut);
    }

    // --------------------------------------------------------------------------

    /**
     * Generate some ranodm Lorem Ipsum paragraphs as HTML
     *
     * @param int $iNumParagraphs The number of paragraphs to generate
     *
     * @return string
     */
    protected function loremHtml($iNumParagraphs = 3)
    {
        $sOut = $this->loremParagraph($iNumParagraphs);
        return '<p>' . str_replace("\n\n", "</p>\n<p>", $sOut) . '</p>';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a random ID from a particular model
     *
     * @param string $sModel    The model to use
     * @param string $sProvider The model's provider
     * @param array  $aData     Any data to pass to the model
     *
     * @return int|null
     */
    protected function randomId($sModel, $sProvider, $aData = [])
    {
        $oModel   = Factory::model($sModel, $sProvider);
        $aResults = $oModel->getAll(0, 1, $aData + ['sort' => [['id', 'random']]]);
        $oRow     = reset($aResults);

        return $oRow ? $oRow->id : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Randomly returns true or false
     *
     * @return bool
     */
    protected function randomBool()
    {
        return (bool) rand(0, 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random datetime, optionally restricted between bounds
     *
     * @param string $sLow    The lowest possible datetime to return
     * @param string $sHigh   The highest possible datetime to return
     * @param string $sFormat The format to return the datetime value in
     *
     * @return string
     */
    protected function randomDateTime($sLow = null, $sHigh = null, $sFormat = 'Y-m-d H:i:s')
    {
        $iLow  = $sLow ? strtotime($sLow) : strtotime('last year');
        $iHigh = $sHigh ? strtotime($sHigh) : strtotime('next year');
        return date($sFormat, rand($iLow, $iHigh));
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random datetime from the future, optionally restricted to a upper bound
     *
     * @param string $sHigh The highest possible datetime to return
     *
     * @return string
     */
    protected function randomFutureDateTime($sHigh = null)
    {
        $oNow = Factory::factory('DateTime');
        return $this->randomDateTime($oNow->format('Y-m-d H:i:s'), $sHigh);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random datetime from the past, optionally restricted to a lower bound
     *
     * @param string $sLow The lowest possible datetime to return
     *
     * @return string
     */
    protected function randomPastDateTime($sLow = null)
    {
        $oNow = Factory::factory('DateTime');
        return $this->randomDateTime($sLow, $oNow->format('Y-m-d H:i:s'));
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random date, optionally restricted between bounds
     *
     * @param string $sLow    The lowest possible date to return
     * @param string $sHigh   The highest possible date to return
     * @param string $sFormat The format to return the datetime value in
     *
     * @return string
     */
    protected function randomDate($sLow = null, $sHigh = null, $sFormat = 'Y-m-d')
    {
        $iLow  = $sLow ? strtotime($sLow) : strtotime('last year');
        $iHigh = $sHigh ? strtotime($sHigh) : strtotime('next year');
        return date($sFormat, rand($iLow, $iHigh));
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random date from the future, optionally restricted to a upper bound
     *
     * @param string $sHigh The highest possible date to return
     *
     * @return string
     */
    protected function randomFutureDate($sHigh = null)
    {
        $oNow = Factory::factory('DateTime');
        return $this->randomDateTime($oNow->format('Y-m-d'), $sHigh);
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random date from the past, optionally restricted to a lower bound
     *
     * @param string $sLow The lowest possible date to return
     *
     * @return string
     */
    protected function randomPastDate($sLow = null)
    {
        $oNow = Factory::factory('DateTime');
        return $this->randomDateTime($sLow, $oNow->format('Y-m-d'));
    }

    // --------------------------------------------------------------------------

    /**
     * Return a random integer
     *
     * @param integer $iLow  The lowest possible value to return
     * @param integer $iHigh The highest possible value to return
     *
     * @return integer
     */
    protected function randomInteger($iLow = 0, $iHigh = 1000)
    {
        return rand($iLow, $iHigh);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a random element from a supplied array
     *
     * @param array $aItems
     *
     * @return mixed
     */
    protected function randomItem($aItems = [])
    {
        return $aItems[array_rand($aItems)];
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a random email address
     *
     * @return string
     */
    protected function email($sDomain = 'example.com')
    {
        return str_replace(' ', '-', $this->loremWord(3)) . '@' . $sDomain;
    }
}
