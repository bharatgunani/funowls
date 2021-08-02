<?php
namespace RedChamps\GuestOrders\Model;

use RedChamps\GuestOrders\Logger\Logger;
use Magento\Customer\Model\AddressFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

/*
 * Package: GuestOrders
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */

class AccountGenerateAfter
{
    protected $processor;

    protected $addressFactory;

    protected $orderCollectionFactory;

    protected $logger;

    public function __construct(
        CollectionFactory $collectionFactory,
        AddressFactory $addressFactory,
        Processor $processor,
        Logger $logger
    )
    {
        $this->processor = $processor;
        $this->addressFactory = $addressFactory;
        $this->orderCollectionFactory = $collectionFactory;
        $this->logger = $logger;
    }

    public function execute($customer, $needAssignment = "1")
    {
        try {
            $customerId = $customer->getId();
            if (!empty($customerId)) {
                if ($needAssignment != "0") {
                    $this->processor->assignOrdersToCustomer($customer->getEmail(), $customer);
                }
                //assign billing and shipping address to customer
                $orderCollection = $this->orderCollectionFactory->create()
                    ->addFieldToFilter("customer_email", $customer->getEmail())
                    ->setOrder('created_at', 'DESC')
                    ->setPageSize(1)
                    ->setCurPage(1);
                if ($orderCollection) {
                    if ($order = $orderCollection->getFirstItem()) {
                        $shippingAddress = $this->addressFactory->create();
                        if ($order->getShippingAddress()) {
                            $orderShippingAddress = $order->getShippingAddress()->getData();
                            $shippingAddress->setData($orderShippingAddress);
                            $shippingAddress->setIsDefaultBilling('1');
                            $shippingAddress->setIsDefaultShipping(false);
                            $shippingAddress->setSaveInAddressBook('1');
                            $shippingAddress->setCustomerId($customerId);
                            $shippingAddress->save();
                        }

                        $billingAddress = $this->addressFactory->create();
                        if ($order->getBillingAddress()) {
                            $orderBillingAddress = $order->getBillingAddress()->getData();
                            $billingAddress->setData($orderBillingAddress);
                            $billingAddress->setIsDefaultBilling(false);
                            $billingAddress->setIsDefaultShipping('1');
                            $billingAddress->setSaveInAddressBook('1');
                            $billingAddress->setCustomerId($customerId);
                            $billingAddress->save();
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->critical(
                "Error during customer signup: " . $exception->getMessage(),
                ["trace" => $exception->getTraceAsString()]
            );
        }
    }
}

