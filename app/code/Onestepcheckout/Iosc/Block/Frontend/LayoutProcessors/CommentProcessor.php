<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Block\Frontend\LayoutProcessors;

class CommentProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
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
        $configKey = 'comments';

        if ($this->helper->isEnabled() && isset($jsLayout['components']['checkout']['children']['iosc']['children'][$configKey])) {
            $customConfig = $jsLayout['components']['checkout']['children']['iosc']['children'][$configKey];
            unset($jsLayout['components']['checkout']['children']['iosc']['children'][$configKey]);

            $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            if ($this->scopeConfig->getValue('onestepcheckout_iosc/' . $configKey . '/enable', $scopeStore)) {
                if (isset($jsLayout['components']['checkout']['children']['sidebar']['children'][$configKey])) {
                    $componentConfig = $jsLayout['components']['checkout']['children']['sidebar']['children'][$configKey];
                    $componentConfig = array_merge($componentConfig, $customConfig);
                    $jsLayout['components']['checkout']['children']['sidebar']['children'][$configKey] = $componentConfig;
                }
            } else {
                if (isset($jsLayout['components']['checkout']['children']['sidebar']['children'][$configKey])) {
                    unset($jsLayout['components']['checkout']['children']['sidebar']['children'][$configKey]);
                }
            }
        }

        return $jsLayout;
    }
}
