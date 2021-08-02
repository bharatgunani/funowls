<?php
namespace RedChamps\CustomerAccountGenerator\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\AddressFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\ManagerInterface;
use Psr\Log\LoggerInterface;
use RedChamps\CustomerAccountGenerator\Plugin\EmailNotification;

class GenerateFromOrder
{
    protected $customerFactory;

    protected $customerResourceFactory;

    protected $addressFactory;

    protected $customerAccountManagement;

    protected $dataObjectHelper;

    protected $scopeConfig;

    protected $eventManager;

    protected $emailNotification;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CustomerInterfaceFactory $customerFactory
     * @param CustomerResourceFactory $customerResourceFactory
     * @param AddressFactory $addressFactory
     * @param AccountManagementInterface $customerAccountManagement
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        CustomerInterfaceFactory $customerFactory,
        CustomerResourceFactory $customerResourceFactory,
        AddressFactory $addressFactory,
        AccountManagementInterface $customerAccountManagement,
        DataObjectHelper $dataObjectHelper,
        ScopeConfigInterface $scopeConfig,
        ManagerInterface $eventManager,
        EmailNotification $emailNotification,
        LoggerInterface $logger
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerResourceFactory = $customerResourceFactory;
        $this->addressFactory = $addressFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->scopeConfig = $scopeConfig;
        $this->eventManager = $eventManager;
        $this->emailNotification = $emailNotification;
        $this->logger = $logger;
    }

    public function execute($order)
    {
        if ($order && $order->getId()) {
            $store = $order->getStore();
            $websiteId = $order->getStore()->getWebsiteId();

            $customer = $this->customerFactory->create();

            $billingAddress = $order->getBillingAddress();

            $customerData = [
                'store' => $store,
                'website_id' => $websiteId,
                'firstname' => $billingAddress->getFirstname(),
                'lastname' => $billingAddress->getLastname(),
                'middlename' => $billingAddress->getMiddlename(),
                'email' => $order->getCustomerEmail(),
                'taxvat' => $billingAddress->getVatId(),
                'dob' => $order->getCustomerDob(),
                'prefix' => $billingAddress->getPrefix(),
                'suffix' => $billingAddress->getSuffix(),
                'gender' => $billingAddress->getGender(),
            ];

            $this->dataObjectHelper->populateWithArray(
                $customer,
                $customerData,
                CustomerInterface::class
            );

            try {
                if ($order->getData('account_email_notification') == "0") {
                    $this->emailNotification->setEmailNotificationAllowed(0);
                }
                $customer = $this->customerAccountManagement->createAccount($customer);
                if ($customer) {
                    $this->eventManager->dispatch(
                        'customer_generator_create_after',
                        ['customer' => $customer, 'need_assignment' => $order->getData('need_assignment')]
                    );
                }

                return $customer;
            } catch (\Exception $e) {
                $this->logger->critical(
                    "Error while generating customer account from order: ".$e->getMessage()." ".$e->getTraceAsString()
                );
            }
        }
        return false;
    }
}
