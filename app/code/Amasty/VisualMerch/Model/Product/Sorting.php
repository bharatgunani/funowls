<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Model\Product;

use Amasty\VisualMerch\Model\Product\Sorting\Factory as SortingFactory;
use Amasty\VisualMerch\Model\Product\Sorting\ImprovedSorting\DummyMethod;
use Amasty\VisualMerch\Model\Product\Sorting\ImprovedSorting\MethodBuilder;
use \Amasty\VisualMerch\Model\Product\Sorting\SortInterface;
use Amasty\VisualMerch\Model\Product\Sorting\UserDefined;
use \Magento\Catalog\Model\ResourceModel\Product\Collection;

class Sorting
{
    /**
     * @var array
     */
    protected $sortMethods = [
        'UserDefined',
        'OutStockBottom',
        'NewestTop',
        'NameAscending',
        'NameDescending',
        'PriceAscending',
        'PriceDescending',
    ];

    /**
     * @var SortingFactory
     */
    protected $factory;

    /**
     * @var array
     */
    protected $sortInstances = [];

    /**
     * @param SortingFactory $factory
     */
    public function __construct(
        SortingFactory $factory,
        MethodBuilder $improvedMethodBuilder
    ) {
        $this->factory = $factory;
        foreach ($this->sortMethods as $className) {
            $this->sortInstances[] = $this->factory->create($className);
        }
        foreach ($improvedMethodBuilder->getMethodList() as $method) {
            $this->sortInstances[] = $method;
        }
    }

    /**
     * @return array
     */
    public function getSortingOptions()
    {
        $options = $default = $improved = [];
        foreach ($this->sortInstances as $idx => $instance) {
            if ($instance instanceof DummyMethod) {
                $improved[$idx] = $instance->getLabel();
            } elseif ($instance instanceof UserDefined) {
                $options[$idx] = $instance->getLabel();
            } else {
                $default[$idx] = $instance->getLabel();
            }
        }

        $options[__('Default Sorting')->render()] = $default;
        if ($improved) {
            $options[__('Improved Sorting')->render()] = $improved;
        } else {
            $options[__('Improved Sorting (not installed)')->render()] = [];
        }

        return $options;
    }

    /**
     * Get the instance of the first option which is None
     *
     * @param int $sortOption
     * @return SortInterface|null
     */
    public function getSortingInstance($sortOption)
    {
        if (isset($this->sortInstances[$sortOption])) {
            return $this->sortInstances[$sortOption];
        }
        return $this->sortInstances[0];
    }

    /**
     * @param Collection $collection
     * @param int $sortingMethod = null
     * @return Collection
     */
    public function applySorting(Collection $collection, $sortingMethod = null)
    {
        $sortBuilder = $this->getSortingInstance($sortingMethod);
        $sortedCollection = $sortBuilder->sort($collection);
        $sortedCollection->addOrder('entity_id', Collection::SORT_ORDER_ASC);

        if ($sortedCollection->isLoaded()) {
            $sortedCollection->clear();
        }

        return $sortedCollection;
    }
}
