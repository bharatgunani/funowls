<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Setup\Operation;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class InstallAttributes
 */
class InstallAttributes
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    public function __construct(
        EavSetup $eavSetup,
        EavConfig $eavConfig
    ) {
        $this->eavSetup = $eavSetup;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        $this->eavSetup->addAttribute(
            Category::ENTITY,
            'amlanding_is_dynamic',
            [
                'type' => 'int',
                'label' => 'Is dynamic category',
                'visible' => true,
                'input' => null,
                'default' => 0,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'note' => "Get products by dynamic rules",
                'group' => 'General Information',
                'sort_order' => 900,
            ]
        );

        $this->eavSetup->updateAttribute(
            Category::ENTITY,
            'amlanding_is_dynamic',
            'frontend_input',
            null
        );

        $this->eavSetup->addAttribute(
            Category::ENTITY,
            'amasty_dynamic_conditions',
            [
                'type' => 'text',
                'label' => 'Dynamic Products Conditions',
                'visible' => false,
                'input' => null,
                'is_user_defined' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General Information',
                'sort_order' => 901,
            ]
        );

        $this->eavSetup->updateAttribute(
            Category::ENTITY,
            'amasty_dynamic_conditions',
            'frontend_input',
            null
        );

        $this->eavSetup->addAttribute(
            Category::ENTITY,
            'amasty_category_product_sort',
            [
                'type' => 'int',
                'label' => 'Category Products Sort',
                'visible' => false,
                'input' => null,
                'is_user_defined' => true,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'group' => 'General Information',
                'sort_order' => 901,
            ]
        );

        $this->eavSetup->updateAttribute(
            Category::ENTITY,
            'amasty_category_product_sort',
            'frontend_input',
            null
        );

        $this->eavConfig->clear();
    }
}
