<?php
namespace RedChamps\GuestOrders\Model\Grid\PendingOrders;

class Collection extends \Magento\Sales\Model\ResourceModel\Order\Grid\Collection
{
    protected function _initSelect()
    {
        $result = parent::_initSelect();
        $this->getSelect()->join(
            ["order" => $this->getTable("sales_order")],
            "main_table.entity_id=order.entity_id",
            "customer_is_guest"
        )->joinLeft(
            ["guest_orders" => $this->getTable("redchamps_guest_orders")],
            "main_table.entity_id=guest_orders.order_id",
            "processed"
        )->where(
            'guest_orders.order_id IS NULL and order.customer_id IS NULL and order.customer_is_guest=1'
        );
        return $result;
    }
}
