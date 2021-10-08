<?php

namespace Webindiainc\Prx\Model\ResourceModel\Lensthickness;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'lensthickness_id';

    protected function _construct() {
        $this->_init('Webindiainc\Prx\Model\Lensthickness', 'Webindiainc\Prx\Model\ResourceModel\Lensthickness');
    }
}
