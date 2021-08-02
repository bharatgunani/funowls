<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Plugin;

class GenderManager
{

    /**
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Onestepcheckout\Iosc\Helper\Data $helper
    ) {

        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     *  Soft set gender value to customer_gender
     * @param unknown $parent
     * @param unknown $value
     */
    public function afterSetGender(
        $parent,
        $value
    ) {

        if (! $this->helper->isEnabled()) {
            return;
        }

        $isSet = $this->checkoutSession->getQuote()->getCustomer()->getGender();
        if (!$isSet) {
            $value = ($parent->getGender()) ? $parent->getGender() : '' ;
            $this->checkoutSession->getQuote()->setCustomerGender($value);
        }
    }
}
