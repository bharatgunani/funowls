<?php

namespace Webindiainc\Prx\Helper;

class ConfigData extends \Magento\Framework\App\Helper\AbstractHelper {

	protected $storeManager;
   
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

	public function getConfig($path = '') {
        if($path) {
			return $this->scopeConfig->getValue( $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
		}
        return '';
	}

}