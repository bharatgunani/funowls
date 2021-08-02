<?php
namespace RedChamps\GuestOrders\Setup;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/*
 * Package: GuestOrders
 * Class: UpgradeSchema
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $connection = $installer->getConnection();

        if (version_compare($context->getVersion(), '1.0.4') < 0) {
            /**
             * Create table 'redchamps_guest_orders'
             */
            $guestOrdersTable = $installer->getConnection()->newTable(
                $installer->getTable('redchamps_guest_orders')
            )->addColumn(
                'order_id',
                Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'identity' => true, 'nullable' => false, 'primary' => true],
                'Order Id'
            )->addColumn(
                'processed_at',
                Table::TYPE_TIMESTAMP,
                '',
                ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
                'Processed At'
            )->addColumn(
                'processed',
                Table::TYPE_BOOLEAN,
                1,
                ['nullable' => false, 'default' => 0],
                'is processed?'
            )->addForeignKey(
                $setup->getFkName(
                    'redchamps_guest_orders',
                    'order_id',
                    'sales_order',
                    'entity_id'
                ),
                'order_id',
                $setup->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )->setComment(
                'Guest Orders Table'
            );

            $installer->getConnection()->createTable($guestOrdersTable);

            //initialise redchamps_guest_orders table
            if ($connection->tableColumnExists($installer->getTable('sales_order'), "is_auto_processed")) {
                $select = $connection->select()->from($installer->getTable('sales_order'), [
                    'order_id' => "entity_id",
                    new \Zend_Db_Expr(1)
                ])->where("is_auto_processed = 1");

                $query = $select->insertFromSelect(
                    $installer->getTable('redchamps_guest_orders'),
                    ['order_id', 'processed']
                );
                $installer->getConnection()->query($query);
            }

            //initialise table
            $lastProcessedTime = $this->scopeConfig->getValue("guest_orders/last_processed/time");
            $select = $connection->select()->from($installer->getTable('sales_order'), [
                'last_processed' => new \Zend_Db_Expr('MAX(created_at)')
            ]);
            $latestOrderTime = $connection->fetchOne($select);
            if(!$lastProcessedTime) {
                $lastProcessedTime = $latestOrderTime;
            }
            $connection->insertMultiple(
                $installer->getTable('redchamps_timestamp'),
                [
                    [
                        'type' => "guest_orders",
                        'last_processed' => $lastProcessedTime
                    ],
                    [
                        'type' => "guest_orders_follow_up_email",
                        'last_processed' => $latestOrderTime
                    ]
                ]
            );

            //remove columns
            $this->dropColumns($installer);
        }
    }

    protected function dropColumns($installer)
    {
        $connection = $installer->getConnection();
        $connection->dropColumn(
            $installer->getTable('sales_order'),
            'is_auto_processed'
        );

        $connection->dropColumn(
            $installer->getTable('sales_order_grid'),
            'is_auto_processed'
        );

        $connection->dropColumn(
            $installer->getTable('sales_order'),
            'guest_order_followed_up'
        );
    }
}
