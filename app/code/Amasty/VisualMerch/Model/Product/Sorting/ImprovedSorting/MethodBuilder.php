<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Model\Product\Sorting\ImprovedSorting;

use Amasty\VisualMerch\Model\Product\Sorting\SortInterface;

class MethodBuilder
{
    /**
     * @var array
     */
    private $methodList = [];

    public function __construct(
        DummyMethodFactory $dummyMethodFactory,
        $methods = []
    ) {
        if(!empty($methods)) {
            uasort($methods, function($first, $last){
                $firstOrder = isset($first['sort_order']) ? $first['sort_order'] : null;
                $lastOrder = isset($last['sort_order']) ? $last['sort_order'] : null;
                if ($firstOrder == $lastOrder) {
                    return 0;
                }
                return ($firstOrder < $lastOrder) ? -1 : 1;
            });
            foreach ($methods as $method) {
                $sortingMethod = $dummyMethodFactory->create();
                $this->methodList[] = $sortingMethod->setData($method);
            }
        }
    }

    /**
     * @return array
     */
    public function getMethodList()
    {
        return $this->methodList;
    }
}
