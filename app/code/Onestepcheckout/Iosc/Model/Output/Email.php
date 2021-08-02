<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Model\Output;

class Email implements OutputManagementInterface
{

    public function getOutputKey()
    {
        return 'customerEmail';
    }

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Onestepcheckout\Iosc\Helper\Data $helper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->escaper = $escaper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritDoc}
     * @see \Onestepcheckout\Iosc\Model\Input\InputManagement::processPayload()
     */
    public function processPayload($input)
    {

        $data = [];

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();

        if ($quote->getId()) {
            $input = (isset($input[$this->getOutputKey()])) ? $input[$this->getOutputKey()] : false;

            if ($input && \Zend_Validate::is($input, 'EmailAddress')) {
                $input = $this->escaper->escapeHtml($input);
                $quote->setCustomerEmail($input);
                $quote->getBillingAddress()->setEmail($input);
                $quote->getShippingAddress()->setEmail($input);
                $quote->save();
            }
        }
        return $data;
    }
}
