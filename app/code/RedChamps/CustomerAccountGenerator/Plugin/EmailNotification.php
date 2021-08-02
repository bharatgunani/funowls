<?php
namespace RedChamps\CustomerAccountGenerator\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;

class EmailNotification
{
    protected $emailNotificationAllowed = 1;

    public function aroundNewAccount(
        \Magento\Customer\Model\EmailNotification $subject,
        callable $proceed,
        CustomerInterface $customer,
        $type,
        $backUrl = '',
        $storeId = 0,
        $sendemailStoreId = null
    ) {
        if ($this->emailNotificationAllowed) {
            return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
        }
        return $subject;
    }

    public function setEmailNotificationAllowed($status)
    {
        $this->emailNotificationAllowed = $status;
    }
}
