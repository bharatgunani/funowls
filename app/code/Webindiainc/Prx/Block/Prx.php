<?php

namespace Webindiainc\Prx\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Webindiainc\Prx\Helper\Data as HelperData;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class Prx extends Template {
	
	protected $lensusageFactory;
	protected $lenstypeFactory;
	protected $lensthicknessFactory;
	protected $lenstintcolorFactory;
	protected $lenstintstrengthFactory;
	protected $lensprescriptionFactory;
	protected $helper;
	protected $pricingHelper;
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
	
	public function isCurrencyChange() {
		$currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
		$baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();
		if ($currentCurrency != $baseCurrency) {
			return true;
		}
		return false;
	}
	
	public function getCurrencySymbol() {
		$currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode(); 
		$currency = $this->currencyFactory->create()->load($currencyCode); 
		return $currencySymbol = $currency->getCurrencySymbol();
	}
	
	public function getBaseCurrencySymbol() {
		$currencyCode = $this->storeManager->getStore()->getBaseCurrency()->getCode();
		$currency = $this->currencyFactory->create()->load($currencyCode); 
		return $currencySymbol = $currency->getCurrencySymbol();
	}
	
	public function getLensusageData($product_id) {
		$product_id_with_comma = ',' . $product_id . ',';
		$model = $this->lensusageFactory->create();
		$collection = $model->getCollection()
			->addFieldToFilter('lensusage_status', ['eq' => 1])
			->addFieldToFilter(
				['lensusage_products', 'lensusage_products'],
    			[
					['nlike' => '%'.$product_id_with_comma.'%'],
					['eq' => '']
				]
			);
		
		return $collection;
	}
	
	public function getLenstypeData($product_id) {
		$product_id_with_comma = ',' . $product_id . ',';
		$model = $this->lenstypeFactory->create();
		$collection = $model->getCollection()
			->addFieldToFilter('lenstype_status', ['eq' => 1])
			->addFieldToFilter(
				['lenstype_products', 'lenstype_products'],
    			[
					['nlike' => '%'.$product_id_with_comma.'%'],
					['eq' => '']
				]
			);
		$collection->setOrder('lenstype_position', 'asc');
		return $collection;
	}
	
	public function getLensthicknessData($product_id) {
		$product_id_with_comma = ',' . $product_id . ',';
		$model = $this->lensthicknessFactory->create();
		$collection = $model->getCollection()
			->addFieldToFilter('lensthickness_status', ['eq' => 1])
			->addFieldToFilter(
				['lensthickness_products', 'lensthickness_products'],
    			[
					['nlike' => '%'.$product_id_with_comma.'%'],
					['eq' => '']
				]
			);
		return $collection;
	}
	
	public function getLenstintcolorData($lenstype_id) {
		$lenstype_id_with_comma = ',' . $lenstype_id . ',';
		$model = $this->lenstintcolorFactory->create();
		$collection = $model->getCollection()
					->addFieldToFilter('lenstintcolor_status', ['eq' => 1])
					->addFieldToFilter('lenstype_id', ['like' => '%'.$lenstype_id_with_comma.'%']);
		return $collection;
	}
	
	public function getLenstintstrengthData($lenstype_id) {
		$lenstype_id_with_comma = ',' . $lenstype_id . ',';
		$model = $this->lenstintstrengthFactory->create();
		$collection = $model->getCollection()
					->addFieldToFilter('lenstintstrength_status', ['eq' => 1])
					->addFieldToFilter('lenstype_id', ['like' => '%'.$lenstype_id_with_comma.'%']);
		return $collection;
	}
	
	public function getLensprescriptionData() {
		$model = $this->lensprescriptionFactory->create();
		$collection = $model->getCollection()
			->addFieldToFilter('lensprescription_status', ['eq' => 1])
			->setOrder('position', 'ASC');
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
	
	public function getCustomerId() {
		return $this->helper->getCustomerId();
	}
}