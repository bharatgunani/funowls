<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Model\Output;

class Subscribe implements OutputManagementInterface
{

    public function getOutputKey()
    {
        return 'subscribe';
    }

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Onestepcheckout\Iosc\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
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

            if ($input && is_array($input)) {
                $input = current($input);
                $quote->setIoscSubscribe((int)$input);
                $response = (boolean)$input;
            } else {
                 $quote->setIoscSubscribe(0);
            }
            $quote->save();
        }

        $data = $response;
        return $data;
    }
}
