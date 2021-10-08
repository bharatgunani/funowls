<?php

namespace Webindiainc\Prx\Model\ResourceModel\Lensprescription;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'lensprescription_id';

    protected function _construct() {
        $this->_init('Webindiainc\Prx\Model\Lensprescription', 'Webindiainc\Prx\Model\ResourceModel\Lensprescription');
    }
}
