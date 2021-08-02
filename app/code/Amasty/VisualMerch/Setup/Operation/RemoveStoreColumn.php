<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;

class RemoveStoreColumn
{
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('catalog_category_product_static');
        $connection = $setup->getConnection();

        $connection->dropIndex(
            $table,
            $connection->getIndexName($table, ['category_id', 'product_id', 'store_id'])
        );

        $connection->addIndex(
            $table,
            $connection->getIndexName($table, ['category_id', 'product_id']),
            ['category_id', 'product_id'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        );

        $connection->dropColumn($table, 'store_id');
    }
}
