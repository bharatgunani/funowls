<?php

namespace Webindiainc\Prx\Model\ResourceModel\Lenstintstrength;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'lenstintstrength_id';

    protected function _construct() {
        $this->_init('Webindiainc\Prx\Model\Lenstintstrength', 'Webindiainc\Prx\Model\ResourceModel\Lenstintstrength');
    }
}
