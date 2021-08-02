<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Model\Product;

use Amasty\VisualMerch\Model\RuleFactory;
use Magento\Catalog\Api\Data\CategoryInterface;

class AdminhtmlDataProvider extends \Magento\Framework\Model\AbstractModel
{
    const DEFAULT_PRODUCT = 0;
    const DEFAULT_REQUEST_NAME = 'catalog_view_container';
    const DEFAULT_REQUEST_LIMIT = 0;

    /**
     * @var  \Amasty\VisualMerch\Model\Adminhtml\Session
     */
    private $session;

    /**
     * @var \Amasty\VisualMerch\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var Sorting
     */
    private $sorting;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    private $emulation;

    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $defaultStore;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\Framework\Search\Request\Config
     */
    private $searchRequestConfig;

    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Status
     */
    private $stockStatus;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Config\Model\Config $backendConfig,
        \Amasty\VisualMerch\Model\Adminhtml\Session $session,
        \Amasty\VisualMerch\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Amasty\VisualMerch\Model\Product\Sorting $sorting,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\VisualMerch\Model\RuleFactory $ruleFactory,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\Status $stockStatus,
        \Magento\Framework\Search\Request\Config $searchRequestConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->session = $session;
        $this->backendConfig = $backendConfig;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->sorting = $sorting;
        $this->moduleManager = $moduleManager;
        $this->emulation = $emulation;
        $this->defaultStore = current($storeManager->getStores());
        $this->ruleFactory = $ruleFactory;
        $this->searchRequestConfig = $searchRequestConfig;
        $this->stockStatus = $stockStatus;
        $this->initSession();
    }

    /**
     * @param $conditions
     * @return $this;
     */
    public function setSerializedRuleConditions($conditions)
    {
        $this->session->setSerializedRuleConditions($conditions);

        return $this;
    }

    public function initSession()
    {
        $category = $this->getCurrentCategory();

        if ($category) {
            $this->setCategoryId((int)$category->getId());
        }
    }

    /**
     * @return string
     */
    public function getSerializedRuleConditions()
    {
        return $this->session->getSerializedRuleConditions();
    }

    /**
     * @return \Amasty\VisualMerch\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection($storeId = null)
    {
        if (!$this->hasData('product_collection')) {
            $this->emulation->startEnvironmentEmulation($storeId ?: $this->getStoreId());
            $collection = $this->productCollectionFactory->create()
                ->addAttributeToSelect([
                    'sku',
                    'name',
                    'price',
                    'small_image'
                ]);

            $this->stockStatus->addStockDataToCollection($collection, false);

            $this->emulation->stopEnvironmentEmulation();
            $this->orderCollection($collection);
            $this->setData('product_collection', $collection);
        }

        return $this->getData('product_collection');
    }

    /**
     * @param \Amasty\VisualMerch\Model\ResourceModel\Product\Collection $collection
     * @return $this
     */
    private function orderCollection($collection)
    {
        $sourceCollection = $this->getSourceCollection();
        $allIds = $sourceCollection->getProductIds();
        $sortedIds = $this->sortIds($allIds);
        $ids = implode(',', $sortedIds);
        $collection->addIdFilter($sortedIds);
        $field = $sourceCollection->getSelect()->getAdapter()->quoteIdentifier('e.entity_id');

        if ($ids) {
            $collection->getSelect()->order(new \Zend_Db_Expr("FIELD({$field}, {$ids})"));
        }

        if ($this->getRestoreConditions()) {
            $this->restoreConditions($allIds);
        }

        return $this;
    }

    /**
     * @param bool $isRestore
     * @return $this;
     */
    public function setRestoreConditions($isRestore)
    {
        $this->session->setRestoreConditions($isRestore);

        return $this;
    }

    /**
     * @return bool;
     */
    public function getRestoreConditions()
    {
        return (bool)$this->session->getRestoreConditions();
    }

    /**
     * @param array $productIds
     * @return $this
     */
    private function restoreConditions(array $productIds)
    {
        $this->setCategoryProductIds($productIds);

        if (!$this->isDynamicMode()) {
            $this->session->setRestoreConditions(false);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isDynamicMode()
    {
        return (bool)$this->session->getDisplayMode();
    }

    /**
     * @param bool $displayMode
     * @return $this
     */
    public function setDisplayMode($displayMode = false)
    {
        $this->session->setDisplayMode($displayMode);

        return $this;
    }

    /**
     * @param bool $addPriceData
     * @param bool $emulateArea
     *
     * @return \Amasty\VisualMerch\Model\ResourceModel\Product\Collection
     */
    private function getSourceCollection(bool $addPriceData = true, bool $emulateArea = true)
    {
        if (!$this->hasData('source_collection')) {
            if ($emulateArea) {
                $this->emulation->startEnvironmentEmulation($this->getStoreId());
            }

            $collection = $this->productCollectionFactory->create();

            if ($this->getRestoreConditions()) {
                $rule = $this->initRule();
                $rule->applyAttributesFilter($collection);

                if ($this->isEmptyRule($rule)) {
                    $collection->addAttributeToFilter('entity_id', self::DEFAULT_PRODUCT);
                }

                if ($this->getDynamicCollectionLimit()) {
                    $collection->getSelect()->limit($this->getDynamicCollectionLimit());
                }
            } else {
                $collection->addIdFilter(array_merge([self::DEFAULT_PRODUCT], $this->getCategoryProductIds()));
                $collection->setUseDefaultSorting(true);
            }

            if ($addPriceData) {
                $collection->addPriceData();
            }

            $this->setCollectionOrder($collection);
            $this->setData('source_collection', $collection);

            if ($emulateArea) {
                $this->emulation->stopEnvironmentEmulation();
            }
        }

        return $this->getData('source_collection');
    }

    /**
     * @param \Amasty\VisualMerch\Model\ResourceModel\Product\Collection $collection
     * @return $this
     */
    private function setCollectionOrder($collection)
    {
        $this->sorting->applySorting($collection, $this->getSortOrder());

        return $this;
    }

    /**
     * @param $rule
     * @return bool
     */
    private function isEmptyRule($rule)
    {
        $sqlConditions = $rule->getConditions()->collectConditionSql();

        return empty($sqlConditions);
    }

    /**
     * @param array $ids
     * @param array $sortedIds
     *
     * @return array
     */
    private function sortIds($ids, $sortedIds = [])
    {
        $sorted = $sortedIds ?: $this->preparePositionDataForSort($ids);
        $ids = array_diff($ids, $sorted);
        $itemsCount = count($ids) + count($sorted);
        $idx = 0;

        while ($idx < $itemsCount) {
            if (!isset($sorted[$idx]) && current($ids)) {
                $sorted[$idx] = current($ids);
                next($ids);
            }

            $idx++;
        }

        ksort($sorted, SORT_NUMERIC);

        return $sorted;
    }

    /**
     * @param array $ids
     * @return array
     */
    private function preparePositionDataForSort($ids)
    {
        $positionData = array_flip($this->getProductPositionData());
        $positionData = array_intersect($positionData, $ids);
        $maxPosition = count($ids);

        foreach ($positionData as $position => $productId) {
            if ($position > $maxPosition) {
                $positionData[$maxPosition] = $productId;
                $maxPosition--;
            }
        }

        return $positionData;
    }

    /**
     * @return CategoryInterface
     */
    public function initRule()
    {
        $category = $this->getCurrentCategory();
        $rule = $category->getAmastyRule();

        if (!$rule) {
            $rule = $this->ruleFactory->create();
            $conditions = $category->getData('amasty_dynamic_conditions');
            $category->setData('amasty_rule', $rule->setConditionsSerialized($conditions));
        }

        if ($this->getSerializedRuleConditions()) {
            $rule->setConditions([]);
            $rule->setData('conditions_serialized', $this->getSerializedRuleConditions());
        }

        return $rule;
    }

    /**
     * @return CategoryInterface
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * @return array
     */
    public function getProductPositionData()
    {
        return $this->session->getPositionData() ?: [];
    }

    /**
     * @param array $positionData
     * @return $this
     */
    public function setProductPositionData($positionData = [])
    {
        if (!empty($positionData)) {
            $currentPositionData = $this->session->getPositionData() ?? [];

            foreach ($positionData as $productId => $position) {
                $currentPositionData[$productId] = $position;
            }

            $positionData = $currentPositionData;
            $this->session->setPositionData($positionData);
        }

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function unsetProductPositionData($key)
    {
        $data = $this->getProductPositionData();

        if (isset($data[$key])) {
            unset($data[$key]);
            $this->session->setPositionData($data);
        }

        return $this;
    }

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->session->setSortOrder($sortOrder);

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return (int)$this->session->getSortOrder();
    }

    /**
     * @param array $productIds
     * @return $this
     */
    public function setCategoryProductIds(array $productIds = [])
    {
        $positionData = $this->getProductPositionData();
        $diff = array_diff(array_keys($positionData), $productIds);

        foreach ($diff as $productId) {
            $this->resortPositionData($this->getCurrentProductPosition($productId));
            $this->unsetProductPositionData($productId);
        }

        $this->session->setCategoryProductIds($productIds);

        return $this;
    }

    /**
     * @param $productId
     * @return $this
     */
    public function unsetCategoryProductId($productId)
    {
        $productIds = $this->getCategoryProductIds();
        $flipped = array_flip($productIds);

        if (isset($flipped[$productId])) {
            unset($productIds[$flipped[$productId]]);
            $this->setCategoryProductIds($productIds);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getCategoryProductIds()
    {
        return $this->session->getCategoryProductIds() ?: [];
    }

    /**
     * @return int
     */
    public function getInvisibleProductsCount()
    {
        $sourceIds = $this->getCategoryProductIds();
        $allIds = array_unique(array_merge(
            $this->getSourceCollection()->getProductIds(false),
            $sourceIds
        ));
        $collectionProductIds = $this->getSourceCollection()->getProductIds();

        return count(array_diff($allIds, $collectionProductIds));
    }

    /**
     * @param \Magento\Framework\DataObject $entity
     * @return $this
     */
    public function init($entity)
    {
        $this->setCategoryProductIds(array_keys($entity->getProductsPosition()));
        $this->setSerializedRuleConditions($entity->getData('amasty_dynamic_conditions'));
        $this->setDisplayMode($entity->getData('amlanding_is_dynamic'));
        $this->setSortOrder($entity->getData('amasty_category_product_sort'));
        $this->session->setPositionData($entity->getProductPositionData());
        $this->setStoreId($entity->getStoreId());

        return $this;
    }

    /**
     * @param int $categoryId
     */
    public function setCategoryId(int $categoryId)
    {
        $this->session->setCurrentCategoryId($categoryId);
    }

    /**
     * @param int $sourcePosition
     * @param int $destanationPosition
     * @return $this
     */
    public function resortPositionData($sourcePosition, $destanationPosition = null)
    {
        $positionData = $this->getProductPositionData();

        if ($destanationPosition === null) {
            foreach ($positionData as $productId => $position) {
                if ($position > $sourcePosition) {
                    $positionData[$productId]--;
                }
            }
        } elseif ($sourcePosition < $destanationPosition) {
            foreach ($positionData as $productId => $position) {
                if ($position > $sourcePosition && $position <= $destanationPosition) {
                    $positionData[$productId]--;
                }
            }
        } elseif ($sourcePosition > $destanationPosition) {
            foreach ($positionData as $productId => $position) {
                if ($position >= $destanationPosition && $position < $sourcePosition) {
                    $positionData[$productId]++;
                }
            }
        } else {
            return $this;
        }

        $this->session->setPositionData($positionData);

        return $this;
    }

    /**
     * @param int $productId
     * @return int
     */
    public function getCurrentProductPosition($productId)
    {
        $productIds = $this->getSourceCollection()->getProductIds();
        $productIds = $this->sortIds($productIds);
        $position = array_search($productId, $productIds);

        return $position !== false ? $position : count($productIds);
    }

    /**
     * @param $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->session->setStoreId($storeId);

        return $this;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->session->getStoreId() ? $this->session->getStoreId() : $this->defaultStore->getId();
    }

    /**
     * Clear storage data after save category
     *
     * @return $this
     */
    public function clear()
    {
        $this->session->setPositionData(null);
        $this->session->setCategoryProductIds(null);
        $this->setSerializedRuleConditions(null);
        $this->setSortOrder(null);
        $this->setStoreId(null);

        return $this;
    }

    /**
     * @param $storeId
     * @return array
     */
    public function getFullPositionDataByStoreId($storeId)
    {
        $this->emulation->startEnvironmentEmulation($storeId);
        $sourceCollection = $this->getSourceCollection(false, false);
        $visibleProducts = $sourceCollection->getProductIds(true);
        $visibleProducts = $this->sortIds($visibleProducts);
        $allIds = $sourceCollection->getProductIds(false);
        $allIds = $this->sortIds($allIds, $visibleProducts);
        $this->emulation->stopEnvironmentEmulation();

        return array_flip($allIds);
    }

    /**
     * @return int
     */
    private function getDynamicCollectionLimit()
    {
        $requestData = $this->searchRequestConfig->get(self::DEFAULT_REQUEST_NAME);

        return isset($requestData['size']) ? $requestData['size'] : self::DEFAULT_REQUEST_LIMIT;
    }
}
