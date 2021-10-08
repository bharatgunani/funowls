<?php

namespace Webindiainc\Prx\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Webindiainc\Prx\Helper\Data as HelperData;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class Prxdata extends Template {
	
	protected $lensusageFactory;
	protected $lenstypeFactory;
	protected $lensthicknessFactory;
	protected $lenstintcolorFactory;
	protected $lenstintstrengthFactory;
	protected $lensprescriptionFactory;
	protected $helper;
	protected $pricingHelper;
	protected $resourceConnection;
	protected $currencyFactory;

	public function __construct(
		Context $context,
		HelperData $helper,
		PricingHelper $pricingHelper,
		\Webindiainc\Prx\Model\LensusageFactory $lensusageFactory,
		\Webindiainc\Prx\Model\LenstypeFactory $lenstypeFactory,
		\Webindiainc\Prx\Model\LensthicknessFactory $lensthicknessFactory,
		\Webindiainc\Prx\Model\LenstintcolorFactory $lenstintcolorFactory,
		\Webindiainc\Prx\Model\LenstintstrengthFactory $lenstintstrengthFactory,
		\Webindiainc\Prx\Model\LensprescriptionFactory $lensprescriptionFactory,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		\Magento\Directory\Model\CurrencyFactory $currencyFactory,
		array $data = []
	)
	{
		$this->helper = $helper;
		$this->pricingHelper = $pricingHelper;
		$this->storeManager = $context->getStoreManager();
		$this->lensusageFactory = $lensusageFactory;
		$this->lenstypeFactory = $lenstypeFactory;
		$this->lensthicknessFactory = $lensthicknessFactory;
		$this->lenstintcolorFactory = $lenstintcolorFactory;
		$this->lenstintstrengthFactory = $lenstintstrengthFactory;
		$this->lensprescriptionFactory = $lensprescriptionFactory;
		$this->resourceConnection = $resourceConnection;
		$this->currencyFactory = $currencyFactory;
		parent::__construct($context, $data);
	}
	
	public function convertPrice($amountValue) {
		$currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
		$baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();
		if ($currentCurrency != $baseCurrency) {
			$rate = $this->currencyFactory->create()->load($baseCurrency)->getAnyRate($currentCurrency);
			$amountValue = $amountValue * $rate;
		}

		return $amountValue;
	}
	
	public function getCurrencySymbol() {
		$currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode(); 
		$currency = $this->currencyFactory->create()->load($currencyCode); 
		return $currencySymbol = $currency->getCurrencySymbol();
	}
	
	public function getLensusageData() {
		$model = $this->lensusageFactory->create();
		$collection = $model->getCollection()->addFieldToFilter('lensusage_status', ['eq' => 1]);
		return $collection;
	}
	
	public function getLenstypeData() {
		$model = $this->lenstypeFactory->create();
		$collection = $model->getCollection()->addFieldToFilter('lenstype_status', ['eq' => 1]);
		return $collection;
	}
	
	public function getLensthicknessData() {
		$model = $this->lensthicknessFactory->create();
		$collection = $model->getCollection()->addFieldToFilter('lensthickness_status', ['eq' => 1]);
		return $collection;
	}
	
	public function getLenstintcolorData($lensthickness_id) {
		$model = $this->lenstintcolorFactory->create();
		$collection = $model->getCollection()
					->addFieldToFilter('lenstintcolor_status', ['eq' => 1])
					->addFieldToFilter('lensthickness_id', ['eq' => $lensthickness_id]);
		return $collection;
	}
	
	public function getLenstintstrengthData($lensthickness_id) {
		$model = $this->lenstintstrengthFactory->create();
		$collection = $model->getCollection()
					->addFieldToFilter('lenstintstrength_status', ['eq' => 1])
					->addFieldToFilter('lensthickness_id', ['eq' => $lensthickness_id]);
		return $collection;
	}
	
	public function getLensprescriptionData() {
		$model = $this->lensprescriptionFactory->create();
		$collection = $model->getCollection()->addFieldToFilter('lensprescription_status', ['eq' => 1]);
		return $collection;
	}
	
	public function getProductData($product_id) {
		return $this->helper->getProductData($product_id);
	}
	
	public function getProductImg($productData) {
		return $this->helper->getProductImg($productData);
	}
	
	public function getProductPriceFormat($price) {
		return $this->pricingHelper->currency($price, true, false); 
	}
	
	public function getSavedPrx($customer_id) {
		$table = $this->resourceConnection->getTableName('prx_customerdata');
		$connection = $this->resourceConnection->getConnection();
		$sql = "SELECT * FROM  $table WHERE `prx_customerid` = $customer_id";
		$result = $connection->fetchAll($sql);
		return $result;
	}
	
	public function getCustomerId() {
		return $this->helper->getCustomerId();
	}
}