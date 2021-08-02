<?php
namespace Mageplaza\Osc\Plugin\Checkout\Model;

class Defaultconfigprovider extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    private $quoteItem;

    public function __construct(
        \Magento\Quote\Model\Quote\Item $quoteItem,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->quoteItem = $quoteItem;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function afterGetConfig(
        \Magento\Checkout\Model\DefaultConfigProvider $subject,
        array $result
    ) {

            $items = $result['totalsData']['items'];
             $objectManager = \Magento\Framework\App\ObjectManager::getInstance();          

             for($i=0;$i<count($items);$i++){

              $quoteId = $items[$i]['item_id'];
              $quoteNext = ($quoteId + 1);

              $quote = $objectManager->create('\Magento\Quote\Model\Quote\Item')->load($quoteId);
              $simpleProName = $quote->getSku();
              
              $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
              $product = $productRepository->get($quote->getSku());
              $product = $productRepository->getById($product->getId());
              $items[$i]['modelname'] = $product->getData('model_name');
              $items[$i]['size'] = $product->getResource()->getAttribute('lens_width')->getFrontend()->getValue($product) ." mm";
              $items[$i]['brand'] =  $product->getResource()->getAttribute('mgs_brand')->getFrontend()->getValue($product);
             
            }
         $result['totalsData']['items'] = $items;
         return $result;

    }

}