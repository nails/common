<?php

namespace Nails\Common\Console\Command\Install;

use Nails\Common\Factory\Component;
use Nails\Common\Interfaces;
use Nails\Common\Service\AppSetting;
use Nails\Console\Command\Base;
use Nails\Factory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Settings
 *
 * @package Nails\Common\Console\Command\Install
 */
class Settings extends Base
{
    /**
     * Configures the command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('install:settings');
        $this->setDescription('Installs default settings for components');
    }

    // --------------------------------------------------------------------------

    /**
     * Executes the command
     *
     * @param InputInterface  $oInput  The Input Interface provided by Symfony
     * @param OutputInterface $oOutput The Output Interface provided by Symfony
     *
     * @return int
     */
    protected function execute(InputInterface $oInput, OutputInterface $oOutput)
    {
        parent::execute($oInput, $oOutput);

        $this->banner('Install: Settings');

        /** @var AppSetting $oAppSetting */
        $oAppSetting = Factory::service('AppSetting');
        $oAppSetting->load();

        foreach (\Nails\Components::available() as $oComponent) {
            $this
                ->setDefaultSettings($oComponent);
        }

        $oAppSetting->load();

        return static::EXIT_CODE_SUCCESS;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets default values for app settings
     *
     * @param Component $oComponent
     *
     * @return $this
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function setDefaultSettings(Component $oComponent): self
    {
        $aClasses = $oComponent
            ->findClasses('Settings')
            ->whichImplement(Interfaces\Component\Settings::class);

        foreach ($aClasses as $sClass) {

            /** @var Interfaces\Component\Settings $oClass */
            $oClass = new $sClass();

            $this->oOutput->writeln(sprintf(
                'Setting default setting values for: <comment>%s</comment> (<comment>%s</comment>)',
                $oComponent->slug,
                $oClass->getLabel()
            ));

            foreach ($oClass->get() as $oSetting) {

                $sKey     = $this->normaliseKey($oSetting->getKey());
                $mDefault = $oSetting->getDefault();
                $mValue   = appSetting($sKey, $oComponent->slug);

                if ($mValue === null && $mDefault !== null) {
                    setAppSetting(
                        $sKey,
                        $oComponent->slug,
                        $mDefault,
                        $oSetting->isEncrypted()
                    );
                }
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Trailing square brackets are a quirk of the form validation system and should be removed for lookup
     *
     * @param string $sKey
     *
     * @return string
     */
    protected function normaliseKey(string $sKey): string
    {
        return preg_replace('/\[\]$/', '', $sKey);
    }
}
