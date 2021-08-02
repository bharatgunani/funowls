<?php
namespace RedChamps\GuestOrders\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ManagerInterface;
use RedChamps\GuestOrders\Logger\Logger;

/*
 * Package: GuestOrders
 * Class: Processor
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class Processor
{
    protected $resourceConnection;

    protected $eventManager;

    protected $connection;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        ResourceConnection $resourceConnection,
        ManagerInterface $eventManager,
        Logger $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->connection = $this->resourceConnection->getConnection();
        $this->eventManager = $eventManager;
        $this->logger = $logger;
    }

    /**
     * Set customer_id in sales_order and sales_order_grid table
     *
     * @param $email
     * @param CustomerInterface $customer
     */
    public function assignOrdersToCustomer($email, $customer)
    {
        try {
            //get resource and write connection
            $connection = $this->connection;

            $customerId = $customer->getId();
            $customerGroup = $customer->getGroupId();
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $customerFirstName = $customer->getFirstname();
            $customerLastName  = $customer->getLastname();
            $customerMiddleName = $customer->getMiddlename();
            $customerDob = $customer->getDob();
            $customerPrefix = $customer->getPrefix();
            $customerSuffix = $customer->getSuffix();
            $customerTaxVat = $customer->getTaxvat();

            //get table names
            $orderTable = $connection->getTableName('sales_order');
            $orderGridTable = $connection->getTableName('sales_order_grid');

            //prepare and run queries
            $conditions[] = $connection->quoteInto('customer_email = ?', $email);
            $conditions[] = 'customer_id IS NULL';

            //Get ids of orders that are going to be updated
            $orderSelectQuery = $connection->select()
                ->from($orderTable, 'entity_id');
            foreach ($conditions as  $condition) {
                $orderSelectQuery->where($condition);
            }

            $orderIds = $connection->fetchCol($orderSelectQuery);

            //update order table
            $connection->update(
                $orderTable,
                [
                    'customer_id' => $customerId,
                    'customer_is_guest' => 0,
                    'customer_group_id' => $customerGroup,
                    'customer_firstname' => $customerFirstName,
                    'customer_lastname' => $customerLastName,
                    'customer_middlename' => $customerMiddleName,
                    'customer_dob' => $customerDob,
                    'customer_prefix' => $customerPrefix,
                    'customer_suffix' => $customerSuffix,
                    'customer_taxvat' => $customerTaxVat
                ],
                $conditions
            );

            //logic to update order grid table
            $whereConditionOrders = '';
            $whereConditionDownloadable = '';

            if (count($orderIds) > 1) {
                $implodedOrderIds = implode(',', $orderIds);
                $whereConditionOrders = "entity_id in ($implodedOrderIds) and customer_id IS NULL";
                $whereConditionDownloadable = "order_id in ($implodedOrderIds) and customer_id IS NULL";
            } elseif (count($orderIds) == 1) {
                $orderId = $orderIds[0];
                $whereConditionOrders = "entity_id = {$orderId} and customer_id IS NULL";
                $whereConditionDownloadable = "order_id = {$orderId} and customer_id IS NULL";
            }

            if ($whereConditionOrders != '') {
                $connection->update(
                    $orderGridTable,
                    [
                        'customer_id' => $customerId,
                        'customer_group' => $customerGroup,
                        'customer_name' => $customerName
                    ],
                    $whereConditionOrders
                );
            }

            //process downloadable links
            if ($whereConditionDownloadable != '') {
                $connection->update(
                    $connection->getTableName('downloadable_link_purchased'),
                    [
                        'customer_id' => $customerId,
                    ],
                    $whereConditionDownloadable
                );
            }

            //log updated orders
            $this->saveRecords($orderIds);

            $this->eventManager->dispatch(
                'guest_order_assign_after',
                [
                    'email' => $email,
                    'customer' => $customer,
                    'orders' => $orderIds
                ]
            );
            return $orderIds;
        } catch (\Exception $exception) {
            $this->logger->addCritical(
                "Error  while assigning GuestOrder: ".$exception->getMessage(),
                ['trace' => $exception->getTraceAsString()]
            );
            return $exception->getMessage();
        }
    }

    protected function saveRecords($orderIds)
    {
        if($orderIds) {
            $data = [];
            foreach ($orderIds as $orderId) {
                $data[] = [
                    'order_id' => $orderId,
                    'processed' => 1
                ];
            }
            $this->connection->insertOnDuplicate(
                $this->connection->getTableName("redchamps_guest_orders"),
                $data
            );
        }
    }
}
