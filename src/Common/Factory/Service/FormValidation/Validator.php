<?php

namespace Nails\Common\Factory\Service\FormValidation;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Service\FormValidation;
use Nails\Factory;

/**
 * Class Validator
 *
 * @package Nails\Common\Factory\FormValidation
 */
class Validator
{
    /**
     * The rules array, key => value format, where value is an array or pipe separated strings
     *
     * @var array
     */
    protected $aRules = [];

    /**
     * Messages to override the default error messages
     *
     * @var string[]
     */
    protected $aMessages = [];

    /**
     * The data to validate
     *
     * @var array
     */
    protected $aData = [];

    // --------------------------------------------------------------------------

    /**
     * Validator constructor.
     *
     * @param array    $aRules    The rules array, key => value format, where value is an array or pipe separated strings
     * @param string[] $aMessages Messages to override the default error messages
     * @param array    $aData     The data to validate
     */
    public function __construct(array $aRules = [], array $aMessages = [], array $aData = [])
    {
        $this
            ->setRules($aRules)
            ->setMessages($aMessages)
            ->setData($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the rules array
     *
     * @param array $aRules The rules array, key => value format, where value is an array or pipe separated strings
     *
     * @return $this
     */
    public function setRules(array $aRules): Validator
    {
        $this->aRules = $aRules;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the rules array
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->aRules;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the messages array
     *
     * @param string[] $aMessages Messages to override the default error messages
     *
     * @return $this
     */
    public function setMessages(array $aMessages): Validator
    {
        $this->aMessages = $aMessages;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the messages array
     *
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->aMessages;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the data array
     *
     * @param array $aData The data to validate
     *
     * @return $this
     */
    public function setData(array $aData): Validator
    {
        $this->aData = $aData;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the data array
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->aData;
    }

    // --------------------------------------------------------------------------

    /**
     * Perform the validation
     *
     * @throws ValidationException
     * @throws FactoryException
     */
    public function run(array $aData = null)
    {
        if ($aData !== null) {
            $this->setData($aData);
        }

        /** @var FormValidation $oFormValidation */
        $oFormValidation = Factory::service('FormValidation');

        $oFormValidation->reset_validation();
        $oFormValidation->set_data($this->getData());

        //  Set the rules
        $aAllRules = [];
        foreach ($this->getRules() as $sField => $aRules) {

            if (is_array($aRules)) {
                $aAllRules = array_merge($aAllRules, $aRules);
                $sRules    = implode('|', $aRules);
            } else {
                $aAllRules = array_merge($aAllRules, explode('|', $aRules));
                $sRules    = $aRules;
            }

            $oFormValidation->set_rules($sField, '', $sRules);
        }

        //  Set the messages
        $aAllRules = array_filter(
            array_unique(
                array_map(function ($sRule) {
                    return preg_replace('/^(.*)\[.*\]$/', '$1', trim($sRule));
                }, $aAllRules)
            )
        );

        $aMessages = $this->getMessages();
        foreach ($aAllRules as $sRule) {
            $oFormValidation->set_message(
                $sRule,
                getFromArray($sRule, $aMessages, lang('fv_' . $sRule))
            );
        }

        //  Execute the validation
        if (!$oFormValidation->run()) {
            $oException = new ValidationException(lang('fv_there_were_errors'));
            $oException->setData($this->getErrors());
            throw $oException;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Return any errors
     *
     * @return string[]
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function getErrors()
    {
        /** @var FormValidation $oFormValidation */
        $oFormValidation = Factory::service('FormValidation');
        return $oFormValidation->error_array();
    }
}
