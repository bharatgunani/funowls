<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Funowls\Plp\Plugin\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePrice as ConfigurablePriceCore;
/**
 * Data provider for price in the Configurable products
 */
class ConfigurablePrice extends ConfigurablePriceCore
{
    /**
     * {@inheritdoc}
     *
     * Added after listener for make price field editable in configurable produc edit page.
     */
    public function afterModifyMeta($subject, $result)
    {
        $groupCode = $this->getGroupCodeByField($result, ProductAttributeInterface::CODE_PRICE)
            ?: $this->getGroupCodeByField($result, ConfigurablePriceCore::CODE_GROUP_PRICE);

        if (!empty($result[$groupCode]['children'][ConfigurablePriceCore::CODE_GROUP_PRICE])) {
            //$result[$groupCode]['children'][ConfigurablePriceCore::CODE_GROUP_PRICE]['children']['price']['arguments']['data']['config']['component'] = 'Magento_Ui/js/form/element/abstract';
        }

        return $result;
    }
}