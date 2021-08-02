<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Observer\Frontend;

class LayoutHandler implements \Magento\Framework\Event\ObserverInterface
{

    public $scopeConfig;
    public $helper;

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
     * Add handles to the page for checkout page .
     *
     *
     * @param Observer $observer
     * @event layout_load_before
     *
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        if (!$this->helper->isEnabled()) {
            return;
        }

        $action = $observer->getFullActionName();

        if ($action === 'checkout_index_index') {
            $observer->getLayout()
                ->getUpdate()
                ->addHandle('checkout_onestepcheckout_iosc');
        }
    }
}
