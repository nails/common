<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Helper\Strings;
use Nails\Common\Model\Base;
use Nails\Common\Service\Database;
use Nails\Factory;

/**
 * Trait Token
 *
 * @package Nails\Common\Traits\Model
 */
trait Token
{
    /**
     * Whether to automatically set tokens
     *
     * @var bool|null
     * @deprecated Use constant AUTO_SET_TOKEN instead
     */
    protected $tableAutoSetTokens;

    /**
     * Override the default token mask when automatically generating tokens for items
     *
     * @var string
     * @deprecated Use constant TOKEN_MASK instead
     */
    protected $sTokenMask;

    // --------------------------------------------------------------------------

    /**
     * Returns the column name for specific columns of interest
     *
     * @param string      $sColumn  The column to query
     * @param string|null $sDefault The default value if not defined
     *
     * @return string|null
     */
    abstract public function getColumn(string $sColumn, string $sDefault = null): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $table
     *
     * @param bool $bIncludeAlias Whether to include the table's alias
     *
     * @throws ModelException
     * @return string
     */
    abstract public function getTableName(bool $bIncludeAlias = false): string;

    // --------------------------------------------------------------------------

    /**
     * Returns item(s) by a column and its value
     *
     * @param string $sColumn      The column to search on
     * @param mixed  $mValue       The value(s) to look for
     * @param array  $aData        Any additional data to pass in
     * @param bool   $bReturnsMany Whether the method expects to return a single item, or many
     *
     * @return Resource[]|Resource|null
     * @throws ModelException
     */
    abstract protected function getByColumn($sColumn, $mValue, array $aData, $bReturnsMany = false);

    // --------------------------------------------------------------------------

    /**
     * Sorts items into a specific order based on a specific column
     *
     * @param array  $aItems      The items to sort
     * @param array  $aInputOrder The order to sort them in
     * @param string $sColumn     The column to sort on
     *
     * @return array
     */
    abstract protected function sortItemsByColumn(array $aItems, array $aInputOrder, $sColumn): array;

    // --------------------------------------------------------------------------

    /**
     * Returns an array of extracted column for a query
     *
     * @param string $sColumn         The column to extract
     * @param array  $aData           The query data
     * @param bool   $bIncludeDeleted Whether to include deleted items
     *
     * @return mixed[]
     * @throws FactoryException
     * @throws ModelException
     */
    abstract public function getAllColumn(string $sColumn, array $aData, bool $bIncludeDeleted = false): array;

    // --------------------------------------------------------------------------

    /**
     * Returns whether this model automatically generates tokens or not
     *
     * @return bool
     */
    public function isAutoSetTokens(): bool
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->tableAutoSetTokens
        return $this->tableAutoSetTokens ?? (defined('static::AUTO_SET_TOKEN') ? static::AUTO_SET_TOKEN : false);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the token on the $aData array
     *
     * @param array $aData The data being passed to the model
     *
     * @return $this
     * @throws ModelException
     */
    protected function setDataToken(array &$aData, $bIsCreate = true): Base
    {
        if ($bIsCreate && $this->isAutoSetTokens() && empty($aData[$this->getColumnToken()])) {
            if (empty($this->getColumnToken())) {
                throw new ModelException(static::class . '::create() Token column variable not set', 1);
            }
            $aData[$this->getColumnToken()] = $this->generateToken();

        } elseif (!$bIsCreate) {
            //  Automatically set tokens are permanent and immutable
            if ($this->isAutoSetTokens() && !empty($aData[$this->getColumnToken()])) {
                unset($aData[$this->getColumnToken()]);
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for tokens
     *
     * @return string
     */
    public function getColumnToken(): string
    {
        return $this->getColumn('token', 'token');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the token mask to use
     *
     * @return string|null
     */
    protected function getTokenMask(): ?string
    {
        //  @todo (Pablo - 2019-04-15) - Phase out support for $this->sTokenMask
        return $this->sTokenMask ?? (defined('static::TOKEN_MASK') ? static::TOKEN_MASK : null);
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a unique token for a record
     *
     * @param string|null $sMask   The token mask, defaults to $this->getTokenMask()
     * @param string|null $sTable  The table to use defaults to $this->getTableName()
     * @param string|null $sColumn The column to use, defaults to $this->getColumnToken()
     *
     * @return string
     * @throws FactoryException
     * @throws ModelException
     */
    protected function generateToken(string $sMask = null, string $sTable = null, string $sColumn = null): string
    {
        $sMask   = $sMask ?? $this->getTokenMask();
        $sTable  = $sTable ?? $this->getTableName();
        $sColumn = $sColumn ?? $this->getColumnToken();

        if (!$sColumn) {
            throw new ModelException(
                sprintf(
                    '%s::generateToken() Token variable not set',
                    static::class
                )
            );
        }

        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        do {

            $sToken = Strings::generateToken($sMask);
            $oDb->where($sColumn, $sToken);

        } while ($oDb->count_all_results($sTable));

        return $sToken;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch an object by its token
     *
     * @param string $sToken The token of the object to fetch
     * @param array  $aData  Any data to pass to getCountCommon()
     *
     * @return Resource|null
     * @throws ModelException
     */
    public function getByToken($sToken, array $aData = [])
    {
        return $this->getByColumn($this->getColumnToken(), $sToken, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch objects by an array of tokens
     *
     * @param array $aTokens             An array of tokens to fetch
     * @param array $aData               Any data to pass to getCountCommon()
     * @param bool  $bMaintainInputOrder Whether to maintain the input order
     *
     * @return Resource[]
     * @throws ModelException
     */
    public function getByTokens($aTokens, array $aData = [], $bMaintainInputOrder = false)
    {
        $sColumn = $this->getColumnToken();
        $aItems  = $this->getByColumn($sColumn, $aTokens, $aData, true);
        return $bMaintainInputOrder
            ? $this->sortItemsByColumn($aItems, $aTokens, $sColumn)
            : $aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the tokens of the objects returned by the query
     *
     * @param array $aData           The query data
     * @param bool  $bIncludeDeleted Whether to include deleted items
     *
     * @return string[]
     * @throws FactoryException
     * @throws ModelException
     */
    public function getTokens(array $aData = [], bool $bIncludeDeleted = false): array
    {
        return $this->getAllColumn($this->getColumnToken(), $aData, $bIncludeDeleted);
    }
}
