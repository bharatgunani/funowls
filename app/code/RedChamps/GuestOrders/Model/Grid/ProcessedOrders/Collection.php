<?php
namespace RedChamps\GuestOrders\Model\Grid\ProcessedOrders;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{
    protected function _initSelect()
    {
        $result = parent::_initSelect();
        $this->getSelect()->join(
            ["guest_orders" => $this->getTable("redchamps_guest_orders")],
            "main_table.entity_id=guest_orders.order_id",
            ["processed_at", "processed"]
        );
        return $result;
    }
}
