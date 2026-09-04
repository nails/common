<?php

/**
 * Loads language lines (`{component}/language/{idiom}/{file}_lang.php`) without
 * CodeIgniter, so that lang() works from the console and in tests.
 *
 * @package     Nails
 * @subpackage  common
 * @category    Service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Common\Service;

use Nails\Components;
use Nails\Config;
use Throwable;

/**
 * Class Translation
 *
 * @package Nails\Common\Service
 */
class Translation
{
    public const DEFAULT_IDIOM = 'english';
    public const FILE_SUFFIX   = '_lang.php';

    // --------------------------------------------------------------------------

    /** @var array<string, string> */
    protected array $aLines = [];

    /** @var array<string, true> "{idiom}:{file}" => loaded */
    protected array $aLoaded = [];

    protected bool $bLoadedAll = false;

    protected string $sIdiom;

    // --------------------------------------------------------------------------

    public function __construct(
        ?string $sIdiom = null,
        protected readonly string $sFallbackIdiom = self::DEFAULT_IDIOM,
    ) {
        $this->sIdiom = $sIdiom ?? static::detectIdiom();
        $this->load('nails');
    }

    // --------------------------------------------------------------------------

    /**
     * Works out the idiom to use, using the same inputs as the web controllers
     * (the active user's language, else the app's default)
     */
    public static function detectIdiom(): string
    {
        if (function_exists('activeUser') && function_exists('get_instance')) {
            try {
                $sLanguage = activeUser('language');
                if (is_string($sLanguage) && $sLanguage !== '') {
                    return $sLanguage;
                }
            } catch (Throwable) {
                //  No active user available; use the default
            }
        }

        $sDefault = Config::get('APP_DEFAULT_LANG_CODE', static::DEFAULT_IDIOM);

        return is_string($sDefault) && $sDefault !== '' ? $sDefault : static::DEFAULT_IDIOM;
    }

    // --------------------------------------------------------------------------

    public function getIdiom(): string
    {
        return $this->sIdiom;
    }

    /**
     * Changes the idiom; lines already loaded are reloaded in the new idiom
     */
    public function setIdiom(string $sIdiom): static
    {
        if ($sIdiom === $this->sIdiom) {
            return $this;
        }

        $this->sIdiom = $sIdiom;

        $aLoaded          = array_keys($this->aLoaded);
        $bLoadedAll       = $this->bLoadedAll;
        $this->aLines     = [];
        $this->aLoaded    = [];
        $this->bLoadedAll = false;

        foreach ($aLoaded as $sKey) {
            $this->load(substr($sKey, strpos($sKey, ':') + 1));
        }
        if ($bLoadedAll) {
            $this->loadAll();
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a language file (e.g. `nails` → `nails_lang.php`) from every component,
     * app last so that the app's lines win. Falls back to the fallback idiom when
     * the file does not exist in the requested idiom.
     */
    public function load(string $sFile, ?string $sIdiom = null): static
    {
        //  Tolerate CodeIgniter/HMVC style names (`auth/auth`) and suffixed names (`nails_lang`)
        $sFile  = preg_replace('/' . preg_quote(static::FILE_SUFFIX, '/') . '$|_lang$/', '', basename($sFile));
        $sIdiom = $sIdiom ?? $this->sIdiom;
        $sKey   = $sIdiom . ':' . $sFile;

        if (isset($this->aLoaded[$sKey])) {
            return $this;
        }
        $this->aLoaded[$sKey] = true;

        $iLoaded = 0;
        foreach ($this->getDirectories($sIdiom) as $sDirectory) {
            $sPath = $sDirectory . $sFile . static::FILE_SUFFIX;
            if (is_file($sPath)) {
                $this->loadFile($sPath);
                $iLoaded++;
            }
        }

        if ($iLoaded === 0 && $sIdiom !== $this->sFallbackIdiom) {
            $this->load($sFile, $this->sFallbackIdiom);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Loads every language file of every component
     */
    public function loadAll(?string $sIdiom = null): static
    {
        $sIdiom = $sIdiom ?? $this->sIdiom;

        if ($this->bLoadedAll) {
            return $this;
        }
        $this->bLoadedAll = true;

        $iLoaded = 0;
        foreach ($this->getDirectories($sIdiom) as $sDirectory) {
            foreach (glob($sDirectory . '*' . static::FILE_SUFFIX) ?: [] as $sPath) {
                $this->loadFile($sPath);
                $iLoaded++;
            }
        }

        if ($iLoaded === 0 && $sIdiom !== $this->sFallbackIdiom) {
            $this->bLoadedAll = false;
            $this->loadAll($this->sFallbackIdiom);
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether a line exists
     */
    public function has(string $sKey): bool
    {
        if (!array_key_exists($sKey, $this->aLines) && !$this->bLoadedAll) {
            $this->loadAll();
        }
        return array_key_exists($sKey, $this->aLines);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a line, with parameters substituted (vsprintf for arrays, sprintf
     * for scalars), or false if the line does not exist.
     *
     * @param string            $sKey    The line's key
     * @param array|string|null $mParams Parameters to substitute
     */
    public function line(string $sKey, array|string|null $mParams = null): string|false
    {
        if (!$this->has($sKey)) {
            return false;
        }

        $sLine = $this->aLines[$sKey];

        if (empty($mParams)) {
            return $sLine;
        }

        try {
            return is_array($mParams)
                ? vsprintf($sLine, $mParams)
                : sprintf($sLine, $mParams);
        } catch (Throwable) {
            return $sLine;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets (or overrides) a line at runtime
     */
    public function set(string $sKey, string $sValue): static
    {
        $this->aLines[$sKey] = $sValue;
        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        return $this->aLines;
    }

    // --------------------------------------------------------------------------

    /**
     * Every directory which may hold language files for an idiom, in precedence
     * order (common, modules, drivers, skins, then the app so that it wins).
     * Mirrors the locations CodeIgniter's HMVC loader searched.
     *
     * @return string[]
     */
    protected function getDirectories(string $sIdiom): array
    {
        $aComponents = Components::available();
        $oApp        = array_shift($aComponents);
        $aComponents[] = $oApp;

        $sSuffix = 'language' . DIRECTORY_SEPARATOR . $sIdiom . DIRECTORY_SEPARATOR;
        $aOut    = [];

        foreach ($aComponents as $oComponent) {

            $aRoots = [$oComponent->path];
            if ($oComponent->isApp()) {
                $aRoots[] = $oComponent->path . 'application' . DIRECTORY_SEPARATOR;
                $aRoots   = array_merge(
                    $aRoots,
                    glob($oComponent->path . 'application' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR, GLOB_ONLYDIR) ?: []
                );
            }

            foreach ($aRoots as $sRoot) {
                $aOut[] = $sRoot . $sSuffix;
                //  HMVC sub-module directories, e.g. module-auth/auth/language/
                foreach (glob($sRoot . '*' . DIRECTORY_SEPARATOR . $sSuffix, GLOB_ONLYDIR) ?: [] as $sDir) {
                    $aOut[] = rtrim($sDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                }
            }
        }

        return array_values(array_unique(array_filter($aOut, 'is_dir')));
    }

    // --------------------------------------------------------------------------

    /**
     * Loads a `$lang[...] = ...` file into the line store; later files win
     */
    protected function loadFile(string $sPath): void
    {
        $aLang = (static function (string $sPath): array {
            $lang = [];
            include $sPath;
            return is_array($lang) ? $lang : [];
        })($sPath);

        foreach ($aLang as $sKey => $mValue) {
            if (is_string($mValue)) {
                $this->aLines[$sKey] = $mValue;
            }
        }
    }
}
