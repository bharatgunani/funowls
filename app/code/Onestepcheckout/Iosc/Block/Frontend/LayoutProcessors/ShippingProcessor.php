<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Block\Frontend\LayoutProcessors;

class ShippingProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $converter
     * @param \Magento\Quote\Api\ShipmentEstimationInterface $shippingMethodManagement
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\Cart\ShippingMethodConverter $converter,
        \Magento\Quote\Api\ShipmentEstimationInterface $shippingMethodManagement,
        \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Onestepcheckout\Iosc\Helper\Data $helper
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->converter = $converter;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->totalsCollector = $totalsCollector;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {

        if ($this->helper->isEnabled()) {
            $shippingMethods = $this->getShippingRates(
                $this->checkoutSession->getQuote(),
                $this->converter,
                $this->serviceOutputProcessor
            );
            $jsLayout['components']['checkout']['children']
                ['iosc']['children']['shipping']['cnf']['availableRates'] = $shippingMethods;
        }

        return $jsLayout;
    }

    /**
     * Get available shipping rates
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $converter
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor
     * @return array
     */
    public function getShippingRates(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Model\Cart\ShippingMethodConverter $converter,
        \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor
    ) {
        $rates = [];

        $shippingAddress = $quote->getShippingAddress();

        $shippingAddress->setCollectShippingRates(true);
        $this->totalsCollector->collectAddressTotals($quote, $shippingAddress);

        $shippingRates = $shippingAddress->getGroupedAllShippingRates();

        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $rates[] = $converter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
            }
        }
        $rates = $serviceOutputProcessor->convertValue($rates, '\Magento\Quote\Api\Data\ShippingMethodInterface[]');

        return $rates;
    }
}
