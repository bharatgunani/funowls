<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Plugin;

class SaveManager
{

    /**
     *
     * @param \Onestepcheckout\Iosc\Model\DataManager $dataManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Onestepcheckout\Iosc\Model\MockManager $mockManager
     * @param \Onestepcheckout\Iosc\Helper\Data $helper
     */
    public function __construct(
        \Onestepcheckout\Iosc\Model\DataManager $dataManager,
        \Magento\Framework\App\Request\Http $request,
        \Onestepcheckout\Iosc\Model\MockManager $mockManager,
        \Onestepcheckout\Iosc\Helper\Data $helper
    ) {

        $this->dataManager = $dataManager;
        $this->request = $request;
        $this->mockManager = $mockManager;
        $this->helper = $helper;
    }

    /**
     * Process posted data
     *
     * @param unknown $content
     * @param unknown $billingAddress
     */
    public function processPayload($content, $billingAddress)
    {
        if ($content) {
            $payload = $this->dataManager->deserializeJsonPost($content);
            unset($payload['paymentMethod']);
            unset($payload['shippingMethod']);
            $payload = $this->dataManager->process($payload);
        }
        if (!empty($billingAddress)) {
            $mockedData = $this->getMockedData($this->getMockManager(), $billingAddress);
            $this->addMockedData($billingAddress, $mockedData);
        }
    }

    /**
     * Update \Magento\Quote\Api\Data\AddressInterface with mocked data
     *
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     * @param array $mockedData
     */
    public function addMockedData(\Magento\Quote\Api\Data\AddressInterface $billingAddress, array $mockedData)
    {
        $billingAddress->addData($mockedData);
    }

    /**
     * Get mocked data
     *
     * @param \Onestepcheckout\Iosc\Model\MockManager $mockManager
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     * @return array
     */
    public function getMockedData(
        \Onestepcheckout\Iosc\Model\MockManager $mockManager,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress
    ) {
        return $mockManager->getMockedAddress($billingAddress);
    }

    /**
     * Get \Onestepcheckout\Iosc\Model\MockManager
     */
    public function getMockManager()
    {
        return $this->mockManager;
    }

    /**
     * Get \Magento\Framework\App\Request\Http
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Before plugin to save all posted data before order placement
     *
     * @param unknown $parent
     * @param unknown $cartId
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @param \Magento\Quote\Api\Data\AddressInterface $billingAddress
     */
    public function beforeSavePaymentInformationAndPlaceOrder(
        $parent,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {

        if (!$this->helper->isEnabled()) {
            return;
        }
        $content = $this->getRequest()->getContent();
        $this->processPayload($content, $billingAddress);
    }
}
