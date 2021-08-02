<?php
namespace RedChamps\GuestOrders\Cron;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\UrlFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use RedChamps\Core\Api\TimeStampRepositoryInterface;
use RedChamps\GuestOrders\Logger\Logger;
use RedChamps\GuestOrders\Model\ConfigReader;
use RedChamps\GuestOrders\Model\EmailSender;

/*
 * Package: GuestOrders
 * Class: ProcessFollowups
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class ProcessFollowUps
{
    protected $urlFactory;

    protected $emailSender;

    protected $orderCollectionFactory;

    protected $resourceConnection;

    protected $logger;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var TimeStampRepositoryInterface
     */
    private $timeStampRepository;

    /**
     * ProcessFollowUps constructor.
     * @param CollectionFactory $collectionFactory
     * @param UrlFactory $urlFactory
     * @param EmailSender $emailSender
     * @param ConfigReader $configReader
     * @param ResourceConnection $resourceConnection
     * @param Logger $logger
     * @param TimeStampRepositoryInterface $timeStampRepository
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        UrlFactory $urlFactory,
        EmailSender $emailSender,
        ConfigReader $configReader,
        ResourceConnection $resourceConnection,
        Logger $logger,
        TimeStampRepositoryInterface $timeStampRepository
    ) {
        $this->urlFactory = $urlFactory;
        $this->emailSender = $emailSender;
        $this->orderCollectionFactory = $collectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->configReader = $configReader;
        $this->timeStampRepository = $timeStampRepository;
    }

    public function execute()
    {
        if (
            $this->configReader->getConfig('email/enabled') &&
            !$this->configReader->getConfig('auto_customer_accounts/enabled')
        ) {
            $connection = $this->resourceConnection->getConnection();
            $guestOrdersTable = $this->resourceConnection->getTableName('redchamps_guest_orders');
            $records = [];
            $from = $this->timeStampRepository->getByType("guest_orders_follow_up_email")->getLastProcessed();
            $to = $connection->fetchOne("select (NOW() - INTERVAL 1 HOUR)");
            $orderCollection = $this->orderCollectionFactory->create();
            $orderCollection
                ->addFieldToFilter('created_at', [
                    'from'     => $from,
                    'to'       => $to,
                    'datetime' => true
                ])
                ->addFieldToFilter('customer_is_guest', 1)
                ->addFieldToFilter('customer_id', ['null' => true])
                ->addFieldToFilter('state', ["nin" => ["canceled", "closed"]]);
            $orderCollection->getSelect()->joinLeft(
                ["guest_orders" => $guestOrdersTable],
                "main_table.entity_id = guest_orders.order_id"
            );
            $orderCollection->addFieldToFilter(
                "guest_orders.order_id",
                ['null' => true]
            );
            foreach ($orderCollection as $order) {
                $orderId = base64_encode($order->getId());
                $registerUrl = $this->urlFactory->create()
                    ->setScope($order->getStoreId())->getUrl(
                        'guest_orders/action/signup',
                        [
                            "order_id" => $orderId,
                            "_scope" => $order->getStoreId(),
                            '_secure' => true
                        ]
                    );
                try {
                    $this->emailSender->sendEmail($order, $registerUrl);
                    $records[] = ["order_id" => $order->getId(), 'processed' => 0];
                } catch (\Exception $exception) {
                    $this->logger->critical(
                        "Error while processing GuestOrders Followup Email for order# {$order->getIncrementId()}: ".$exception->getMessage(),
                        ["trace" => $exception->getTraceAsString()]
                    );
                }
            }
            if (count($records)) {
                $connection->insertArray(
                    $guestOrdersTable,
                    ["order_id", "processed"],
                    $records,
                    AdapterInterface::INSERT_IGNORE
                );
            }
        }
    }
}
