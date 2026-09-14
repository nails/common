<?php

namespace Nails\Common\Traits\Database;

use Nails\Common\Exception\FactoryException;
use Nails\Factory;

/**
 * Trait Migration
 *
 * @package Nails\Common\Traits\Database
 */
trait Migration
{
    /**
     * The database connection
     *
     * @var \Nails\Common\Service\PDODatabase
     */
    protected $oDb;

    // --------------------------------------------------------------------------

    /**
     * A counter which is incremented for each query to make it easier to trace wen
     *
     * @var int
     */
    protected $iQueryCount = 0;

    // --------------------------------------------------------------------------

    /**
     * The last query which was called via query() or prepare()
     *
     * @var string
     */
    protected $sLastQuery = '';

    // --------------------------------------------------------------------------

    /**
     * Base constructor.
     *
     * @param \Nails\Common\Service\PDODatabase $oDb
     */
    public function __construct(\Nails\Common\Service\PDODatabase $oDb)
    {
        $this->oDb = $oDb;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the priority of the migration
     *
     * @return int
     */
    public function getPriority(): int
    {
        return (int) preg_replace('/^.*?(\d+$)/', '$1', static::class);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns how many queries have been attempted
     *
     * @return int
     */
    public function getQueryCount(): int
    {
        return $this->iQueryCount;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the last query
     *
     * @return string
     */
    public function getLastQuery(): string
    {
        return $this->sLastQuery;
    }

    // --------------------------------------------------------------------------

    /**
     * Execute a DB query
     *
     * @param string $sQuery The query to execute
     *
     * @return \PDOStatement
     */
    public function query(string $sQuery): \PDOStatement
    {
        $sQuery = $this->replaceConstants($sQuery);
        $this->iQueryCount++;
        $this->sLastQuery = $sQuery;

        return $this->oDb->query($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Prepare a DB query
     *
     * @param string $sQuery The query to prepare
     *
     * @return \PDOStatement
     */
    public function prepare(string $sQuery): \PDOStatement
    {
        $sQuery = $this->replaceConstants($sQuery);
        $this->iQueryCount++;
        $this->sLastQuery = $sQuery;

        return $this->oDb->prepare($sQuery);
    }

    // --------------------------------------------------------------------------

    /**
     * Replaces {{CONSTANT}} with the value of constant, CONSTANT
     *
     * @param string $sString The string to search on
     *
     * @return string
     */
    protected function replaceConstants(string $sString): string
    {
        return preg_replace_callback(
            '/{{(.+?)}}/',
            function ($aMatches) {
                if (defined($aMatches[1])) {
                    return constant($aMatches[1]);
                }

                return $aMatches[0];
            },
            $sString
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Whether a table exists
     *
     * @param string $sTable The table to look for
     *
     * @return bool
     */
    protected function tableExists(string $sTable): bool
    {
        return $this
                ->query('SHOW TABLES LIKE "' . $sTable . '"')
                ->rowCount() > 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a table's columns, keyed by column name
     *
     * Each element is the raw row returned by SHOW COLUMNS, i.e. Field, Type, Null,
     * Key, Default and Extra.
     *
     * @param string $sTable The table to inspect
     *
     * @return array[]
     */
    protected function getTableColumns(string $sTable): array
    {
        $aOut = [];
        foreach ($this->query('SHOW COLUMNS FROM `' . $sTable . '`')->fetchAll(\PDO::FETCH_ASSOC) as $aRow) {
            $aOut[$aRow['Field']] = $aRow;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether a column exists on a table
     *
     * @param string $sTable  The table to inspect
     * @param string $sColumn The column to look for
     *
     * @return bool
     */
    protected function columnExists(string $sTable, string $sColumn): bool
    {
        return array_key_exists($sColumn, $this->getTableColumns($sTable));
    }

    // --------------------------------------------------------------------------

    /**
     * Whether an index exists on a table
     *
     * @param string $sTable The table to inspect
     * @param string $sIndex The key name to look for
     *
     * @return bool
     */
    protected function indexExists(string $sTable, string $sIndex): bool
    {
        return $this
                ->query('SHOW INDEX FROM `' . $sTable . '` WHERE Key_name = "' . $sIndex . '"')
                ->rowCount() > 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a table's foreign keys, grouped by the constrained column name
     *
     * Each element is an array of rows containing CONSTRAINT_NAME, COLUMN_NAME,
     * REFERENCED_TABLE_NAME and REFERENCED_COLUMN_NAME.
     *
     * @param string $sTable The table to inspect
     *
     * @return array[]
     */
    protected function getTableForeignKeys(string $sTable): array
    {
        $oStatement = $this->prepare(
            <<<'EOT'
            SELECT
                `CONSTRAINT_NAME`,
                `COLUMN_NAME`,
                `REFERENCED_TABLE_NAME`,
                `REFERENCED_COLUMN_NAME`
            FROM `INFORMATION_SCHEMA`.`KEY_COLUMN_USAGE`
            WHERE
                `TABLE_SCHEMA` = DATABASE()
                AND `TABLE_NAME` = :table
                AND `REFERENCED_TABLE_NAME` IS NOT NULL
            EOT
        );

        $oStatement->execute([':table' => $this->replaceConstants($sTable)]);

        $aOut = [];
        foreach ($oStatement->fetchAll(\PDO::FETCH_ASSOC) as $aRow) {
            $aOut[$aRow['COLUMN_NAME']][] = $aRow;
        }

        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether a column is constrained by a foreign key, optionally to a specific target
     *
     * @param string      $sTable     The table to inspect
     * @param string      $sColumn    The constrained column
     * @param string|null $sRefTable  The table the key must reference, if it matters
     * @param string|null $sRefColumn The column the key must reference, if it matters
     *
     * @return bool
     */
    protected function foreignKeyExists(
        string $sTable,
        string $sColumn,
        ?string $sRefTable = null,
        ?string $sRefColumn = null
    ): bool {

        $aKeys = $this->getTableForeignKeys($sTable)[$sColumn] ?? [];

        if (empty($aKeys)) {
            return false;
        } elseif ($sRefTable === null && $sRefColumn === null) {
            return true;
        }

        foreach ($aKeys as $aKey) {
            $bTableMatch  = $sRefTable === null
                || strcasecmp($aKey['REFERENCED_TABLE_NAME'], $this->replaceConstants($sRefTable)) === 0;
            $bColumnMatch = $sRefColumn === null
                || strcasecmp($aKey['REFERENCED_COLUMN_NAME'], $sRefColumn) === 0;

            if ($bTableMatch && $bColumnMatch) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the ID created by the previous write query
     *
     * @return string
     */
    public function lastInsertId(): ?int
    {
        return (int) $this->oDb->lastInsertId() ?: null;
    }

    // --------------------------------------------------------------------------

    /**
     * Exposes the database API
     *
     * @return \PDO
     */
    public function db(): \PDO
    {
        return $this->oDb;
    }
}
