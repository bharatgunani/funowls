<?php
namespace RedChamps\GuestOrders\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigReader
{
    const XML_CONFIG_BASE_PATH = "guest_orders/";

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfig($path, $store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_CONFIG_BASE_PATH.$path,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function canProcessImmediately()
    {
        return $this->getConfig('general/processing_method') == "checkout";
    }
}
