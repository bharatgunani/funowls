<?php

namespace Webindiainc\Prx\Model\ResourceModel;

class Lensusage extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_idFieldName = 'lensusage_id';

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    protected function _construct() {
        $this->_init('prx_lensusage', 'lensusage_id');
    }
}
