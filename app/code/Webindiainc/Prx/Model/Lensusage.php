<?php

namespace Webindiainc\Prx\Model;

class Lensusage extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'prx_lensusage';
	protected $_cacheTag = 'prx_lensusage';
	protected $_eventPrefix = 'prx_lensusage';

	protected function _construct() {
		$this->_init('Webindiainc\Prx\Model\ResourceModel\Lensusage');
	}

	public function getIdentities() {
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues() {
		$values = [];
		return $values;
	}

}
