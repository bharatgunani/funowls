<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Model;

class PaymentConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{

    /**
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Webapi\ServiceOutputProcessor $serviceOutputProcessor,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Onestepcheckout\Iosc\Helper\Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->serviceOutputProcessor = $serviceOutputProcessor;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $return = [];
        if ($this->helper->isEnabled() && !$this->checkoutSession->getQuote()->isVirtual()) {
            $paymentDetails = $this->paymentDetailsFactory->create();
            $paymentDetails->setPaymentMethods($this->paymentMethodManagement
                ->getList($this->checkoutSession->getQuote()->getId()));
            $data = $this->serviceOutputProcessor
                ->convertValue(
                    $paymentDetails,
                    '\Magento\Checkout\Api\Data\PaymentDetailsInterface'
                );
            if (!empty($data['payment_methods'])) {
                $return = [
                'paymentMethods' => $data['payment_methods'],
                ];
            }
        }

        return $return;
    }
}
