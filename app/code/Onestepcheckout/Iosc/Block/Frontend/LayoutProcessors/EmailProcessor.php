<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Block\Frontend\LayoutProcessors;

class EmailProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Onestepcheckout\Iosc\Helper\Data $helper
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {

        if ($this->helper->isEnabled()) {
            $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            if ($this->scopeConfig->getValue('onestepcheckout_iosc/shippingfields/separateemail', $scopeStore)) {
                $var = $jsLayout['components']['checkout']['children']
                    ['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['customer-email'];

                unset(
                    $jsLayout['components']['checkout']['children']
                    ['steps']['children']['shipping-step']['children']
                    ['shippingAddress']['children']['customer-email']
                );
                $jsLayout['components']['checkout']['children']['steps']
                    ['children']['shipping-step']['children']['shippingAddress']
                    ['children']['shipping-address-fieldset']['children']['email'] = $var;
            }
        }

        return $jsLayout;
    }
}
