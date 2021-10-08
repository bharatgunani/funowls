<?php

namespace Webindiainc\Prx\Model\ResourceModel;

class Lenstintcolor extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_idFieldName = 'lenstintcolor_id';

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
    }

    protected function _construct() {
        $this->_init('prx_lenstintcolor', 'lenstintcolor_id');
    }
}
