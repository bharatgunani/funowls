<?php
namespace RedChamps\GuestOrders\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use RedChamps\GuestOrders\Model\AccountGenerateAfter;

/*
 * Package: GuestOrders
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */

class AccountManagementPlugin
{
    private $accountGenerateAfter;

    public function __construct(
        AccountGenerateAfter $accountGenerateAfter
    ) {
        $this->accountGenerateAfter = $accountGenerateAfter;
    }

    public function afterCreateAccount(AccountManagementInterface $subject, $result)
    {
        if ($result) {
            $this->accountGenerateAfter->execute($result);
        }
        return $result;
    }
}

