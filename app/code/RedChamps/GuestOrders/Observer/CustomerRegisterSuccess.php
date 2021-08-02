<?php
namespace RedChamps\GuestOrders\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use RedChamps\GuestOrders\Model\AccountGenerateAfter;

/*
 * Package: GuestOrders
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class CustomerRegisterSuccess implements ObserverInterface
{
    private $accountGenerateAfter;

    public function __construct(
        AccountGenerateAfter $accountGenerateAfter
    ) {
        $this->accountGenerateAfter = $accountGenerateAfter;
    }

    public function execute(Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $needAssignment = $observer->getEvent()->getNeedAssignment();
        $this->accountGenerateAfter->execute($customer, $needAssignment);
    }
}
