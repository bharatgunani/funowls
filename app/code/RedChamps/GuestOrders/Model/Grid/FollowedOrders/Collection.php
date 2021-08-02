<?php
namespace RedChamps\GuestOrders\Model\Grid\FollowedOrders;

class Collection extends \RedChamps\GuestOrders\Model\Grid\ProcessedOrders\Collection
{
    protected function _initSelect()
    {
        $result = parent::_initSelect();
        $this->getSelect()->where('guest_orders.processed = ?', 0);
        return $result;
    }
}
