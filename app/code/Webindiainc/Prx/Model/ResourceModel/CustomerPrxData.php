<?php

namespace Webindiainc\Prx\Model\ResourceModel;

class CustomerPrxData extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_idFieldName = 'prx_saveid';

    protected function _construct() {
        $this->_init('prx_customerdata', 'prx_saveid');
    }
}

