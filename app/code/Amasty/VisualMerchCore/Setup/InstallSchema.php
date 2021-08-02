<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerchCore
 */


namespace Amasty\VisualMerchCore\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->createTable($setup, 'amasty_merchandiser_product_index_eav');
        $this->createTable($setup, 'amasty_merchandiser_product_index_eav_replica');
        $this->createTable($setup, 'amasty_merchandiser_product_index_eav_tmp');

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param string $tableName
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private function createTable(SchemaSetupInterface $setup, string $tableName): void
    {
        $tableName = $setup->getTable($tableName);
        $table = $setup->getConnection()
            ->newTable($tableName)
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Attribute ID'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                10,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Value'
            )
            ->addColumn(
                'source_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Source Id'
            )
            ->addIndex(
                $setup->getIdxName('amasty_merchandiser_product_index_eav', ['attribute_id']),
                ['attribute_id']
            )
            ->addIndex(
                $setup->getIdxName('amasty_merchandiser_product_index_eav', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('amasty_merchandiser_product_index_eav', ['value']),
                ['value']
            )
            ->setComment(
                'Amasty Product EAV Index Table For Merchandiser'
            );
        if (!$setup->getConnection()->isTableExists($tableName)) {
            $setup->getConnection()->createTable($table);
        }
    }
}
