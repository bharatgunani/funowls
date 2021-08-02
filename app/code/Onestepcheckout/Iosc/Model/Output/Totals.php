<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Model\Output;

class Totals implements OutputManagementInterface
{

    /**
     *
     * {@inheritDoc}
     * @see \Onestepcheckout\Iosc\Model\Output\OutputManagement::getOutputKey()
     */
    public function getOutputKey()
    {
        return 'paymentMethod';
    }

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {

        $this->scopeConfig = $scopeConfig;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * {@inheritDoc}
     * @see \Onestepcheckout\Iosc\Model\Output\OutputManagement::processPayload()
     */
    public function processPayload($input)
    {
        $data = [];

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->checkoutSession->getQuote();

        if ($quote->getId()) {
            /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
            $paymentDetails = $this->paymentDetailsFactory->create();
            $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($quote->getId()));
            $paymentDetails->setTotals($this->cartTotalsRepository->get($quote->getId()));
            $data = $this->serviceOutputProcessor
                ->convertValue($paymentDetails, '\Magento\Checkout\Api\Data\PaymentDetailsInterface');
        }

        return $data;
    }
}
