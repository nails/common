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

/**
 * Class Meta
 *
 * @package Nails\Common\Service
 */
class Meta
{
    /** @var array */
    private $aEntries = [];

    // --------------------------------------------------------------------------

    /**
     * Returns the raw entries array
     *
     * @return array
     */
    public function getEntries(): array
    {
        return array_values($this->aEntries);
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a meta tag, setting all the element keys as tag attributes.
     *
     * @param array $aAttr An array of attributes which make up the entry
     *
     * @return $this
     */
    public function addRaw($aAttr): self
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
     * @param array $aAttr An array of attributes which make up the entry
     *
     * @return $this
     */
    public function removeRaw($aAttr): self
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
     *
     * @param string $sName    The element's name attribute
     * @param string $sContent The element's content attribute
     * @param string $sTag     The elements's type
     *
     * @return $this
     */
    public function add($sName, $sContent, $sTag = ''): self
    {
        return $this->addRaw([
            'name'    => $sName,
            'content' => $sContent,
            'tag'     => $sTag,
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Removes a basic meta tag
     *
     * @param string $sName    The elements's name attribute
     * @param string $sContent The elements's content attribute
     * @param string $sTag     The elements's type
     *
     * @return $this
     */
    public function remove($sName, $sContent, $sTag = ''): self
    {
        return $this->removeRaw([
            'name'    => $sName,
            'content' => $sContent,
            'tag'     => $sTag,
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Removes items whose propeerties match a defined pattern
     *
     * @param array $aProperties A key/value array of properties and matching patterns
     *
     * @return $this
     */
    public function removeByPropertyPattern($aProperties): self
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
     *
     * @return array
     */
    public function outputAr(): array
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
     *
     * @return string
     */
    public function outputStr(): string
    {
        return implode(PHP_EOL, $this->outputAr());
    }

    // --------------------------------------------------------------------------

    /**
     * Compiles metadata tags from the page's MetaData object
     *
     * @param MetaData $oMetaData The Metadata object
     *
     * @return $this
     */
    public function compileFromMetaData(MetaData $oMetaData): self
    {
        $sAppName     = $oMetaData->getTitles()->implode();
        $sDescription = $oMetaData->getDescription();
        $aKeywords    = array_filter(array_unique($oMetaData->getKeywords()));

        // --------------------------------------------------------------------------

        //  Generic meta
        $this
            ->addRaw([
                'charset' => 'utf-8',
            ])
            ->addRaw([
                'name'    => 'viewport',
                'content' => 'width=device-width, initial-scale=1',
            ]);

        // --------------------------------------------------------------------------

        // Open graph meta
        $this
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:title',
                'content'  => $sAppName,
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:type',
                'content'  => 'website',
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:locale',
                'content'  => (string) $oMetaData->getLocale(),
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:site_name',
                'content'  => $sAppName,
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:url',
                'content'  => current_url(),
            ])
            ->addRaw([
                'tag'      => 'meta',
                'property' => 'og:description',
                'content'  => $sDescription,
            ]);

        // --------------------------------------------------------------------------

        // Twitter card config
        $this
            ->addRaw([
                'name'    => 'twitter:title',
                'content' => $sAppName,
            ])
            ->addRaw([
                'name'    => 'twitter:description',
                'content' => $sDescription,
            ])
            ->addRaw([
                'name'    => 'twitter:site',
                'content' => $oMetaData->getTwitterHandle(),
            ])
            ->addRaw([
                'name'    => 'twitter:card',
                'content' => 'summary_large_image',
            ]);

        // --------------------------------------------------------------------------

        // Image tags
        if ($oMetaData->getImageUrl()) {
            $this
                ->addRaw([
                    'tag'      => 'meta',
                    'property' => 'og:image',
                    'content'  => $oMetaData->getImageUrl(),
                ])
                ->addRaw([
                    'name'    => 'twitter:image',
                    'content' => $oMetaData->getImageUrl(),
                ]);

            if ($oMetaData->getImageWidth()) {
                $this
                    ->addRaw([
                        'tag'      => 'meta',
                        'property' => 'og:image:width',
                        'content'  => $oMetaData->getImageWidth(),
                    ]);
            }

            if ($oMetaData->getImageHeight()) {
                $this
                    ->addRaw([
                        'tag'      => 'meta',
                        'property' => 'og:image:height',
                        'content'  => $oMetaData->getImageHeight(),
                    ]);
            }
        }

        // --------------------------------------------------------------------------

        //  Other meta tags
        $this
            ->add('apple-mobile-web-app-title', $sAppName)
            ->add('application-name', $sAppName)
            ->add('description', $sDescription)
            ->add('keywords', implode(', ', $aKeywords));

        if ($oMetaData->getThemeColour()) {
            $this
                ->add(
                    'theme-color',
                    $oMetaData->getThemeColour()
                )
                ->addRaw([
                    'tag'     => 'meta',
                    'name'    => 'theme-color',
                    'content' => $oMetaData->getThemeColour(),
                ])
                ->addRaw([
                    'tag'     => 'meta',
                    'name'    => 'msapplication-TileColor',
                    'content' => $oMetaData->getThemeColour(),
                ]);
        }

        // --------------------------------------------------------------------------

        //  Canonical URL
        $this
            ->addRaw([
                'tag'  => 'link',
                'rel'  => 'canonical',
                'href' => $oMetaData->getCanonicalUrl() ?: current_url(),
            ]);

        // --------------------------------------------------------------------------

        return $this;
    }
}
