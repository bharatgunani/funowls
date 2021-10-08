<?php

namespace Webindiainc\Prx\Model\ResourceModel\Lenstintcolor;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'lenstintcolor_id';

    protected function _construct() {
        $this->_init('Webindiainc\Prx\Model\Lenstintcolor', 'Webindiainc\Prx\Model\ResourceModel\Lenstintcolor');
    }
}
