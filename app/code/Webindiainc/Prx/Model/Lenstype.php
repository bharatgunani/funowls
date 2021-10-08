<?php

namespace Webindiainc\Prx\Model;

class Lenstype extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'prx_lenstype';
	protected $_cacheTag = 'prx_lenstype';
	protected $_eventPrefix = 'prx_lenstype';

	protected function _construct() {
		$this->_init('Webindiainc\Prx\Model\ResourceModel\Lenstype');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];
		return $values;
	}
}
