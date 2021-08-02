<?php
namespace RedChamps\GuestOrders\Plugin;

use Magento\Checkout\Block\Registration;
use RedChamps\GuestOrders\Model\ConfigReader;

class RegistrationBlock
{
    /**
     * @var ConfigReader
     */
    private $configReader;

    public function __construct(ConfigReader $configReader)
    {
        $this->configReader = $configReader;
    }

    public function aroundToHtml(Registration $subject, callable $proceed)
    {
        if (!$this->configReader->getConfig("auto_customer_accounts/enabled")) {
            $proceed();
        }
        return "";
    }
}
