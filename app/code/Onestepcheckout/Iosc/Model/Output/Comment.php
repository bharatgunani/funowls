<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Model\Output;

class Comment implements OutputManagementInterface
{

    public function getOutputKey()
    {
        return 'customerNote';
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

        $response = false;

        if ($quote->getId()) {
            $input = (isset($input[$this->getOutputKey()])) ? $input[$this->getOutputKey()] : false;

            if ($input) {
                $input = $this->escaper->escapeHtml(current($input));
                $quote->setCustomerNote($input);
                $quote->setCustomerNoteNotify(0);
                $response = true;
            } else {
                $quote->setCustomerNote(null);
                $quote->setCustomerNoteNotify(1);
            }

            $quote->save();
        }
        $data = $response;
        return $data;
    }
}
