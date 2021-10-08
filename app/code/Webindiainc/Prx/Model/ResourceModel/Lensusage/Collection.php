<?php

namespace Webindiainc\Prx\Model\ResourceModel\Lensusage;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected $_idFieldName = 'lensusage_id';

    protected function _construct() {
        $this->_init('Webindiainc\Prx\Model\Lensusage', 'Webindiainc\Prx\Model\ResourceModel\Lensusage');
    }
}
