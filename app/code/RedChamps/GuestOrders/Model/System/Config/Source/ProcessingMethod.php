<?php
namespace RedChamps\GuestOrders\Model\System\Config\Source;

/*
 * Package: GuestOrders
 * Class: ProcessingMethod
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class ProcessingMethod
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'checkout', 'label' => __('Checkout')],
            ['value' => 'cron', 'label' => __('Cron Job')]
        ];
    }
}
