<?php
namespace Funowls\Pricesort\Plugin\Catalog\Block;
class Toolbar
{
    /**
    * Plugin
    *
    * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
    * @param \Closure $proceed
    * @param \Magento\Framework\Data\Collection $collection
    * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
    */
    public function aroundSetCollection(
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
        \Closure $proceed,
        $collection
    ) {
        $this->_collection = $collection;
        $currentOrder = $toolbar->getCurrentOrder();
        $currentDirection = $toolbar->getCurrentDirection();
        $result = $proceed($collection);

        if ($currentOrder) {
            switch ($currentOrder) {

            case 'newest':
                $this->_collection
                    ->getSelect()
                    ->order('e.created_at DESC');
            break;

            case 'price_desc':
                $this->_collection
                    ->getSelect()
                    ->order('price_index.min_price DESC');
            break;

            case 'price_asc':
                $this->_collection
                    ->getSelect()
                    ->order('price_index.min_price ASC');
            break;

            case 'bestseller':
                 $this->_collection->getSelect()->joinLeft( 
                'sales_order_item', 
                'e.entity_id = sales_order_item.product_id', 
                array('qty_ordered'=>'SUM(sales_order_item.qty_ordered)')) 
                ->group('e.entity_id') 
                ->order('qty_ordered '.$currentDirection);
            break;

            default:        
                $this->_collection->getSelect()->joinLeft( 
                'sales_order_item', 
                'e.entity_id = sales_order_item.product_id', 
                array('qty_ordered'=>'SUM(sales_order_item.qty_ordered)')) 
                ->group('e.entity_id') 
                ->order('qty_ordered '.$currentDirection);
            break;

            }
        }
        //var_dump((string) $this->_collection->getSelect()); You can use this to get a list of all the available sort fields
        return $result;
    }
}