<?php

namespace Webindiainc\Prx\Model;

class CustomerPrxData extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'prx_customerdata';
	protected $_cacheTag = 'prx_customerdata';
	protected $_eventPrefix = 'prx_customerdata';

	protected function _construct() {
		$this->_init('Webindiainc\Prx\Model\ResourceModel\CustomerPrxData');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];
		return $values;
	}

}
