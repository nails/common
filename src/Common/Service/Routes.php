<?php

/**
 * Manage app routes
 *
 * @package     Nails
 * @subpackage  common
 * @category    Service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Common\Exception\NailsException;
use Nails\Common\Traits\ErrorHandling;
use Nails\Components;
use Nails\Factory;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Routes
 *
 * @package Nails\Common\Service
 */
class Routes
{
    use ErrorHandling;

    // --------------------------------------------------------------------------

    /**
     * Where the routes file should be written to
     *
     * @var string
     */
    protected $sRoutesDir;

    /**
     * Whether the routes can be written
     *
     * @var bool
     */
    protected $bCanWriteRoutes;

    /**
     * The reason, if any, why routes cannot be written
     *
     * @var string
     */
    public $sCantWriteReason;

    /**
     * Ignore requests to rewrite routes
     *
     * @var bool
     */
    protected $bIgnoreRewriteRequests = false;

    /**
     * The routes to write
     *
     * @var array
     */
    protected $aRoutes = [];

    /**
     * The name to give the routes file
     */
    const ROUTES_FILE = 'routes_app.php';

    // --------------------------------------------------------------------------

    /**
     * Constructs the Service
     */
    public function __construct()
    {
        //  Set Defaults
        /** @var FileCache $oFileCache */
        $oFileCache       = Factory::service('FileCache');
        $this->sRoutesDir = $oFileCache->getDir();

        if (!$this->canWriteRoutes()) {
            $this->sCantWriteReason = $this->lastError();
            $this->clearErrors();
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the file to use for saving routes
     *
     * @return string
     */
    public function getRoutesFile(): string
    {
        return $this->sRoutesDir . static::ROUTES_FILE;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets whether rewrite requests are ignored or not
     *
     * @param bool $bIgnore Whether to ignore rewrite requests
     *
     * @return $this
     */
    public function ignoreRewriteRequests(bool $bIgnore): self
    {
        $this->bIgnoreRewriteRequests = $bIgnore;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Update routes
     *
     * @param string          $sModule For which module to restrict the route update
     * @param OutputInterface $oOutput A Symfony OutputInterface to write logs to
     *
     * @return bool
     * @throws \Exception
     */
    public function update(string $sModule = null, OutputInterface $oOutput = null)
    {
        if ($this->bIgnoreRewriteRequests) {
            return true;

        } elseif (!$this->bCanWriteRoutes) {
            $this->setError($this->sCantWriteReason);
            return false;
        }

        // --------------------------------------------------------------------------

        //  Look for modules who wish to write to the routes file
        $aModules        = Components::modules();
        $aModules['app'] = Components::getApp();

        //  Reset routes cache
        $this->aRoutes = [];

        foreach ($aModules as $oModule) {

            if (!empty($sModule) && $sModule !== $oModule->slug) {
                continue;
            }

            $sClass = $oModule->namespace . 'Routes';
            if (class_exists($sClass)) {

                if (!is_null($oOutput)) {
                    $oOutput->write('Generating routes for <info>' . $oModule->slug . '</info>... ');
                }

                $sInterface = 'Nails\\Common\\Interfaces\\RouteGenerator';
                if (!classImplements($sClass, $sInterface)) {
                    throw new NailsException(
                        'Routes generator ' . $sClass . ' does not implement ' . $sInterface
                    );
                }

                $this->aRoutes = array_merge(
                    $this->aRoutes,
                    ['// BEGIN ' . $oModule->name => ''],
                    $sClass::generate(),
                    ['// END ' . $oModule->name => '']
                );

                if (!is_null($oOutput)) {
                    $oOutput->writeln('<info>done</info>');
                }
            }
        }

        // --------------------------------------------------------------------------

        //  Write the file
        if (!is_null($oOutput)) {
            $oOutput->write('Writing routes to file... ');
        }

        if ($this->writeFile()) {

            if (!is_null($oOutput)) {
                $oOutput->writeln('<info>done</info>');
            }
            return true;

        } else {

            if (!is_null($oOutput)) {
                $oOutput->writeln('<error>fail</error>');
            }
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Write the routes file
     *
     * @return bool
     */
    protected function writeFile()
    {
        //  Routes are writable, apparently, give it a bash
        $sData = '<?php' . "\n\n";
        $sData .= '/**' . "\n";
        $sData .= ' * THIS FILE IS CREATED/MODIFIED AUTOMATICALLY' . "\n";
        $sData .= ' * ===========================================' . "\n";
        $sData .= ' *' . "\n";
        $sData .= ' * Any changes you make in this file will be overwritten the' . "\n";
        $sData .= ' * next time the app routes are generated.' . "\n";
        $sData .= ' *' . "\n";
        $sData .= ' * See Nails docs for instructions on how to utilise the' . "\n";
        $sData .= ' * ' . static::ROUTES_FILE . ' file' . "\n";
        $sData .= ' *' . "\n";
        $sData .= ' **/' . "\n\n";

        // --------------------------------------------------------------------------

        foreach ($this->aRoutes as $sKey => $sValue) {
            if (preg_match('#^//.*$#', $sKey)) {
                //  This is a comment
                $sData .= $sKey . "\n";
            } else {
                //  This is a route
                $sData .= '$route[\'' . $sKey . '\']=\'' . $sValue . '\';' . "\n";
            }
        }

        /** @var \DateTime $oDate */
        $oDate = Factory::factory('DateTime');
        $sData .= "\n" . '//LAST GENERATED: ' . $oDate->format('Y-m-d H:i:s');
        $sData .= "\n";

        // --------------------------------------------------------------------------

        $fHandle = @fopen($this->getRoutesFile(), 'w');

        if (!$fHandle) {
            $this->setError('Unable to open routes file for writing.');
            return false;
        }

        if (!fwrite($fHandle, $sData)) {
            fclose($fHandle);
            $this->setError('Unable to write data to routes file.');
            return false;
        }

        fclose($fHandle);

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($this->getRoutesFile(), true);
        }

        return true;
    }

    // --------------------------------------------------------------------------

    /**
     * Determine whether or not the routes can be written
     *
     * @return bool
     */
    public function canWriteRoutes()
    {
        if (!is_null($this->bCanWriteRoutes)) {
            return $this->bCanWriteRoutes;
        }

        $sFile = $this->getRoutesFile();

        //  First, test if file exists, if it does is it writable?
        if (file_exists($sFile)) {

            if (is_writable($sFile)) {

                $this->bCanWriteRoutes = true;
                return true;

            } else {

                //  Attempt to chmod the file
                if (@chmod($sFile, FILE_WRITE_MODE)) {

                    $this->bCanWriteRoutes = true;
                    return true;

                } else {

                    $this->setError('The route config exists, but is not writable.');
                    $this->bCanWriteRoutes = false;
                    return false;
                }
            }

        } elseif (is_writable($this->sRoutesDir)) {

            $this->bCanWriteRoutes = true;
            return true;

        } else {

            //  Attempt to chmod the directory
            if (@chmod($this->sRoutesDir, DIR_WRITE_MODE)) {

                $this->bCanWriteRoutes = true;
                return true;

            } else {

                $this->setError('The route directory is not writable. <small>' . $this->sRoutesDir . '</small>');
                $this->bCanWriteRoutes = false;
                return false;
            }
        }
    }
}
