<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Observer\Frontend;

class Setlogindefaults implements \Magento\Framework\Event\ObserverInterface
{

    public $scopeConfig = null;

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Psr\Log\LoggerInterface $logger,
        \Onestepcheckout\Iosc\Helper\Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->objectCopyService = $objectCopyService;
        $this->checkoutSession = $checkoutSession;
        $this->log = $logger;
        $this->helper = $helper;
    }

    /**
     * Add default data to address objects
     *
     * @param Observer $observer
     * @event customer_login
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();

        if (!$this->helper->isEnabled() || ! is_object($quote)) {
            return;
        }

        if (!empty($quote->getCustomer()) && !empty($quote->getCustomer()->getAddresses())) {
            $saveQuote = false;
            $customer = $observer->getEvent()->getCustomer();
            $primaryBilling = $customer->getPrimaryBillingAddress();

            if (!$quote->getBillingAddress()->getCustomerAddressId() &&
                (is_object($primaryBilling) && $primaryBilling->getId())
            ) {
                $newBilling = $this->copyAddressData(
                    $customer->getPrimaryBillingAddress(),
                    $quote->getBillingAddress()
                );
                $quote->setBillingAddress($newBilling);
                $saveQuote = true;
            }

            $primaryShipping = $customer->getPrimaryShippingAddress();
            if (!$quote->getShippingAddress()->getCustomerAddressId() &&
                (is_object($primaryShipping) && $primaryShipping->getId())
            ) {
                $newShipping = $this->copyAddressData(
                    $customer->getPrimaryShippingAddress(),
                    $quote->getShippingAddress()
                );
                $quote->setShippingAddress($newShipping);
                $saveQuote = true;
            }

            if ($saveQuote) {
                $quote->save();
            }
        }
    }

    /**
     *
     * @param array $source
     * @param $target
     * @return $target
     */
    private function copyAddressData($source, $target)
    {
        $this->objectCopyService->copyFieldsetToTarget(
            'customer_address',
            'to_quote_address',
            $source,
            $target
        );

        return $target;
    }
}
