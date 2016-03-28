<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Atwix\AcceptanceHelper\Controller\Settings;

use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;

/**
 * Provides functionality for changing system settings
 * WARNING: Do not use on production environments!
 */
class Change extends \Magento\Framework\App\Action\Action
{
    const SUCCESS_MESSAGE = '<h4 style="color: green">All operations were completed successfully</h4>';
    const FAIL_MESSAGE = '<h4 style="color: red">There was error processing your request: %s</h4>';

    /**
     * @var \Magento\Framework\App\Config\ConfigResource\ConfigInterface
     */
    protected $configResource;


    public function __construct(
        Context $context,
        ConfigInterface $config
    ) {
        parent::__construct($context);
        $this->configResource = $config;
    }

    /**
     * Changes system configuration value.
     * Please note, the configuration path should be provided with backslashes instead of slashes
     * i.e. dev\debug\template_hints_storefront
     */
    public function execute()
    {
        $configPath = str_replace('\\', '/', $this->getRequest()->getParam('config'));
        $configValue = (string) $this->getRequest()->getParam('value');
        if ($this->validatePassedParams($configPath, $configValue)) {
            $this->configResource->saveConfig(
                $configPath,
                $configValue,
                \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                null
            );
        } else {
            $this->getResponse()->appendBody(sprintf(self::FAIL_MESSAGE, 'One or more required parameters are missing'));
            return $this->getResponse();
        }

        $this->getResponse()->appendBody(self::SUCCESS_MESSAGE);
    }

    /**
     * Validates if passed parameters are correct
     *
     * @param string $configPath
     * @param string $configValue
     * @return bool
     */
    protected function validatePassedParams($configPath, $configValue)
    {
        return (!empty($configPath) && $configValue) || (!empty($configPath) && $configValue === '0');
    }
}
