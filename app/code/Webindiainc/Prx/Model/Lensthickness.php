<?php

namespace Webindiainc\Prx\Model;

class Lensthickness extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'prx_lensthickness';
	protected $_cacheTag = 'prx_lensthickness';
	protected $_eventPrefix = 'prx_lensthickness';

	protected function _construct() {
		$this->_init('Webindiainc\Prx\Model\ResourceModel\Lensthickness');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];
		return $values;
	}

}
