<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */

namespace Amasty\VisualMerch\Model\Rule\Condition\Price;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

class AbstractPrice extends \Amasty\VisualMerch\Model\Rule\Condition\AbstractCondition
{
    protected $_inputType = 'numeric';

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Magento\Eav\Model\Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $attrSetCollection,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        array $data = []
    ) {
        return parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat
        );
    }

    public function getAttributeElementHtml()
    {
        return __('Price');
    }

    protected function _getAttributeCode()
    {
        return 'price';
    }

    protected function _getCondition()
    {
        if (!$this->_condition) {
            $alias = $this->_getAlias();

            $value     = $this->getValue();
            $operator  = $this->getOperatorForValidate();

            $this->_condition = $this->getOperatorCondition(
                $alias . '.' . $this->_getAttributeCode(),
                $operator,
                $value
            );
        }
        return $this->_condition;
    }

    /**
     * @param Collection $productCollection
     * @return AbstractPrice|void
     */
    public function collectValidatedAttributes($productCollection)
    {
        $select = $productCollection->getSelect();
        $alias = $this->_getAlias();

        $this->_condition = $this->_getCondition();

        $select->joinLeft(
            [
                $alias => $this->_productResource->getTable('catalog_product_index_price')
            ],
            $this->_productResource->getConnection()->quoteInto(
                'e.entity_id = ' . $alias . '.entity_id AND ' . $alias . '.website_id = ?',
                $this->getStoreManager()->getStore()->getWebsiteId() /** @todo website id 0 or current */
            ),
            []
        );

        //fix error "You cannot define a correlation name '$correlationName' more than once
        if (strpos($select, 'catalog_rule') === false) {
            $select->joinLeft(
                ['catalog_rule' => $this->_productResource->getTable('catalogrule_product_price')],
                'catalog_rule.product_id = e.entity_id',
                []
            );
        }

        //$select->where($this->_condition);
    }

    public function collectConditionSql()
    {
        return $this->_getCondition();
    }
}
