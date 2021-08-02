<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Model\Extend;

class AccountManagement extends \Magento\Customer\Model\AccountManagement
{
    /**
     *
     * {@inheritDoc}
     * @see \Magento\Customer\Model\AccountManagement::checkPasswordStrength()
     */
    public function checkPasswordStrength($password)
    {
        /**
         * Just tunring this method to public, not a useless override
         */
        return parent::checkPasswordStrength($password);
    }
}
