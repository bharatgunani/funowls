<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */

namespace Amasty\VisualMerch\Model\Rule\Condition;

class ProductFactory extends \Magento\CatalogRule\Model\Rule\Condition\ProductFactory
{
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        $instanceName = \Amasty\VisualMerch\Model\Rule\Condition\Product::class
    ) {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }
}
