<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Model\Product\Sorting;

use \Magento\Catalog\Model\ResourceModel\Product\Collection;

class PriceAscending extends SortAbstract implements SortInterface
{
    /**
     * @return string
     */
    public function getSortField()
    {
        return 'price';
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __('Price: Ascending');
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function sort(Collection $collection)
    {
        parent::sort($collection);
        $collection->addAttributeToSelect($this->getSortField(), true);
        $collection->addOrder($this->getSortField(), $this->ascOrder());
        return $collection;
    }
}
