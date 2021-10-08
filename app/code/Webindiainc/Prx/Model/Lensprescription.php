<?php

namespace Webindiainc\Prx\Model;

class Lensprescription extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'prx_lensprescription';
	protected $_cacheTag = 'prx_lensprescription';
	protected $_eventPrefix = 'prx_lensprescription';

	protected function _construct() {
		$this->_init('Webindiainc\Prx\Model\ResourceModel\Lensprescription');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];
		return $values;
	}

}
