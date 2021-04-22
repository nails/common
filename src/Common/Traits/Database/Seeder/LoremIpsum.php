<?php

namespace Nails\Common\Traits\Database\Seeder;

/**
 * Trait LoremIpsum
 *
 * @package Nails\Common\Traits\Database\Seeder
 */
trait LoremIpsum
{
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
     * @param int    $iNumParagraphs  The number of paragraphs to generate
     * @param string $sFirstParagraph The contents of the first paragraph
     *
     * @return string
     */
    protected function loremParagraph($iNumParagraphs = 1, string $sFirstParagraph = '')
    {
        $aOut     = array_filter([$sFirstParagraph]);
        $aLengths = [5, 6, 8, 10, 12];

        for ($i = count($aOut); $i < $iNumParagraphs; $i++) {
            $iLength = $aLengths[array_rand($aLengths)];
            $aOut[]  = $this->loremSentence($iLength);
        }

        return implode("\n\n", $aOut);
    }

    // --------------------------------------------------------------------------

    /**
     * Generate some ranodm Lorem Ipsum paragraphs as HTML
     *
     * @param int    $iNumParagraphs  The number of paragraphs to generate
     * @param string $sFirstParagraph The contents of the first paragraph
     *
     * @return string
     */
    protected function loremHtml($iNumParagraphs = 3, string $sFirstParagraph = '')
    {
        $sOut = $this->loremParagraph($iNumParagraphs, $sFirstParagraph);
        return '<p>' . str_replace("\n\n", "</p>\n<p>", $sOut) . '</p>';
    }

}
