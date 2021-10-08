<?php

namespace Webindiainc\Prx\Model;

class Lenstintcolor extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'prx_lenstintcolor';
	protected $_cacheTag = 'prx_lenstintcolor';
	protected $_eventPrefix = 'prx_lenstintcolor';

	protected function _construct() {
		$this->_init('Webindiainc\Prx\Model\ResourceModel\Lenstintcolor');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];
		return $values;
	}

}
