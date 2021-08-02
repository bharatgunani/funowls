<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


declare(strict_types=1);

namespace Amasty\VisualMerch\Model\ResourceModel\Product;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\DB\Select;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * @var bool
     */
    private $visibleOnlyFlag = true;

    /**
     * @var bool
     */
    private $isClonned = false;

    /**
     * @var bool
     */
    private $useDefaultSortingFlag = false;

    /**
     * @param bool $visibleOnly
     * @return array
     */
    public function getProductIds($visibleOnly = true)
    {
        if (!$this->isClonned) {
            $clonned = clone $this;
            return $clonned->setIsClonned(true)->getProductIds($visibleOnly);
        }
        $this->visibleOnlyFlag = $visibleOnly;
        $this->_beforeLoad();
        $this->_renderFilters();
        $this->_renderOrders();
        $this->getSelect()->reset(Select::LIMIT_COUNT);
        $this->getSelect()->reset(Select::LIMIT_OFFSET);
        $columns = $this->getSelect()->getPart(Select::COLUMNS);
        array_unshift($columns, [
            'e',
            'entity_id',
            null
        ]);
        $this->getSelect()->setPart(Select::COLUMNS, $columns);
        return $this->getConnection()->fetchCol($this->getSelect());
    }

    /**
     * @inheritdoc
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = $this->_getClearSelect();
        /**
         * Keep "order" part in getAllIds() method for merchandising search.
         * Using in Amasty/VisualMerch/Block/Adminhtml/Products/Listing::search()
         */
        $idsSelect->setPart(Select::ORDER, $this->getSelect()->getPart(Select::ORDER));
        $idsSelect->columns('e.' . $this->getEntity()->getIdFieldName());
        $idsSelect->limit($limit, $offset);
        $idsSelect->resetJoinLeft();

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    /**
     * @return $this
     */
    protected function _beforeLoad()
    {
        if ($this->visibleOnlyFlag) {
            $this->addAttributeToFilter('status', Status::STATUS_ENABLED);
            $this->addAttributeToFilter('visibility', [
                'in' => [
                    Visibility::VISIBILITY_IN_CATALOG,
                    Visibility::VISIBILITY_BOTH
                ]
            ]);
        }

        return parent::_beforeLoad();
    }

    /**
     * @return bool
     */
    public function getIsClonned()
    {
        return $this->isClonned;
    }

    /**
     * @param bool $isClonned
     * @return $this
     */
    public function setIsClonned($isClonned = false)
    {
        $this->isClonned = $isClonned;
        return $this;
    }

    public function setUseDefaultSorting(bool $useDefaultSorting = false): void
    {
        $this->useDefaultSortingFlag = $useDefaultSorting;
    }

    public function getUseDefaultSorting(): bool
    {
        return (bool) $this->useDefaultSortingFlag;
    }
}
