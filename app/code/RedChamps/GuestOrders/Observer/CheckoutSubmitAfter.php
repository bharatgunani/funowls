<?php
namespace RedChamps\GuestOrders\Observer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use RedChamps\GuestOrders\Logger\Logger;
use RedChamps\GuestOrders\Model\ConfigReader;
use RedChamps\GuestOrders\Model\CustomerAccountGenerator;
use RedChamps\GuestOrders\Model\Processor;

/*
 * Package: GuestOrders
 * Class: CheckoutSubmitAfter
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class CheckoutSubmitAfter implements ObserverInterface
{
    protected $processor;

    protected $storeManager;

    protected $customerRepository;

    protected $scopeConfig;

    protected $customerAccountGenerator;

    protected $logger;

    /**
     * @var ConfigReader
     */
    private $configReader;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        Processor $processor,
        StoreManagerInterface $storeManager,
        ConfigReader $configReader,
        CustomerAccountGenerator $customerAccountGenerator,
        Logger $logger
    ) {
        $this->processor = $processor;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->customerAccountGenerator = $customerAccountGenerator;
        $this->logger = $logger;
        $this->configReader = $configReader;
    }

    public function execute(Observer $observer)
    {
        if ($this->configReader->canProcessImmediately()) {
            $order = $observer->getEvent()->getOrder();
            $email = $order->getCustomerEmail();
            $customerId = $order->getCustomerId();
            if (!$customerId) {
                try {
                    $customer = $this->customerRepository->get($email, $this->storeManager->getWebsite()->getId());
                    $customerId = $customer->getId();
                } catch (NoSuchEntityException $exception) {
                    if ($customer = $this->customerAccountGenerator->execute($order)) {
                        $customerId = $customer->getId();
                    }
                } catch (\Exception $e) {
                    return;
                }
            } else {
                try {
                    $customer = $this->customerRepository->getById($customerId);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ['exception' => $e]);
                    return;
                }
            }
            if (!empty($customerId) && $customer) {
                $this->processor->assignOrdersToCustomer($email, $customer);
            }
        }
    }
}
