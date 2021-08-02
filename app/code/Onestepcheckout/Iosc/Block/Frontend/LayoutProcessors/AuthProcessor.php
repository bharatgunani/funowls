<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Block\Frontend\LayoutProcessors;

class AuthProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Onestepcheckout\Iosc\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {

        if ($this->helper->isEnabled()) {
            $enabled = $this->scopeConfig->getValue(
                'onestepcheckout_iosc/registration/showlogin',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if (!$enabled) {
                unset($jsLayout['components']['checkout']['children']['authentication']);
            }
            $enabled = $this->scopeConfig->getValue(
                'onestepcheckout_iosc/registration/optionalpwd',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            $isLoggedIn = $this->customerSession->getId();

            if (!$isLoggedIn && $enabled && isset($jsLayout['components']['checkout']['children']['sidebar']['children']['registration-fields'])) {
                $fields = $jsLayout['components']['checkout']['children']['sidebar']['children']['registration-fields'];
                $registration = $jsLayout['components']['checkout']['children']['iosc']['children']['registration'];
                $regFields = array_merge($fields, $registration);
                $jsLayout['components']['checkout']['children']['sidebar']['children']['registration-fields'] = $regFields;
                unset($jsLayout['components']['checkout']['children']['iosc']['children']['registration']);
            } elseif (isset($jsLayout['components']['checkout']['children']['sidebar']['children']['registration-fields'])) {
                unset($jsLayout['components']['checkout']['children']['sidebar']['children']['registration-fields']);
            }
        }

        return $jsLayout;
    }
}
