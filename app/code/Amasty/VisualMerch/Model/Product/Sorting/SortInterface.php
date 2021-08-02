<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Model\Product\Sorting;

use \Magento\Catalog\Model\ResourceModel\Product\Collection;

interface SortInterface
{
    /**
     * @param Collection $collection
     * @return Collection
     */
    public function sort(Collection $collection);

    /**
     * @return string
     */
    public function getLabel();
}
