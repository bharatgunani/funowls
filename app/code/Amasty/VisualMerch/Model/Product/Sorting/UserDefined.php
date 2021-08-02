<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Model\Product\Sorting;

use \Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\Registry;

class UserDefined extends SortAbstract implements SortInterface
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Manager $moduleManager,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($moduleManager, $scopeConfig, $data);
        $this->registry = $registry;
    }

    /**
     * @param Collection $collection
     * @return Collection
     */
    public function sort(Collection $collection)
    {
        parent::sort($collection);
        if ($collection->getUseDefaultSorting()) {
            $this->addDefaultSorting($collection);
        }

        return $collection;
    }

    private function addDefaultSorting(Collection $collection): void
    {
        $currentCategory = $this->registry->registry('current_category');
        $currentCategoryId = $currentCategory ? $currentCategory->getId() : null;
        $collection->joinField(
            'position',
            'catalog_category_product',
            'position',
            'product_id=entity_id',
            $currentCategoryId ? 'at_position.category_id = ' . $currentCategoryId : null,
            'left'
        );
        $collection->setOrder('position', $this->ascOrder());
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return __("None");
    }
}
