<?php
declare(strict_types=1);

namespace Web\Base\Plugin;

use Magento\Customer\Model\EmailNotification;

class DisableNotification
{
    /**
    * Disable admin Email notifications
    *
    * @param EmailNotification $subject
    * @param \Closure $proceed
    * @return EmailNotification
    */
   public function aroundNewAccount(
      EmailNotification $subject,
      \Closure $proceed
  ) {
      return $subject;
    }
}