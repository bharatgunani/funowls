<?php
namespace Funowls\Pdp\Block;
class Pdpdetails extends \Magento\Framework\View\Element\Template
{
    protected $_productloader;
    protected $_registry;
    protected $storemanager;
    protected $productRepository;
    protected $reviewFactory;
    protected $reviewCollectionFactory;
    protected $eavConfigFactory;
    protected $filterProvide;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storemanager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollectionFactory,
        \Magento\Eav\Model\ConfigFactory $eavConfigFactory,
        \Magento\Cms\Model\Template\FilterProviderFactory $filterProvide,
        \Magento\Catalog\Model\ProductFactory $_productloader
        )
    {
        $this->_productloader = $_productloader;
        $this->_registry = $registry;
        $this->storemanager = $storemanager;
        $this->productRepository = $productRepository;
        $this->reviewFactory = $reviewFactory;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->eavConfigFactory = $eavConfigFactory;
        $this->filterProvide = $filterProvide;
        parent::__construct($context);
    }

    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    public function getCurrentProduct()
    {        
        return $this->_registry->registry('current_product');
    }

    public function loadProBySku($sku)
    {
        return $this->productRepository->get($sku);
    }

    public function getBaseUrl()
    {        
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getMediaUrl()
    {        
        return $this->_storeManager->getStore()->getBaseMediaDir();
    }

    public function getProImage($image)
    {
        $store = $this->storemanager->getStore();
        return $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' .$image;
    }

    public function getReview()
    {  
        $collection = $this->reviewCollectionFactory->create()
            ->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->getCurrentProduct()->getId()
            )->setDateOrder();
        $reviewlist = $collection->getData();
        return $reviewlist;
    }

    public function getOptionLabel($attr, $pid)
    {
        $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', $attr);
        $productInfo = $this->getLoadProduct($pid);
        if($attr == 'bridge_size')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getBridgeSize());
        }
        else if($attr == 'lens_width')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getLensWidth()).' mm';
        }
        else if($attr == 'temple_length')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getTempleLength());
        }
        else if($attr == 'mgs_brand')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getMgsBrand());
        }
        else if($attr == 'gender')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getGender());
        }
        else if($attr == 'lens_material')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getLensMaterial());
        }
        else if($attr == 'front_material')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getFrontMaterial());
        }
        else if($attr == 'temple_material')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getTempleMaterial());
        }
        else if($attr == 'hinges')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getHinges());
        }
        else if($attr == 'rim_type')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getRimType());
        }
        else if($attr == 'polarization')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getPolarization());
        }
        else if($attr == 'color_variant')
        {
            return $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getColorVariant());
        }
        else
        {
            return $optionlabel = '';
        }
    }

    public function getProDesc($desc, $pid)
    {
        $productInfo = $this->getLoadProduct($pid);
        if($desc == 'desc')
        {
            return $filterContent =  $this->filterProvide->create()->getPageFilter()->filter($productInfo->getDescription());
        }
        else if($desc == 'short_desc')
        {
            return $filterContent =  $this->filterProvide->create()->getPageFilter()->filter($productInfo->getShortDescription());
        }
        else
        {
            return $filterContent = '';
        }
    }
}