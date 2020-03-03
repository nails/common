<?php

namespace Nails\Common\Resource;

use Nails\Common\Exception\Encrypt\DecodeException;
use Nails\Common\Exception\EnvironmentException;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Common\Service\Encrypt;
use Nails\Factory;

/**
 * Class AppSetting
 *
 * @package Nails\Common\Resource
 */
class AppSetting extends Resource
{
    /** @var int */
    public $id;

    /** @var string */
    public $grouping;

    /** @var string */
    public $key;

    /** @var string */
    public $value;

    /** @var bool */
    public $is_encrypted;

    /** @var bool */
    protected $is_decrypted;

    /** @var string */
    protected $value_decrypted;

    // --------------------------------------------------------------------------

    /**
     * Determines whether the setting is encrypted
     *
     * @return bool
     */
    public function isEncrypted(): bool
    {
        return $this->is_encrypted;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the setting's value, decoding it if necessary
     *
     * @return string
     * @throws DecodeException
     * @throws EnvironmentException
     * @throws FactoryException
     */
    public function getValue()
    {
        if ($this->isEncrypted()) {
            if (!$this->is_decrypted) {

                /** @var Encrypt $oEncrypt */
                $oEncrypt = Factory::service('Encrypt');

                $this->value_decrypted = $oEncrypt->decode($this->value);
            }
            return json_decode($this->value_decrypted);
        } else {
            return json_decode($this->value);
        }
    }
}
