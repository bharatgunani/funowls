<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


declare(strict_types = 1);

namespace Amasty\VisualMerch\Model\Rule\Condition;

use Magento\Backend\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Rule\Model\Condition\Context;
use Magento\Store\Model\StoreManagerInterface;

class Product extends AbstractCondition
{
    const CATALOG_PRODUCT_ENTITY_TABLE  = 'amlanding_catalog_product_entity';

    const CONDITIONS_IN_JOIN = [
        '!=',
        '=='
    ];

    const LIMIT = 1;
    const TMP_FIELD = 'tmp_xlanding_attribute';

    /**
     * @var CategoryCollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var CollectionFactory
     */
    protected $_entityAttributeSetCollectionFactory;

    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * @var string
     */
    private $productIdLink;

    /**
     * @var bool
     */
    private $requireProductEntityTable = false;

    /**
     * @var array
     */
    private $staticCategoryIds;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        Context $context,
        Data $backendData,
        Config $config,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        ProductRepositoryInterface $productRepository,
        ProductResource $productResource,
        Collection $attrSetCollection,
        FormatInterface $localeFormat,
        CategoryCollectionFactory $categoryCollectionFactory,
        CollectionFactory $entityAttributeSetCollectionFactory,
        Config $eavConfig,
        ProductMetadataInterface $productMetadata,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendData,
            $config,
            $productFactory,
            $productRepository,
            $productResource,
            $attrSetCollection,
            $localeFormat,
            $data
        );

        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_entityAttributeSetCollectionFactory = $entityAttributeSetCollectionFactory;
        $this->_eavConfig = $eavConfig;
        $this->productIdLink = $productMetadata->getEdition() != 'Community' ? 'row_id' : 'entity_id';
        $this->storeManager = $storeManager;
    }

    public function getAttributeName()
    {
        if ($this->getAttribute()==='attribute_set_id') {
            return __('Attribute Set');
        }

        return $this->getAttributeObject()->getFrontendLabel();
    }

    protected function _prepareValueOptions()
    {
        if ($this->getAttribute() === 'attribute_set_id') {
            $entityTypeId = $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();

            $selectOptions = $this->_entityAttributeSetCollectionFactory->create()
                ->setEntityTypeFilter($entityTypeId)
                ->load()
                ->toOptionArray();

            $this->setData('value_select_options', $selectOptions);
        }

        return parent::_prepareValueOptions();
    }

    public function getInputType()
    {
        if ($this->getAttribute()==='attribute_set_id') {
            return 'select';
        }

        return parent::getInputType();
    }

    public function getValueElementType()
    {
        if ($this->getAttribute()==='attribute_set_id') {
            return 'select';
        }

        return parent::getValueElementType();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getStaticCategoryIds(): array
    {
        if (!$this->staticCategoryIds) {
            $categories = $this->_categoryCollectionFactory->create();
            $categories->addAttributeToFilter('amlanding_is_dynamic', true);
            $this->staticCategoryIds = $categories->getAllIds();
        }

        return $this->staticCategoryIds;
    }

    protected function excludeDynamicCategories(array $ids)
    {
        return array_diff($ids, $this->getStaticCategoryIds());
    }

    public function getFilterValue()
    {
        $value = parent::getValue();

        if ($this->getAttributeObject()->getAttributeCode() == 'category_ids') {
            if (!is_array($value)) {
                $value = array_map('trim', explode(',', $value));
            }
            $value = $this->excludeDynamicCategories($value);

        }

        return $value;
    }

    protected function _getMappingData()
    {
        [$alias, $table, $joinConditions] = $this->getPreparedMappedDataParams();

        return [$alias, $table, $joinConditions];
    }

    public function getStoreManager(): StoreManagerInterface
    {
        return $this->storeManager;
    }

    /**
     * @return array
     */
    private function getPreparedMappedDataParams()
    {
        $joinConditions = '';
        $valueField = 'value';
        $storeId = 0;
        $alias = $this->_getAlias();
        $fieldToTableMap = $this->_getFieldToTableMap($alias);
        if ($fieldToTableMap) {
            [$table, $joinConditions, $valueField] = $fieldToTableMap;
            $table = $this->_productResource->getTable($table);
        } else {
            if ($this->isSelectFromIndexTable()) {
                $storeId = $this->getStoreManager()->getStore()->getId();
                $table = $this->_productResource->getTable('amasty_merchandiser_product_index_eav');
                $this->productIdLink = 'entity_id'; //there is no row_id column in this table
            } elseif ($this->getAttributeObject()->getBackendType()
                === \Magento\Eav\Model\Entity\Attribute\AbstractAttribute::TYPE_STATIC) {
                $table = $this->getAttributeObject()->getBackendTable();
                $joinConditions = sprintf($this->getJoinConditionsPattern(), $alias);
                $valueField = $this->getAttributeObject()->getAttributeCode();
            } else {
                $storeId = $this->getStoreManager()->getStore()->getId();
                if ($this->getAttributeObject()->getScope()
                    === \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL_TEXT) {
                    $storeId = 0;
                }

                $table = $this->getAttributeObject()->getBackendTable();
            }
        }

        $condition = $this->getCondition($alias, $valueField);
        if (!$joinConditions) {
            $attributeId = $this->getAttributeObject()->getId();
            $joinPattern = $this->getJoinConditionsPattern()
                . ' AND %1$s.attribute_id = %2$d AND %1$s.store_id = %3$d';
            $joinConditions = sprintf($joinPattern, $alias, $attributeId, $storeId);

            if ($storeId && !$this->isSelectFromIndexTable()) {
                $defaultAlias = 'tad_' . $alias;
                $alias = [$defaultAlias, $alias];
                $storeId = [0, $storeId];
                $joinConditions = [$joinConditions];
                array_unshift(
                    $joinConditions,
                    sprintf($joinPattern, $alias[0], $attributeId, $storeId[0])
                );
            }
        }
        if ($condition) {
            $joinConditions .= sprintf(' AND %s', $condition);
        }

        return [$alias, $table, $joinConditions, $valueField, $storeId];
    }

    /**
     * @param string|array $alias
     * @param string $valueField
     * @return string|null
     */
    private function getCondition($alias, $valueField): ?string
    {
        $value = $this->getFilterValue();
        $operator  = $this->getOperatorForValidate();

        if (in_array($operator, self::CONDITIONS_IN_JOIN)) {
            $joinCondition = $this->getConditionText($alias, $valueField, $operator, $value);
            $operator = '!';
            $value = null;
        }
        $this->_condition =  $this->getConditionText($alias, $valueField, $operator, $value);

        return $joinCondition ?? null;
    }

    /**
     * @param array|$alias
     * @param string $valueField
     * @param string $operator
     * @param mixed $value
     * @return string
     */
    private function getConditionText($alias, $valueField, $operator, $value): string
    {
        if (!is_array($alias)) {
            $condition = $this->_getCondition($alias, $valueField, $operator, $value);
        } else {
            $eavColumn = "IFNULL(`{$alias[1]}`.`$valueField`, `{$alias[0]}`.`$valueField`)";
            $condition = $this->getOperatorCondition(self::TMP_FIELD, $operator, $value);
            $count = self::LIMIT;
            $condition = str_replace('`' . self::TMP_FIELD . '`', $eavColumn, $condition, $count);
        }

        return $condition;
    }

    /**
     * @return bool
     */
    private function isSelectFromIndexTable()
    {
        return in_array($this->getAttributeObject()->getFrontendInput(), ['select', 'multiselect'], true);
    }

    /**
     * @return string
     */
    private function getJoinConditionsPattern()
    {
        if ($this->productIdLink === 'entity_id') {
            $result = 'e.entity_id = %1$s.';
        } else {
            $result = self::CATALOG_PRODUCT_ENTITY_TABLE . '.row_id = %1$s.';
            $this->requireProductEntityTable = true;
        }

        $result .= $this->productIdLink;
        return $result;
    }

    protected function _getCondition($alias, $valueField, $operator, $value)
    {
        return $this->getOperatorCondition("{$alias}.{$valueField}", $operator, $value);
    }

    protected function _getFieldToTableMap($alias)
    {
        $priceTable = $this->_productResource->getTable('catalog_product_index_price');
        $categoryTable = $this->_productResource->getTable('catalog_category_product');
        $fieldToTableMap = [
            'price' => [
                $priceTable,
                $this->_productResource->getConnection()->quoteInto(
                    'e.entity_id = ' . $alias . '.entity_id AND ' . $alias . '.website_id = ?',
                    $this->getStoreManager()->getStore()->getWebsiteId()
                ),
                'price'
            ],
            'category_ids' => [
                $categoryTable,
                'e.entity_id = ' . $alias . '.product_id',
                'category_id'
            ]
        ];

        $result = array_key_exists($this->getAttributeObject()->getAttributeCode(), $fieldToTableMap)
            ? $fieldToTableMap[$this->getAttributeObject()->getAttributeCode()]
            : null;
        return $result;
    }

    /**
     * @param ProductCollection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        $select = $productCollection->getSelect();
        if ($this->getAttributeObject()->getAttributeId() || $this->getAttributeObject()->isStatic()) {
            [$alias, $table, $joinConditions] = $this->_getMappingData();

            if ($this->requireProductEntityTable
                && !key_exists(self::CATALOG_PRODUCT_ENTITY_TABLE, $select->getPart('from'))) {
                $productEntityTable = $this->_productResource->getTable('catalog_product_entity');
                $select->join(
                    [self::CATALOG_PRODUCT_ENTITY_TABLE => $productEntityTable],
                    self::CATALOG_PRODUCT_ENTITY_TABLE . '.entity_id = e.entity_id',
                    []
                );
            }

            if (is_array($alias) && is_array($joinConditions)) {
                $select->joinLeft([$alias[0] => $table], $joinConditions[0], []);
                $select->joinLeft([$alias[1] => $table], $joinConditions[1], []);
            } else {
                $joinType = 'joinLeft';
                $select->{$joinType}([$alias => $table], $joinConditions, []);
            }
        }
        return $this;
    }

    protected function _getAttributeCode()
    {
        return $this->getAttributeObject()->getAttributeCode();
    }
}
