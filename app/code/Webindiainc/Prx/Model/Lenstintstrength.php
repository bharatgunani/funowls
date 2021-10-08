<?php

namespace Webindiainc\Prx\Model;

class Lenstintstrength extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'prx_lenstintstrength';
	protected $_cacheTag = 'prx_lenstintstrength';
	protected $_eventPrefix = 'prx_lenstintstrength';

	protected function _construct() {
		$this->_init('Webindiainc\Prx\Model\ResourceModel\Lenstintstrength');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];
		return $values;
	}

}
