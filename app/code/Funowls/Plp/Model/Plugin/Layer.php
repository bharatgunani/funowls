<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Funowls\Plp\Model\Plugin;

class Layer
{
    public function afterGetProductCollection($subject, $collection)
    {
        $collection->addAttributeToFilter('type_id', 'configurable');
        return $collection;
    }
}
