<?php

namespace Webindiainc\Prx\Model\ResourceModel;

class Lensthickness extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_idFieldName = 'lensthickness_id';

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    protected function _construct() {
        $this->_init('prx_lensthickness', 'lensthickness_id');
    }
}
