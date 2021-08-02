<?php
namespace MGS\InstantSearch\Block\SearchResult;
class CustomProduct extends \Magento\Framework\View\Element\Template
{    
    protected $_productCollectionFactory;
        
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,        
        array $data = []
    )
    {    
        $this->_productCollectionFactory = $productCollectionFactory;    
        parent::__construct($context, $data);
    }
    
    public function getProductCollection($product_arr)
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToFilter('entity_id', array('in' => $product_arr));
        $collection->addAttributeToSelect('*');
       
        return $collection;
    }
}
?>