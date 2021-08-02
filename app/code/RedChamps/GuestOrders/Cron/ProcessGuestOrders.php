<?php
namespace RedChamps\GuestOrders\Cron;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use RedChamps\Core\Api\TimeStampRepositoryInterface;
use RedChamps\GuestOrders\Logger\Logger;
use RedChamps\GuestOrders\Model\ConfigReader;
use RedChamps\GuestOrders\Model\CustomerAccountGenerator;
use RedChamps\GuestOrders\Model\Processor;

/*
 * Package: GuestOrders
 * Class: ProcessGuestOrders
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class ProcessGuestOrders
{
    protected $orderCollectionFactory;

    protected $customerRepository;

    protected $processor;

    protected $storeManager;

    protected $customerAccountGenerator;

    protected $logger;

    protected $configReader;
    /**
     * @var TimeStampRepositoryInterface
     */
    private $timeStampRepository;

    public function __construct(
        TimeStampRepositoryInterface $timeStampRepository,
        CustomerRepositoryInterface $customerRepository,
        Processor $processor,
        ConfigReader $configReader,
        StoreManagerInterface $storeManager,
        CollectionFactory $orderCollectionFactory,
        CustomerAccountGenerator $customerAccountGenerator,
        Logger $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->processor = $processor;
        $this->storeManager = $storeManager;
        $this->customerAccountGenerator = $customerAccountGenerator;
        $this->logger = $logger;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->configReader = $configReader;
        $this->timeStampRepository = $timeStampRepository;
    }

    public function execute()
    {
        if ($this->configReader->getConfig('general/processing_method') == "cron") {
            $timeStampRecord = $this->timeStampRepository->getByType("guest_orders");
            $from = $timeStampRecord->getLastProcessed();
            $orders = $this->orderCollectionFactory->create()
                ->addFieldToFilter('created_at', [
                    'from'       => $from,
                    'datetime' => true
                ])
                ->addFieldToFilter('customer_is_guest', 1)
                ->addFieldToFilter('customer_id', ['null' => true])
                ->setOrder('created_at', 'asc');

            $websites = [];
            $lastProcessedTime = false;

            foreach ($orders as $order) {
                try {
                    $email = $order->getCustomerEmail();
                    $storeId = $order->getStoreId();
                    if (!isset($websites[$storeId])) {
                        $websiteId = $this->storeManager->getStore($storeId)->getWebsiteId();
                        $websites[$storeId] = $websiteId;
                    } else {
                        $websiteId = $websites[$storeId];
                    }
                    try {
                        $customer = $this->customerRepository->get($email, $websiteId);
                    } catch (NoSuchEntityException $exception) {
                        if (!$customer = $this->customerAccountGenerator->execute($order)) {
                            continue;
                        }
                    } catch (\Exception $exception) {
                        $this->logger->critical($exception->getMessage(), ['exception' => $exception]);
                        continue;
                    }
                    $this->processor->assignOrdersToCustomer($email, $customer);
                    $lastProcessedTime = $order->getCreatedAt();
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage(), ["exception" => $e]);
                    continue;
                }
            }
            if ($lastProcessedTime) {
                $this->timeStampRepository->save(
                    $timeStampRecord->setLastProcessed($lastProcessedTime)
                );
            }
        }
    }
}
