<?php

namespace Nails\Common\Service;

use MimeType\MimeType;
use MimeTyper\Repository\MimeDbRepository;

/**
 * Class Mime
 *
 * @package Nails\Common\Service
 */
class Mime
{
    /**
     * The mime databse
     *
     * @var MimeDbRepository
     */
    protected $oDatabase;

    /**
     * The mime detector
     *
     * @var MimeType
     */
    protected $oDetector;

    // --------------------------------------------------------------------------

    /**
     * Mime constructor.
     *
     * @param MimeDbRepository $oDatabase The mime database to use
     * @param MimeType         $oDetector The mime detector to use
     */
    public function __construct(
        MimeDbRepository $oDatabase,
        MimeType $oDetector
    ) {
        $this->oDatabase = $oDatabase;
        $this->oDetector = $oDetector;
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

        if ($sMime === 'application/octet-stream' || empty($sMime)) {
            $sMime = $this->oDetector->getType($sFile);
        }

        return $sMime;
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
        return $this->oDatabase->findExtensions($sMime);
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
        return $this->oDatabase->findTypes($sExtension);
    }
}
