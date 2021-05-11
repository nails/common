<?php

namespace Nails\Common\Service;

use MimeTyper\Repository\MimeDbRepository;
use Nails\Common\Helper\ArrayHelper;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypesInterface;

/**
 * Class Mime
 *
 * @package Nails\Common\Service
 */
class Mime
{
    /**
     * The path to the application's mime config file
     *
     * @var string
     */
    const APP_MIME_FILE = NAILS_APP_PATH . 'application/config/mimes.php';

    /**
     * The path to the Nails mime config file
     *
     * @var string
     */
    const NAILS_MIME_FILE = NAILS_COMMON_PATH . 'config/mimes.php';

    // --------------------------------------------------------------------------

    /**
     * The mime databse
     *
     * @var MimeDbRepository
     */
    protected $oDatabase;

    /**
     * The mime detector
     *
     * @var MimeTypesInterface
     */
    protected $oDetector;

    /**
     * A map of extensions and their valid mime types
     *
     * @var string[]
     */
    protected static $aMapExtensionToMimes;

    /**
     * A map of mime types and their valid extensions
     *
     * @var string[]
     */
    protected static $aMapMimeToExtensions;

    // --------------------------------------------------------------------------

    /**
     * Mime constructor.
     *
     * @param MimeDbRepository   $oDatabase The mime database to use
     * @param MimeTypesInterface $oDetector The mime detector to use
     */
    public function __construct(
        MimeDbRepository $oDatabase,
        MimeTypesInterface $oDetector
    ) {
        $this->oDatabase = $oDatabase;
        $this->oDetector = $oDetector;

        static::$aMapExtensionToMimes = $oDatabase->dumpExtensionToType();
        static::$aMapMimeToExtensions = $oDatabase->dumpTypeToExtensions();

        if (file_exists(static::APP_MIME_FILE)) {
            include static::APP_MIME_FILE;
        } else {
            include static::NAILS_MIME_FILE;
        }

        if (empty($mimes)) {
            $mimes = [];
        }

        foreach ($mimes as $sExtension => $mMimes) {
            $this->addExtension($sExtension, (array) $mMimes);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Detect a file's mimetype, first using the system, followed by the detector
     *
     * @param string $sFile The path to the file to detect
     *
     * @return string
     * @throws \Exception
     */
    public function detectFromFile(string $sFile): string
    {
        if (!file_exists($sFile)) {
            return '';
        }

        $rHandle = finfo_open(FILEINFO_MIME_TYPE);
        $sMime   = finfo_file($rHandle, $sFile);

        if (($sMime === 'application/octet-stream' || empty($sMime)) && $this->oDetector->isGuesserSupported()) {
            $sMime = $this->oDetector->guessMimeType($sFile);
        }

        return $sMime ?? 'application/octet-stream';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the mime to extension map
     *
     * @return array
     */
    public function getMimeMap(): array
    {
        return static::$aMapMimeToExtensions;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the extension to mime map
     *
     * @return array
     */
    public function getExtensionMap(): array
    {
        return static::$aMapExtensionToMimes;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a new mime type to the map and its associated extensions
     *
     * @param string $sMime       The mime type to add
     * @param array  $aExtensions An array of acceptable extensions
     *
     * @return Mime
     */
    public function addMime(string $sMime, array $aExtensions): self
    {
        $this->updateMap(static::$aMapMimeToExtensions, $sMime, $aExtensions);
        foreach ($aExtensions as $sExtension) {
            $this->updateMap(static::$aMapExtensionToMimes, $sExtension, [$sMime]);
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds a new extension to the map and its associated mime types
     *
     * @param string $sExtension the extension to add
     * @param array  $aMimes     An array of acceptable mime types
     *
     * @return Mime
     */
    public function addExtension(string $sExtension, array $aMimes): self
    {
        $this->updateMap(static::$aMapExtensionToMimes, $sExtension, $aMimes);
        foreach ($aMimes as $sMime) {
            $this->updateMap(static::$aMapMimeToExtensions, $sMime, [$sExtension]);
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Updates a map array
     *
     * @param array  $aMap    The array to update
     * @param string $sKey    The key to update
     * @param array  $aValues The values to add
     *
     * @return Mime
     */
    protected function updateMap(array &$aMap, string $sKey, array $aValues): self
    {
        if (!array_key_exists($sKey, $aMap)) {
            $aMap[$sKey] = [];
        }

        $aMap[$sKey] = array_values(
            array_unique(
                array_merge(
                    $aMap[$sKey],
                    $aValues
                )
            )
        );

        return $this;
    }


    // --------------------------------------------------------------------------

    /**
     * Get extensions for a given mime type
     *
     * @param string $sMime The mime type to query
     *
     * @return string[]
     */
    public function getExtensionsForMime(string $sMime): array
    {
        return ArrayHelper::get($sMime, static::$aMapMimeToExtensions, []);
    }

    // --------------------------------------------------------------------------

    /**
     * Get mimes for a given extension
     *
     * @param string $sExtension The extension to query
     *
     * @return string[]
     */
    public function getMimesForExtension(string $sExtension): array
    {
        return ArrayHelper::get($sExtension, static::$aMapExtensionToMimes, []);
    }
}
