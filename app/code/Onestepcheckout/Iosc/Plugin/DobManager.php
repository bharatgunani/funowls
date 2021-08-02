<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Plugin;

class DobManager
{

    /**
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Onestepcheckout\Iosc\Helper\Data $helper
    ) {

        $this->helper = $helper;
        $this->dateFilter = $dateFilter;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     *  Soft set dob value to customer_dob
     * @param unknown $parent
     * @param unknown $value
     */
    public function afterSetDob(
        $parent,
        $value
    ) {

        if (! $this->helper->isEnabled()) {
            return;
        }

        $isSet = $this->checkoutSession->getQuote()->getCustomer()->getDob();
        if (!$isSet) {
            $value = ($parent->getDob()) ? $this->dateFilter->filter($parent->getDob()) : '' ;
            $this->checkoutSession->getQuote()->setCustomerDob($value);
        }
    }
}
