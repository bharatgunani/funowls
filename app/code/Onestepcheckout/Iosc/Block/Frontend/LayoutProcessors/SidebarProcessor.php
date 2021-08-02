<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Block\Frontend\LayoutProcessors;

class SidebarProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
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

        if ($this->helper->isEnabled() && isset($jsLayout['components']['checkout']['children']['sidebar']['children'])) {

            $configKey = 'iosc-summary';
            $lastComponent = end($jsLayout['components']['checkout']['children']['sidebar']['children'])['component'];
            $componentCount = count($jsLayout['components']['checkout']['children']['sidebar']['children']);
            $component = [
                'component' => 'Onestepcheckout_Iosc/js/totals',
                'displayArea' => 'sidebar',
                'config' => [
                    'template' => 'Onestepcheckout_Iosc/summary',
                    'iosc_cnf' => [
                        'lastComponent' => $lastComponent,
                        'componentCount' => $componentCount
                    ]
                ]
            ];
            $sidebar = $jsLayout['components']['checkout']['children']['sidebar'];
            unset($jsLayout['components']['checkout']['children']['sidebar']);
            $jsLayout['components']['checkout']['children'][$configKey] = $component;
            $jsLayout['components']['checkout']['children']['sidebar'] = $sidebar;
        }

        return $jsLayout;
    }
}
