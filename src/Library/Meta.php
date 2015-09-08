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

namespace Nails\Common\Library;

class Meta
{
    private $aEntries = array();

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
     * @param array $aAttr An array of attributes which make up the entry
     */
    public function addRaw($aAttr)
    {
        if (!empty($aAttr)) {

            //  Hash to identify the meta tag (so it can be removed easily)
            $sHash = md5(json_encode($aAttr));

            $this->aEntries[$sHash] = $aAttr;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a meta tag
     * @param  array $aAttr An array of attributes which make up the entry
     */
    public function removeRaw($aAttr)
    {
        if (!empty($aAttr)) {

            $sHash = md5(json_encode($aAttr));
            unset($this->aEntries[$sHash]);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a basic meta tag, setting the name and the content attributes
     * @param string $sName    The meta tag's name attribute
     * @param string $sContent The meta tag's content attribute
     */
    public function add($sName, $sContent)
    {
        $aMeta = array(
            'name' => $sName,
            'content' => $sContent
        );

        return $this->addRaw($aMeta);
    }

    // --------------------------------------------------------------------------

    /**
     * Removes a basic meta tag
     * @param string $sName    The meta tag's name attribute
     * @param string $sContent The meta tag's content attribute
     */
    public function remove($sName, $sContent)
    {
        $aMeta = array(
            'name' => $sName,
            'content' => $sContent
        );

        return $this->removeRaw($aMeta);
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles the meta tags into an array of strings
     * @return array
     */
    public function outputAr()
    {
        $aOut = array();

        foreach ($this->aEntries as $aEntry) {

            $sTemp = '<meta ';
            foreach ($aEntry as $sKey => $sValue) {

                $sTemp .= $sKey . '="' . $sValue . '" ';
            }
            $sTemp .= '/>';
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
