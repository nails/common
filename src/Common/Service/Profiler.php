<?php

namespace Nails\Common\Service;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Config;
use Nails\Factory;

/**
 * Class Profiler
 *
 * @package Nails\Common\Service
 */
class Profiler
{
    const REPORT_TYPE_JSON = 'JSON';
    const REPORT_TYPE_HTML = 'HTML';

    const HEADER_PROFILER_ENABLE = 'X-Profiler-Enabled';

    /**
     * The values to give the various sections of the HTML profiler
     */
    const HTML_CLASS_PROFILER                          = 'profiler';
    const HTML_CLASS_PROFILER_SECTION                  = self::HTML_CLASS_PROFILER . '__section';
    const HTML_CLASS_PROFILER_TABLE_PROPERTY           = self::HTML_CLASS_PROFILER . '__table--property';
    const HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW       = self::HTML_CLASS_PROFILER_TABLE_PROPERTY . '__row';
    const HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW_KEY   = self::HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW . '__key';
    const HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW_VALUE = self::HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW . '__value';
    const HTML_CLASS_PROFILER_TABLE_DATA               = self::HTML_CLASS_PROFILER . '__table--data';
    const HTML_CLASS_PROFILER_TABLE_DATA_ROW           = self::HTML_CLASS_PROFILER_TABLE_DATA . '__row';
    const HTML_CLASS_PROFILER_TABLE_DATA_ROW_VALUE     = self::HTML_CLASS_PROFILER_TABLE_DATA_ROW . '__value';

    // --------------------------------------------------------------------------

    /**
     * Whether profiling is enabled or not. The Profiler is enabled internally so it can greedily
     * capture bootstrapping marks.
     *
     * @var bool
     */
    protected static bool $bEnabled = true;

    /**
     * Recorded marks
     *
     * @var array
     */
    protected static array $aMarks = [];

    /**
     * The type of report to generate (HTML or JSON))
     *
     * @var string|null
     */
    protected static ?string $sReportType = null;

    // --------------------------------------------------------------------------

    public static function headerSecret(int $windowSeconds = 60): string
    {
        $window = (int) floor(time() / $windowSeconds);
        return hash_hmac('sha256', (string) $window, Config::get('PRIVATE_KEY'));
    }

    // --------------------------------------------------------------------------

    public static function isValidHeaderSecret(string $sSecret, int $windowSeconds = 60): bool
    {
        $window = (int) floor(time() / $windowSeconds);
        foreach ([$window, $window - 1] as $w) {
            if (hash_equals(hash_hmac('sha256', (string) $w, Config::get('PRIVATE_KEY')), $sSecret)) {
                return true;
            }
        }
        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether profiling is enabled or not
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return static::$bEnabled;
    }

    // --------------------------------------------------------------------------

    /**
     * Enable profiling
     */
    public static function enable(): void
    {
        static::$bEnabled = true;
    }

    // --------------------------------------------------------------------------

    /**
     * Disable profiling
     */
    public static function disable(): void
    {
        static::$bEnabled = false;
    }

    // --------------------------------------------------------------------------

