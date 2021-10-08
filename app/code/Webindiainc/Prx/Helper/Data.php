<?php

namespace Webindiainc\Prx\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	protected $_registry;
	protected $lensusageFactory;
	protected $lenstypeFactory;
	protected $lensthicknessFactory;
	protected $lenstintcolorFactory;
	protected $lenstintstrengthFactory;
	protected $lensprescriptionFactory;
	protected $productFactory;
	protected $imageHelper;
	protected $_backendUrl;
	protected $storeManager;
	protected $_customerSession;
	protected $orderItemRepository;
	protected $pricehelper;
	protected $categoryRepository;
	protected $currencyFactory;
	
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Registry $registry,
		\Webindiainc\Prx\Model\LensusageFactory $lensusageFactory,
		\Webindiainc\Prx\Model\LenstypeFactory $lenstypeFactory,
		\Webindiainc\Prx\Model\LensthicknessFactory $lensthicknessFactory,
		\Webindiainc\Prx\Model\LenstintcolorFactory $lenstintcolorFactory,
		\Webindiainc\Prx\Model\LenstintstrengthFactory $lenstintstrengthFactory,
		\Webindiainc\Prx\Model\LensprescriptionFactory $lensprescriptionFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Helper\Image $imageHelper,
		\Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Sales\Api\OrderItemRepositoryInterface $orderItemRepository,
		\Magento\Framework\Pricing\Helper\Data $pricehelper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
		\Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
		$this->_registry = $registry;
		$this->lensusageFactory = $lensusageFactory;
		$this->lenstypeFactory = $lenstypeFactory;
		$this->lensthicknessFactory = $lensthicknessFactory;
		$this->lenstintcolorFactory = $lenstintcolorFactory;
		$this->lenstintstrengthFactory = $lenstintstrengthFactory;
		$this->lensprescriptionFactory = $lensprescriptionFactory;
		$this->productFactory = $productFactory;
		$this->imageHelper = $imageHelper;
		$this->_backendUrl = $backendUrl;
        $this->storeManager = $storeManager;
		$this->_customerSession = $customerSession;
		$this->orderItemRepository = $orderItemRepository;
		$this->pricehelper = $pricehelper;
		$this->categoryRepository = $categoryRepository;
		$this->currencyFactory = $currencyFactory;
        parent::__construct($context);
    }

	public function getCurrencySymbol() {
		$currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode(); 
		$currency = $this->currencyFactory->create()->load($currencyCode); 
		return $currencySymbol = $currency->getCurrencySymbol();
	}
	
	public function getLensusageIds() {
		$model = $this->lensusageFactory->create();
		$collection = $model->getCollection()->addFieldToFilter('lensusage_status', ['eq' => 1]);
		$options = array(['label'=>'Please Select Lensusage', 'value'=>'']);
		foreach($collection as $item){
			$arr = array('label'=>$item->getLensusageTitle(), 'value'=>$item->getLensusageId());
			array_push($options, $arr);
		}
        return $options;
	}
	
	public function getLenstypeIds() {
		$model = $this->lenstypeFactory->create();
		$collection = $model->getCollection()->addFieldToFilter('lenstype_status', ['eq' => 1]);

		$options = array(['label'=>'Please Select Lenstype', 'value'=>'']);
		foreach($collection as $item){
			$arr = array('label'=>$item->getLenstypeIdentifier(), 'value'=>$item->getLenstypeId());
			array_push($options, $arr);
		}
		
        return $options;
	}
	
	public function getLensthicknessIds() {
		$model = $this->lensthicknessFactory->create();
		$collection = $model->getCollection()->addFieldToFilter('lensthickness_status', ['eq' => 1]);

		$options = array(['label'=>'Please Select Lensthickness', 'value'=>'']);
		foreach($collection as $item){
			$arr = array('label'=>$item->getLensthicknessTitle(), 'value'=>$item->getLensthicknessId());
			array_push($options, $arr);
		}
		
        return $options;
	}
	
	public function getProductsIdsFromLens($lensTable, $id, $productFieldName) {
		$Factory = $lensTable . 'Factory';
		$model = $this->$Factory->create();
		$collection = $model->getCollection()->addFieldToSelect($productFieldName)->addFieldToFilter($lensTable.'_id', ['eq' => $id]);
		return $collection->getData();
	}
	
	public function getProductData($product_id) {
		$model = $this->productFactory->create();
		$collection = $model->load($product_id);
        return $collection;
	}
	
	public function getProductImg($productData) {
		$image_url = $this->imageHelper->init($productData, 'product_base_image')->getUrl();
		return $image_url;
	}
	
	public function getProductsGridUrl()
    {
        return $this->_backendUrl->getUrl('prx/*/products', ['_current' => true]);
    }
	
	public function getCustomerId()
    {
        if( $this->_customerSession->isLoggedIn() ) {
			return $customer_id = $this->_customerSession->getId();
		}
		return false;
    }
	
	public function getOrderItem($itemId)
    {
        $itemCollection = $this->orderItemRepository->get($itemId);
        return $itemCollection;
    }
	
	public function getFormatPrice($price) {
		$formattedPrice = $this->pricehelper->currency($price, true, false);
		return $formattedPrice;
	}
	
	public  function getCategoryUrl($categoryId) {
		$category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
		return $category->getUrl();
    }


}