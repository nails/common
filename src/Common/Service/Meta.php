<?php

/**
 * This class provides a convinient way to inject meta tags into the
 * app's header
 *
 * @package     Nails
 * @subpackage  common
 * @category    Library
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

class Meta
{
    private $aEntries = [];

    // --------------------------------------------------------------------------

    /**
     * Returns the raw entries array
     * @return Array
     */
    public function getEntries()
    {
        return array_values($this->aEntries);
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a meta tag, setting all the element keys as tag attributes.
     *
     * @param array $aAttr An array of attributes which make up the entry
     */
    public function addRaw($aAttr)
    {
        if (!empty($aAttr)) {

            $sHash                  = md5(json_encode($aAttr));
            $this->aEntries[$sHash] = $aAttr;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a meta tag
     *
     * @param  array $aAttr An array of attributes which make up the entry
     */
    public function removeRaw($aAttr)
    {
        dd($this->aEntries);
        if (!empty($aAttr)) {

            $sHash = md5(json_encode($aAttr));
            unset($this->aEntries[$sHash]);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a basic meta tag, setting the name and the content attributes
     *
     * @param string $sName    The element's name attribute
     * @param string $sContent The element's content attribute
     * @param string $sTag     The elements's type
     */
    public function add($sName, $sContent, $sTag = '')
    {
        $aMeta = [
            'name'    => $sName,
            'content' => $sContent,
            'tag'     => $sTag,
        ];

        return $this->addRaw($aMeta);
    }

    // --------------------------------------------------------------------------

    /**
     * Removes a basic meta tag
     *
     * @param string $sName    The elements's name attribute
     * @param string $sContent The elements's content attribute
     * @param string $sTag     The elements's type
     */
    public function remove($sName, $sContent, $sTag = '')
    {
        $aMeta = [
            'name'    => $sName,
            'content' => $sContent,
            'tag'     => $sTag,
        ];

        return $this->removeRaw($aMeta);
    }

    // --------------------------------------------------------------------------

    /**
     * Removes items whose propeerties match a defined pattern
     *
     * @param array $aProperties A key/value array of properties and matching patterns
     *
     * @return $this
     */
    public function removeByPropertyPattern($aProperties)
    {
        foreach ($this->aEntries as $sHash => $aEntry) {
            foreach ($aProperties as $aPatterns) {
                foreach ($aPatterns as $sProperty => $sPattern) {
                    if (!array_key_exists($sProperty, $aEntry)) {
                        continue 2;
                    } elseif (!preg_match('/^' . $sPattern . '$/i', $aEntry[$sProperty])) {
                        continue 2;
                    }
                }
                unset($this->aEntries[$sHash]);
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the elements into an array of strings
     * @return array
     */
    public function outputAr()
    {
        $aOut = [];

        foreach ($this->aEntries as $aEntry) {

            $sTemp = !empty($aEntry['tag']) ? '<' . $aEntry['tag'] . ' ' : '<meta ';
            unset($aEntry['tag']);
            foreach ($aEntry as $sKey => $sValue) {

                $sTemp .= $sKey . '="' . $sValue . '" ';
            }
            $sTemp  = trim($sTemp) . '>';
            $aOut[] = $sTemp;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the output as a string
     * @return string
     */
    public function outputStr()
    {
        return implode("\n", $this->outputAr());
    }
}
