<?php

namespace Webindiainc\Prx\Model\ResourceModel\Lenstype;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'lenstype_id';
    
    protected function _construct() {
        $this->_init('Webindiainc\Prx\Model\Lenstype', 'Webindiainc\Prx\Model\ResourceModel\Lenstype');
    }
	
}
