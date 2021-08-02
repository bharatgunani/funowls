<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


declare(strict_types=1);

namespace Amasty\VisualMerch\Setup\Operation;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;

class DisableIsRequiredOptionForMerchAttributes
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    const ATTRIBUTES_FOR_CHANGE = [
        'amlanding_is_dynamic',
        'amasty_dynamic_conditions',
        'amasty_category_product_sort'
    ];

    public function __construct(
        EavSetup $eavSetup,
        EavConfig $eavConfig
    ) {
        $this->eavSetup = $eavSetup;
        $this->eavConfig = $eavConfig;
    }

    public function execute()
    {
        foreach (self::ATTRIBUTES_FOR_CHANGE as $attribute) {
            $this->eavSetup->updateAttribute(
                Category::ENTITY,
                $attribute,
                'is_required',
                false
            );
        }

        $this->eavConfig->clear();
    }
}