    /**
     * Marks a point
     *
     * @param string|null $sLabel
     */
    public static function mark(?string $sLabel = null): void
    {
        if (static::isEnabled()) {

            if (!$sLabel) {
                $aBacktrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
                $aImmediate = $aBacktrace[0];
                $aCaller    = $aBacktrace[1];
                $sLabel     = (!empty($aCaller['class']) ? $aCaller['class'] . '->' : '') .
                    (!empty($aCaller['function']) ? $aCaller['function'] . '()' : '<unknown>()') .
                    (!empty($aImmediate['line']) ? '@L' . $aImmediate['line'] : '');
            }

            static::$aMarks[] = [
                'Label'     => $sLabel,
                'Timestamp' => microtime(true),
            ];
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the type of report to generate
     *
     * @param string $sReportType
     *
     * @throws NailsException
     */
    public static function setReportType(string $sReportType): void
    {
        $aValidTypes = [
            static::REPORT_TYPE_HTML,
            static::REPORT_TYPE_JSON,
        ];

        if (!in_array($sReportType, $aValidTypes)) {
            throw new NailsException(sprintf(
                'Invalid report type "%s", must be one of: %s',
                $sReportType,
                implode(', ', $aValidTypes)
            ));
        }

        static::$sReportType = $sReportType;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current report type, setting a default if not set
     */
    public static function getReportType(): string
    {
        return static::$sReportType =
            static::$sReportType
            ?? Config::get('PROFILER_REPORT_TYPE')
            ?? static::REPORT_TYPE_JSON;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates the profiling report
     *
     * @return string
     * @throws FactoryException
     * @throws NailsException
     */
    public function generateReport(): string
    {
        if (!static::isEnabled()) {
            throw new NailsException('Profiling is disabled');
        }

        $oReport = (object) [
            'Timestamps' => $this->summariseMarks(),
            'Database'   => $this->summariseQueries(),
        ];

        return match (self::getReportType()) {
            static::REPORT_TYPE_HTML => $this->generateReportHtml($oReport),
            static::REPORT_TYPE_JSON => $this->generateReportJson($oReport),
            default => throw new NailsException('Invalid report type: ' . static::$sReportType),
        };
    }

    // --------------------------------------------------------------------------

    /**
     * Writes a JSON report to disk
     *
     * @param object $oReport
     *
     * @throws FactoryException
     */
    protected function generateReportJson(object $oReport): string
    {
        /** @var FileCache $oFileCache */
        $oFileCache = Factory::service('FileCache');
        /** @var DateTime $oNow */
        $oNow = Factory::factory('DateTime');

        $oFileCache->write(
            json_encode($oReport, JSON_PRETTY_PRINT),
            'profiler-' . $oNow->format('Y-m-d-H-i-s') . '.json'
        );

        //  Return an empty string for output; JSON reports go to disk
        return '';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an HTML report of the profiling data
     *
     * @param object $oReport
     *
     * @return string
     */
    protected function generateReportHtml(object $oReport): string
    {
        ob_start();
        $this->renderStyles();
        $this->renderJavascript();
        ?>
        <div class="<?=static::HTML_CLASS_PROFILER?>">
            <h1>Profiler</h1>
            <?php
            foreach ($oReport as $sSection => $oData) {
                ?>
                <section class="<?=static::HTML_CLASS_PROFILER_SECTION?>">
                    <h2><?=$sSection?></h2>
                    <table class="<?=static::HTML_CLASS_PROFILER_TABLE_PROPERTY?>">
                        <tbody>
                            <?php
                            foreach ($oData as $sProperty => $mValue) {
                                if (!is_array($mValue)) {
                                    $this->renderPropertyRow($sProperty, $mValue);
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                    foreach ($oData as $sProperty => $mValue) {
                        if (is_array($mValue)) {

                            $aFirst   = reset($mValue);
                            $aColumns = array_keys($aFirst);
                            ?>
                            <h3><?=$sProperty?></h3>
                            <table class="<?=static::HTML_CLASS_PROFILER_TABLE_DATA?>">
                                <thead>
                                    <tr>
                                        <?php
                                        foreach ($aColumns as $sColumn) {
                                            ?>
                                            <th><?=$sColumn?></th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($mValue as $aRow) {
                                        $this->renderDataRow($aRow);
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <?php
                        }
                    }
                    ?>
                </section>
                <?php
            }
            ?>
        </div>
        <?php

        return ob_get_clean();
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the report javascript
     */
    protected function renderJavascript()
    {
    }

    // --------------------------------------------------------------------------

    /**
     * Renders the report styles
     */
    protected function renderStyles()
    {
        echo styleOpen();
        ?>
            .<?=static::HTML_CLASS_PROFILER?> {
                padding: 1rem;
                margin-top: 5rem;
                border-top: 2px solid #ccc;
            }

            .<?=static::HTML_CLASS_PROFILER?> h1 {
                margin-top: 0;
            }

            .<?=static::HTML_CLASS_PROFILER_SECTION?> {
                border: 1px solid #ccc;
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .<?=static::HTML_CLASS_PROFILER_SECTION?> *:last-child {
                margin-bottom: 0;
            }

            .<?=static::HTML_CLASS_PROFILER_SECTION?> h2,
            .<?=static::HTML_CLASS_PROFILER_SECTION?> h3 {
                margin: 0 0 1rem 0;
                padding: 0;
            }

            .<?=static::HTML_CLASS_PROFILER_TABLE_PROPERTY?>,
            .<?=static::HTML_CLASS_PROFILER_TABLE_DATA?> {
                width: 100%;
                margin-bottom: 1rem;
            }

            .<?=static::HTML_CLASS_PROFILER_TABLE_PROPERTY?> th,
            .<?=static::HTML_CLASS_PROFILER_TABLE_DATA?> th {
                background: #000;
                color: #fff;
                padding: 0.5rem;
            }

            .<?=static::HTML_CLASS_PROFILER_TABLE_PROPERTY?> td,
            .<?=static::HTML_CLASS_PROFILER_TABLE_DATA?> td {
                border: 1px solid #ccc;
                padding: 0.5rem;
            }

            .<?=static::HTML_CLASS_PROFILER_TABLE_DATA_ROW?>:hover td,
            .<?=static::HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW?>:hover td {
                background: #CCCCCC;
            }
        <?php
        echo styleClose();
    }

    // --------------------------------------------------------------------------

    /**
     * Renders a single property table row
     *
     * @param string $mKey   The property's key
     * @param string $mValue The property's value
     */
    protected function renderPropertyRow($mKey, $mValue)
    {
        ?>
        <tr class="<?=static::HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW?>">
            <td class="<?=static::HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW_KEY?>">
                <?=$mKey?>
            </td>
            <td class="<?=static::HTML_CLASS_PROFILER_TABLE_PROPERTY_ROW_VALUE?>">
                <?=$mValue?>
            </td>
        </tr>
        <?php
    }

    // --------------------------------------------------------------------------

    /**
     * Renders a single data table row
     *
     * @param string[] $aRow
     */
    protected function renderDataRow(array $aRow)
    {
        ?>
        <tr class="<?=static::HTML_CLASS_PROFILER_TABLE_DATA_ROW?>">
            <?php
            foreach ($aRow as $sCell) {
                ?>
                <td class="<?=static::HTML_CLASS_PROFILER_TABLE_DATA_ROW_VALUE?>">
                    <?=$sCell?>
                </td>
                <?php
            }
            ?>
        </tr>
        <?php
    }

    // --------------------------------------------------------------------------

    /**
     * Summarises the marks
     *
     * @return array
     */
    protected function summariseMarks(): array
    {
        $aReportedMarks     = [];
        $fPreviousTimestamp = null;
        $fTotal             = 0;

        foreach (static::$aMarks as $aMark) {

            $iDiff            = $fPreviousTimestamp === null ? 0 : $aMark['Timestamp'] - $fPreviousTimestamp;
            $aReportedMarks[] = [
                'Label'     => $aMark['Label'],
                'Diff (s)'  => sprintf('%f', $iDiff),
                'Total (s)' => sprintf('%f', $fTotal + $iDiff),
            ];

            $fPreviousTimestamp = $aMark['Timestamp'];
            $fTotal             += $iDiff;
        }

        return [
            'Count'     => count($aReportedMarks),
            'Total (s)' => $fTotal,
            'Data'      => $aReportedMarks,
        ];

    }

    // --------------------------------------------------------------------------

    /**
     * Summarises the database
     *
     * @return array
     * @throws FactoryException
     */
    protected function summariseQueries(): array
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        $aReportedQueries = [];
        $iNumQueries      = count($oDb->queries);

        for ($i = 0; $i < $iNumQueries; $i++) {
            $aReportedQueries[] = [
                'Query'    => $oDb->queries[$i],
                'Time (s)' => $oDb->query_times[$i],
            ];
        }

        return [
            'Count'     => $iNumQueries,
            'Total (s)' => array_sum($oDb->query_times),
            'Data'      => $aReportedQueries,
        ];
    }
}
