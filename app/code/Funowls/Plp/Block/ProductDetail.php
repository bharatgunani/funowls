<?php
namespace Funowls\Plp\Block;

//use Magento\Framework\App\Action\Context;
class ProductDetail extends \Magento\Framework\View\Element\Template

{    
    protected $_productRepository;
    protected $_productCollectionFactory;
    protected $_registry;

        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Cms\Model\Template\FilterProvider $contentProcessor,       
        \Magento\Catalog\Model\ProductRepository $productRepository,
          \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        array $data = []
    )
    {
        $this->contentProcessor = $contentProcessor;
        $this->entityAttribute = $entityAttribute;
        $this->_productRepository = $productRepository;
        $this->_productCollectionFactory = $productCollectionFactory; 
        $this->_registry = $registry;   
        //parent::__construct($context, $data);
        parent::__construct($context, $data);
    }

        /**
        * Load attribute data by code
        * @return  \Magento\Eav\Model\Entity\Attribute
        */
        public function getAttributeInfo($attributeCode)
        {
            return $this->entityAttribute->loadByCode('catalog_product', $attributeCode);
        }

        public function processContent($content)
{
    return $this->contentProcessor->getPageFilter()->filter($content);
}

     public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    } 
    
    public function getLoadedProductCollection($sku)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToFilter('sku', ['in' => $sku]);
        $collection->addAttributeToSelect('*');
       
        return $collection;
    }


    
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
    
    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }
}
?>