<?php

namespace Nails\Common\Settings;

use Nails\Admin\Traits;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Helper\Form;
use Nails\Common\Interfaces;
use Nails\Common\Service\Input;
use Nails\Components;
use Nails\Components\Setting;
use Nails\Factory;

/**
 * Class Site
 *
 * @package Nails\Common\Settings
 */
class Site implements Interfaces\Component\Settings
{
    use Traits\Settings\Permission;

    // --------------------------------------------------------------------------

    const KEY_CUSTOM_JS             = 'site_custom_js';
    const KEY_CUSTOM_CSS            = 'site_custom_css';
    const KEY_CUSTOM_MARKUP         = 'site_custom_markup';
    const KEY_MAINTENANCE_ENABLED   = 'maintenance_mode_enabled';
    const KEY_MAINTENANCE_WHITELIST = 'maintenance_mode_whitelist';
    const KEY_MAINTENANCE_TITLE     = 'maintenance_mode_title';
    const KEY_MAINTENANCE_BODY      = 'maintenance_mode_body';

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return 'Site';
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getPermissions(): array
    {
        return [
            'maintenance' => 'Maintenance Mode',
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        return array_merge(
            $this->getSettingsCustomJsCss(),
            $this->getSettingsMaintenance(),
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Get Custom JS & CSS settings
     *
     * @return Setting[]
     * @throws FactoryException
     */
    protected function getSettingsCustomJsCss(): array
    {
        if (!$this->userHasPermission()) {
            return [];
        }

        /** @var Setting $oCustomJs */
        $oCustomJs = Factory::factory('ComponentSetting');
        $oCustomJs
            ->setKey(static::KEY_CUSTOM_JS)
            ->setType(Form::FIELD_TEXTAREA)
            ->setLabel('Javascript')
            ->setFieldset('Custom JS & CSS')
            ->setPlaceholder('Specify any custom JS to include at the foot of the page')
            ->setInfo('You should <strong>not</strong> wrap this in <code>&lt;script&gt;</code> tags');

        /** @var Setting $oCustomCss */
        $oCustomCss = Factory::factory('ComponentSetting');
        $oCustomCss
            ->setKey(static::KEY_CUSTOM_CSS)
            ->setType(Form::FIELD_TEXTAREA)
            ->setLabel('CSS')
            ->setFieldset('Custom JS & CSS')
            ->setPlaceholder('Specify any custom CSS to include at the head of the page')
            ->setInfo('You should <strong>not</strong> wrap this in <code>&lt;style&gt;</code> tags');

        /** @var Setting $oCustomMarkup */
        $oCustomMarkup = Factory::factory('ComponentSetting');
        $oCustomMarkup
            ->setKey(static::KEY_CUSTOM_MARKUP)
            ->setType(Form::FIELD_TEXTAREA)
            ->setLabel('Markup')
            ->setFieldset('Custom JS & CSS')
            ->setPlaceholder('Specify any custom markup to include at the foot of the page');

        return [
            $oCustomJs,
            $oCustomCss,
            $oCustomMarkup,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Get Maintenance Mode Settings
     *
     * @return Setting[]
     * @throws FactoryException
     */
    protected function getSettingsMaintenance(): array
    {
        if (!$this->userHasPermission('maintenance')) {
            return [];
        }

        /** @var Input $oInput */
        $oInput = \Nails\Factory::service('Input');

        /** @var Setting $oMaintenanceEnabled */
        $oMaintenanceEnabled = Factory::factory('ComponentSetting');
        $oMaintenanceEnabled
            ->setKey(static::KEY_MAINTENANCE_ENABLED)
            ->setType(Form::FIELD_BOOLEAN)
            ->setLabel('Enabled')
            ->setFieldset('Maintenance Mode')
            ->setInfo('<strong>Note:</strong> Maintenance mode can be enabled via this setting, or by placing a file entitled <code>.MAINTENANCE</code> at the site\'s root. If the <code>.MAINTENANCE</code> file is found then the site will forcibly be placed into maintenance mode, regardless of this setting.')
            ->setInfoClass('alert alert-warning')
            ->setData([
                'revealer' => 'maintenance-mode',
            ]);

        /** @var Setting $oMaintenanceWhitelist */
        $oMaintenanceWhitelist = Factory::factory('ComponentSetting');
        $oMaintenanceWhitelist
            ->setKey(static::KEY_MAINTENANCE_WHITELIST)
            ->setType(Form::FIELD_TEXTAREA)
            ->setLabel('Allowed IPs')
            ->setFieldset('Maintenance Mode')
            ->setPlaceholder('Specify IP addresses to whitelist either comma seperated or on new lines.')
            ->setInfo('Your current IP address is: <code>' . $oInput->ipAddress() . '</code>')
            ->setRenderFormatter(function ($mValue) {
                return is_array($mValue) ? implode(PHP_EOL, $mValue) : '';
            })
            ->setSaveFormatter(function ($mValue) {
                return $this->prepareWhitelist($mValue);
            })
            ->setData([
                'revealer'  => 'maintenance-mode',
                'reveal-on' => true,
            ]);

        /** @var Setting $oMaintenanceTitle */
        $oMaintenanceTitle = Factory::factory('ComponentSetting');
        $oMaintenanceTitle
            ->setKey(static::KEY_MAINTENANCE_TITLE)
            ->setLabel('Title')
            ->setFieldset('Maintenance Mode')
            ->setPlaceholder('Optionally set a custom title for the maintenance page')
            ->setData([
                'revealer'  => 'maintenance-mode',
                'reveal-on' => true,
            ]);

        /** @var Setting $oMaintenanceBody */

        $oMaintenanceBody = Factory::factory('ComponentSetting');
        $oMaintenanceBody
            ->setKey(static::KEY_MAINTENANCE_TITLE)
            ->setType('wysiwyg')
            ->setLabel('Body')
            ->setFieldset('Maintenance Mode')
            ->setPlaceholder('Optionally set a custom body for the maintenance page')
            ->setData([
                'revealer'  => 'maintenance-mode',
                'reveal-on' => true,
            ]);

        return [
            $oMaintenanceEnabled,
            $oMaintenanceWhitelist,
            $oMaintenanceTitle,
            $oMaintenanceBody,
        ];
    }
}
