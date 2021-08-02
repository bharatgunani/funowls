<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Model\Product\Sorting;

use \Magento\Catalog\Model\ResourceModel\Product\Collection;

class OutStockBottom extends SortAbstract implements SortInterface
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return __("Move out of stock to bottom");
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function sort(Collection $collection)
    {
        if (!$this->moduleManager->isEnabled('Magento_CatalogInventory')) {
            return $collection;
        }
        parent::sort($collection);
        $collection->getSelect()->joinLeft(
            ['stock_item' => $collection->getResource()->getTable('cataloginventory_stock_item')],
            'stock_item.product_id = e.entity_id and stock_item.stock_id = "'.  $this->getStockId() .'"',
            []
        );

        $collection->getSelect()
            ->order('stock_item.is_in_stock ' . $collection::SORT_ORDER_DESC)
            ->order('e.entity_id ' . $collection::SORT_ORDER_ASC);

        return $collection;
    }
}
