<?php
namespace Webindiainc\Prx\Controller\Index;

use Webindiainc\Prx\Helper\Data as HelperData;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $_productFactory;
	protected $_productRepository; 
	protected $helper; 

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		HelperData $helper
	)
	{
		$this->_pageFactory = $pageFactory;
		$this->_productFactory = $productFactory;
		$this->_productRepository = $productRepository;
		$this->helper = $helper;
		return parent::__construct($context);
	}

	public function execute()
	{
		$isProductExist = false;
		$product_id = $this->getRequest()->getParam('id');
		if( isset($product_id) && $product_id != '' ) {
			$isProductExist = $this->productExistById($product_id);
			$productData = $this->helper->getProductData($product_id);
			$product_name = $productData->getName();
		}
		
		if(!$isProductExist) {
			$this->getRequest()->initForward();
            $this->getRequest()->setActionName('noroute');
            $this->getRequest()->setDispatched(false);
		}
		$resultPage = $this->_pageFactory->create();
		
		$resultPage->getConfig()->getTitle()->set(__($product_name)); // browser tab title
        $resultPage->getConfig()->setKeywords(__($product_name)); // meta keywords
        $resultPage->getConfig()->setDescription(__($product_name)); //meta description

        /* $pageMainTitle = $resultPage->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__('Account Information')); // Page H1 title
        } */
        return $resultPage;
	}
	
	public function productExistById($productId) {
		try {
			$product = $this->_productRepository->getById($productId);
		} catch (\Exception $e) {
			return false;
		}

		return true;
    }
}
