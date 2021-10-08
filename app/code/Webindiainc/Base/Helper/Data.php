<?php

namespace Webindiainc\Base\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

   protected $_productloader;
   protected $swatchesHelper;
   protected $_registry;
   protected $_storeManager;
   
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $_productloader
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Model\ProductFactory $_productloader,
		\Magento\Swatches\Helper\Data $swatchesHelper,
		\Magento\Framework\Registry $registry,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_productloader = $_productloader;
        $this->swatchesHelper = $swatchesHelper;
		$this->_registry = $registry;
		$this->_storeManager = $storeManager;
        parent::__construct($context);
    }

	public function loadProduct($product_id) {
		return $this->_productloader->create()->load($product_id);
	}

	public function getSwatchesByOptionsId($optionIdArray) {
		return $this->swatchesHelper->getSwatchesByOptionsId($optionIdArray);
	}
	
	public function getCurrentCategory() {
		return $this->_registry->registry('current_category');
	}
	
	public function getCurrentStore() {
		return $this->_storeManager->getStore();
	}

}
