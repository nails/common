<?php

namespace Nails\Common\Traits\Model;

use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Factory\Model\Field;
use Nails\Common\Resource\Entity;
use Nails\Common\Service\Database;
use Nails\Factory;

/**
 * Trait Copyable
 *
 * @package Nails\Common\Traits\Model
 */
trait Copyable
{
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
     * Returns the column to use as the ID
     *
     * @return string
     */
    abstract public function getColumnId(): string;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use as the label
     *
     * @return string|null
     */
    abstract public function getColumnLabel(): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use as the is_deleted
     *
     * @return string|null
     */
    abstract public function getColumnIsDeleted(): ?string;

    // --------------------------------------------------------------------------

    /**
     * Returns protected property $table
     *
     * @param bool $bIncludePrefix Whether to include the table's alias
     *
     * @return string
     */
    abstract public function getTableName(bool $bIncludePrefix = false): string;

    // --------------------------------------------------------------------------

    /**
     * Describes the fields for this model automatically and with some guesswork;
     * for more fine grained control models should overload this method.
     *
     * @param string|null $sTable The database table to query
     *
     * @return Field[]
     */
    abstract public function describeFields($sTable = null);

    // --------------------------------------------------------------------------

    /**
     * Creates a new object
     *
     * @param array $aData         The data to create the object with
     * @param bool  $bReturnObject Whether to return just the new ID or the full object
     *
     * @return mixed
     * @throws ModelException
     */
    abstract public function create(array $aData = [], $bReturnObject = false);

    // --------------------------------------------------------------------------

    /**
     * Copies the item with the  specified ID
     *
     * @param int $iId The ID of the item to copy
     *
     * @return Entity|int
     */
    public function copy(int $iId, bool $bReturnObject = false, array $aReturnData = [])
    {
        /** @var Database $oDb */
        $oDb    = Factory::service('Database');
        $sQuery = sprintf(
            'SELECT `%s` FROM `%s` WHERE `%s` = %s;',
            implode('`,`', $this->getCopiedColumns()),
            $this->getTableName(),
            $this->getColumnId(),
            $iId
        );

        $aResult = (array) $oDb->query($sQuery)->row();

        if (empty($aResult)) {
            throw new NailsException('Invalid item ID.');
        }

        if (array_key_exists($this->getCopiedLabelColumn(), $aResult)) {

            /** @var \DateTime $oNow */
            $oNow = Factory::factory('DateTime');

            $aResult[$this->getCopiedLabelColumn()] .= sprintf(
                ' (Copy %s)',
                toUserDatetime($oNow->format('Y-m-d H:i:s'))
            );
        }

        //  @todo (Pablo - 2019-12-10) - Add support for classes which implement Localised trait

        $iItemId = $this->create($aResult);
        if (empty($iItemId)) {
            throw new NailsException('Failed to copy item.' . $this->lastError());
        }

        //  Return the new item's ID or object
        return $bReturnObject
            ? $this->getById($iItemId, $aReturnData)
            : $iItemId;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of the columns to copy
     *
     * @return string[]
     */
    protected function getCopiedColumns(): array
    {
        $aColumns = array_keys($this->describeFields());
        $aColumns = array_filter($aColumns, function ($sColumn) {
            return !in_array($sColumn, array_filter([

                //  Base model
                $this->getColumnId(),
                $this->getColumnIsDeleted(),

                //  Trait\Model\User
                method_exists($this, 'getColumnCreatedBy') ? $this->getColumnCreatedBy() : null,
                method_exists($this, 'getColumnModifiedBy') ? $this->getColumnModifiedBy() : null,

                //  Trait\Model\Slug
                method_exists($this, 'getColumnSlug') ? $this->getColumnSlug() : null,

                //  Trait\Model\Token
                method_exists($this, 'getColumnToken') ? $this->getColumnToken() : null,

                //  Trait\Model\Timestamps
                method_exists($this, 'getColumnCreated') ? $this->getColumnCreated() : null,
                method_exists($this, 'getColumnModified') ? $this->getColumnModified() : null,
            ]));
        });

        return $aColumns;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the column to use for the label when copying
     *
     * @return string
     */
    protected function getCopiedLabelColumn(): string
    {
        return $this->getColumnLabel();
    }
}
