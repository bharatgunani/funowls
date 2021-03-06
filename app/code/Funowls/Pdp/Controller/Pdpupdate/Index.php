<?php
namespace Funowls\Pdp\Controller\Pdpupdate;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $productdata;
    protected $coreHelpdata;
    protected $catalogAbsBlock;
    protected $_productloader;
    protected $resultJsonFactory;
    protected $eavConfigFactory;
    protected $_entityAttribute;
    protected $_storeManager;
    protected $filterProvide;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Catalog\Model\Product $productdata,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Magento\Catalog\Block\Product\AbstractProduct $catalogAbsBlock,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Eav\Model\ConfigFactory $eavConfigFactory,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProviderFactory $filterProvide,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\Pricing\Helper\Data $coreHelpdata
    ) {
        $this->_pageFactory = $pageFactory;
        $this->productdata = $productdata;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->coreHelpdata = $coreHelpdata;
        $this->catalogAbsBlock = $catalogAbsBlock;
        $this->_productloader = $_productloader;
        $this->eavConfigFactory = $eavConfigFactory;
        $this->_entityAttribute = $entityAttribute;
        $this->_storeManager = $storeManager;
        $this->filterProvide = $filterProvide;
        $this->_blockFactory = $blockFactory;
        $this->_filterProvider = $filterProvider;
        return parent::__construct($context);
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $idData = $postData['id'];
        $pidData = $postData['pId'];
        $productInfo = $_product = $currentProduct = $this->productdata->load($idData, 'entity_id');
        // $attributeData = $this->_entityAttribute->loadByCode('catalog_product', 'frame_color'); $attrType = $attributeData->getFrontendInput();
        $images = $productInfo->getMediaGalleryImages();
        
        $gallery = '';
        $gallery .= '<div class=" hererworking xxx product media">';
        if($images->count()>0)
        {
            $gallery .= '<div class="owl-carousel">';
            foreach($images as $image)
            {
                $gallery .= '<div class="item imgzoom" data-zoom="'.$image->getUrl().'">';
                $gallery .= '<img alt = "'.$productInfo->getName().'" src="'.$image->getUrl().'">';
                $gallery .= '</div>';
            }
            $gallery .= '</div>';
            // $gallery .= '<script type="text/javascript">require(["jquery","mgs/owlcarousel"], function(jQuery){(function($) {var $owl = $(".owl-carousel");function callSlider(){$owl.owlCarousel({margin: 10,nav: true,loop: false,responsive: {0: {items: 1},600: {items: 1},1000: {items: 1}}})}if ($owl.length) {$(".owl-carousel").on("changed.owl.carousel", function(event) {var currentItem = event.property.value - Math.ceil( event.item.count / 2 );if(isNaN(currentItem) || currentItem == 0){currentItem = 1;}else{currentItem = currentItem + 1;}var count = event.item.count;var calc = ( (currentItem) / (count -1) ) * 100;});callSlider();}})(jQuery);}); function zoomElement(el){ require([ "jquery", "zoom-images" ],function($) { var dataZ = $(el).attr("data-zoom"); $(el).zoom({ magnify: 2, url: dataZ }); }); } require([ "jquery", "zoom-images" ],function($) { $(".imgzoom").each(function( index ) { zoomElement(this); }); });</script>';

             $gallery .= '<script type="text/javascript">require(["jquery","mgs/owlcarousel"], function(jQuery){(function($) {var $owl = $(".owl-carousel");function callSlider(){$owl.owlCarousel({margin: 10,nav: true,loop: false,responsive: {0: {items: 1},600: {items: 1},1000: {items: 1}}})}if ($owl.length) {$(".owl-carousel").on("changed.owl.carousel", function(event) {var currentItem = event.property.value - Math.ceil( event.item.count / 2 );if(isNaN(currentItem) || currentItem == 0){currentItem = 1;}else{currentItem = currentItem + 1;}var count = event.item.count;var calc = ( (currentItem) / (count -1) ) * 100;});callSlider();}})(jQuery);}); function zoomElement(el){ require([ "jquery", "zoom-images" ],function($) { var dataZ = $(el).attr("data-zoom"); $(el).zoom({ magnify: 2, url: dataZ }); }); } </script>';
        } 

        $gallery .= '</div>';
        
        $proName = '';
        if($productInfo->getName())
        {
            $proName .= '<h1 class="product-name">'.$productInfo->getName().'</h1>';
        }

        $polarized = '';
        if($productInfo->getPolarization())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'polarization');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getPolarization());
            
            if($optionlabel == 'Polarized')
            {
                $polarized .= '<div class="polarized_tag"><img class="polrized_imageTag" src="'.$this->_storeManager->getStore()->getBaseUrl().'pub/media/wysiwyg/polarized.svg" alt="polarized tag"></div>';
               // $polarized .= '<div class="polarized_tag">Polarized</div>';
            }
            else
            {
                $polarized .= '';
            }
        }

        $colorVarient = '';
        if($productInfo->getColorVariant())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'color_variant');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getColorVariant());
            
            $colorVarient .= '<div class="pro_color_varient">'.$optionlabel.'</div>';
        }

        // $frameColor = '';
        // if($productInfo->getFrameColor())
        // {
        //     $frameColor .= '<div class="frame_color">Frame Color : <div class="frame_color_val">'.$productInfo->getFrameColor().'</div></div>  ';
        // }
        // else
        // {
        //     $frameColor .= '<div class="frame_color">Frame Color :  <div class="frame_color_val">No Color</div></div> ';
        // }

        // $lensColor = '';
        // if($productInfo->getLensColor())
        // {
        //     $lensColor .= '<div class="lens_color">Lens Color :  <div class="lens_color_val">'.$productInfo->getLensColor().'</div></div> ';
        // }
        // else
        // {
        //     $lensColor .= '<div class="lens_color">Lens Color :  <div class="lens_color_val">No Color</div></div> ';
        // }

        $proDesc = '';
        if($productInfo->getDescription())
        {
            $filterContent = $this->filterProvide->create()->getPageFilter()->filter($productInfo->getDescription());
 
            $proDesc .= '<div class="pro_desc">'.$filterContent.'</div>';
        }else{
                $pro_blockId = 'productdetails_block_one';
                $block_content = '';
                $storeId = $this->_storeManager->getStore()->getId();
                $block = $this->_blockFactory->create();
                $block->setStoreId($storeId)->load($pro_blockId);
                $block_content = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
                $proDesc .= '<div class="pro_desc">'.$block_content.'</div>';
        }

        $proShortDesc = '';
        if($productInfo->getShortDescription())
        {
            $filterContent = $this->filterProvide->create()->getPageFilter()->filter($productInfo->getShortDescription());

            $proShortDesc .= '<div class="pro_shortdesc">'.$filterContent.'</div>';
        }

       

        $proInfoTab = '';
        $proInfoTab .= '<table class="proinfo_table" style="width:100%">';
        if($productInfo->getMgsBrand())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'mgs_brand');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getMgsBrand());
            $proInfoTab .= '<tr class="proinfo_col"><td>Brand</td><td>'.$optionlabel.'</td></tr>';
        }


        $proModelName = '';
        if($productInfo->getModelName())
        {

            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'mgs_brand');
            $brnadvalue =  $attrLabel->getSource()->getOptionText($productInfo->getMgsBrand());
            $proModelName .= '<div class="pro_modelName">'.$brnadvalue . ' '. $productInfo->getModelName().'</div>';
        }


        if($productInfo->getModelNumber())
        {
            $proInfoTab .= '<tr class="proinfo_col"> <td>Model Number</td><td>'.$productInfo->getModelNumber().'</td></tr>';
        }

        if($productInfo->getUpc())
        {
            $proInfoTab .= '<tr class="proinfo_col"> <td>UPC</td><td>'.$productInfo->getUpc().'</td></tr>';
        }


        if($productInfo->getFrameColor())
        {
            $proInfoTab .= '<tr class="proinfo_col"> <td>Frame Color</td><td>'.$productInfo->getFrameColor().'</td></tr>';
        }

        if($productInfo->getLensColor())
        {
            $proInfoTab .= '<tr class="proinfo_col"> <td>Lens Color</td><td>'.$productInfo->getLensColor().'</td></tr>';
        }

        if($productInfo->getGender())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'gender');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getGender());
            $proInfoTab .= '<tr class="proinfo_col"> <td>Gender</td><td>'.$optionlabel.'</td></tr>';
        }

        if($productInfo->getLensWidth())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'lens_width');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getLensWidth());
            $proInfoTab .= '<tr class="proinfo_col"> <td>Lens Size</td><td>'.$optionlabel.' mm</td></tr>';
        }

        if($productInfo->getBridgeSize())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'bridge_size');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getBridgeSize());
            $proInfoTab .= '<tr class="proinfo_col"> <td>Bridge Size</td><td>'.$optionlabel.'</td></tr>';
        }

        if($productInfo->getTempleLength())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'temple_length');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getTempleLength());
            $proInfoTab .= '<tr class="proinfo_col"> <td>Temple Length</td><td>'.$optionlabel.'</td></tr>';
        }

        if($productInfo->getLensMaterial())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'lens_material');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getLensMaterial());
            $proInfoTab .= '<tr class="proinfo_col"><td>Lens Material</td><td>'.$optionlabel.'</td></tr>';
        }

        if($productInfo->getFrontMaterial())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'front_material');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getFrontMaterial());
            $proInfoTab .= '<tr class="proinfo_col"><td>Frame Front Material</td><td>'.$optionlabel.'</td></tr>';
        }

        if($productInfo->getTempleMaterial())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'temple_material');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getTempleMaterial());
            $proInfoTab .= '<tr class="proinfo_col"> <td>Temple Material</td><td>'.$optionlabel.'</td></tr>';
        }

        if($productInfo->getLensType())
        {
            $proInfoTab .= '<tr class="proinfo_col"> <td>Lens Type</td><td>'.$productInfo->getLensType().'</td></tr>';
        }

        if($productInfo->getHinges())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'hinges');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getHinges());
            $proInfoTab .= '<tr class="proinfo_col"> <td>Hinges</td><td>'.$optionlabel.'</td></tr>';
        }

        if($productInfo->getRimType())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'rim_type');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getRimType());
            $proInfoTab .= '<tr class="proinfo_col">  <td>Rim</td><td>'.$optionlabel.'</td></tr>';
        }

        $proInfoTab .= '</table>';

        $lensSizeVal = '';
        $lensSizeDim = '';
        if($productInfo->getLensWidth())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'lens_width');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getLensWidth());
            $lensSizeVal .= '<li class="lens_size_val"><b>Lens Size</b>: '.$optionlabel.' mm</li>';
            $lensSizeDim .= '<p class="lens_size_val_img">'.$optionlabel.' mm</p>';
        }
        else
        {
            $lensSizeVal .= '<li class="lens_size_val"><b>Lens Size</b>: </li>';
            $lensSizeDim .= '<p class="lens_size_val_img"></p>';
        }

        $bridgeSize = '';
        $bridgeSizeDim = '';
        if($productInfo->getBridgeSize())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'bridge_size');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getBridgeSize());
            $bridgeSize .= '<li class="bridge_size_val"><b>Bridge Size</b>: '.$optionlabel.'</li>';
            $bridgeSizeDim .= '<p class="bridge_size_img">'.$optionlabel.'</p>';
        }
        else
        {
            $bridgeSize .= '<li class="bridge_size_val"><b>Bridge Size</b>: </li>';
            $bridgeSizeDim .= '<p class="bridge_size_img"></p>';
        }

        $templeLen = '';
        $templeLenDim = '';
        if($productInfo->getTempleLength())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'temple_length');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getTempleLength());
            $templeLen .= '<li class="temple_length_val"><b>Temple Length</b>: '.$optionlabel.'</li>';
            $templeLenDim .= '<p class="temple_length_img">'.$optionlabel.'</p>';
        }
        else
        {
            $templeLen .= '<li class="temple_length_val"><b>Temple Length</b>: </li>';
            $templeLenDim .= '<p class="temple_length_img"></p>';
        }


        $polarizedImage = '';
        if($productInfo->getPolarization())
        {
            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'polarization');
            $optionlabel =  $attrLabel->getSource()->getOptionText($productInfo->getPolarization());
            
            if($optionlabel == 'Polarized')
            {
                 $blockId = 'polarized-image-slider';
                 $html_content = '';
                    if ($blockId) {
                        $storeId = $this->_storeManager->getStore()->getId();
                        $block = $this->_blockFactory->create();
                        $block->setStoreId($storeId)->load($blockId);

                            $html_content = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
                    }
                $polarizedImage .= '<div class="polrized_section"><hr><div class="polar_name"><span>Polarized Glass</span></div>'.$html_content.'</div>';
            }
            else
            {
                $polarizedImage .= '';
            }
        }

       /* $stockStatus = '';
        $stockItem = $productInfo->getExtensionAttributes()->getStockItem();
        $stockData = $stockItem->getQty();
        $backorder=$productInfo->getExtensionAttributes()->getStockItem()->getBackorders();
        if($productInfo->isAvailable() && $stockData > 0)
        {
            $stockStatus .= '<div class="stock available pro_status here"><span class="glyphicon glyphicon-ok"></span><span>In stock - Ready to ship <div class="tooltip-custom end">???<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></div></span></div>';
        }
        else
        {
            if($backorder===0){
                 $stockStatus .= '<div class="stock unavailable pro_status here1"><span class="glyphicon glyphicon-remove"></span><span>Out of Stock <div class="tooltip-custom end">???<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span>
                    </div></span></div>';
            }else{


            $stockStatus .= '<div class="stock unavailable pro_status here12"><span class="glyphicon glyphicon-ok"></span><span>Ships within 3-10 days <div class="tooltip-custom end">???<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></div></span></div>';
           
          }
        }
        */

        $stockStatus = '';
        $stockItem = $productInfo->getExtensionAttributes()->getStockItem();
        $stockData = $stockItem->getQty();
        $backorder=$productInfo->getExtensionAttributes()->getStockItem()->getBackorders();
        if($productInfo->isSaleable() && $stockData > 0)
        {
            $stockStatus .= '<div class="stock available pro_status here"><span class="glyphicon glyphicon-ok"></span><span>In stock - Ready to ship </span></div>';
        }
        
        else
        {
            if($backorder===0){
                 $stockStatus .= '<div class="stock unavailable pro_status here1"><span class="glyphicon glyphicon-remove"></span><span>Out of Stock </span></div>';
            }else{


            $stockStatus .= '<div class="stock unavailable pro_status here12"><span class="glyphicon glyphicon-ok"></span><span>Ships within 3-10 days </span></div>';
           
          }
        }

        $sezzleEmi = '';
        if($productInfo->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $proPrice = $productInfo->getFinalPrice();
            $proFinalPrice = number_format((float)$proPrice/4, 2, '.', '');
            $sezzleEmi .= '<span class="sezzle-payment-amount sezzle-button-text">CAD$'.$proFinalPrice.'&nbsp;</span>';
        }
        else
        {
            if($productInfo->getSpecialPrice())
            {
                $proPrice = $productInfo->getSpecialPrice();
            }
            else
            {
                $proPrice = $productInfo->getPrice();
            }
            
            $proFinalPrice = number_format((float)$proPrice/4, 2, '.', '');
            $sezzleEmi .= '<span class="sezzle-payment-amount sezzle-button-text">CAD$'.$proFinalPrice.'&nbsp;</span>';
        }

        $proDiscountPrice = '';
        if($productInfo->getSpecialPrice())
        {
            $orgprice = $productInfo->getPrice();
            $specialprice = $productInfo->getSpecialPrice();
            $saleAmt = round($orgprice - $specialprice);
            $discountAmt =  round($saleAmt * 100 / $orgprice);

            $proDiscountPrice .= '<div class="discount_amount_tag"><div class="discount-amt"><span class="discount-per">'.$discountAmt.'%<span> off</div></div>';
        }

        $proPriceGuarantee = '';
        if($productInfo->getBestPriceGuarantee())
        {
            $bestPriceGuaranteeImage = $this->_storeManager->getStore()->getBaseUrl().'pub/media/wysiwyg/bestPriceGuarantee.jpeg';
            $proPriceGuarantee .= '<div class="price_guarantee"><img src="'.$bestPriceGuaranteeImage.'" alt="Price Guarantee"></div>';
        }
        

        $_response['gallery'] = $gallery;
        $_response['proName'] = $proName;
        $_response['polarized'] = $polarized;
        // $_response['frameColor'] = $frameColor;
        // $_response['lensColor'] = $lensColor;
        $_response['proDesc'] = $proDesc;
        $_response['proShortDesc'] = $proShortDesc;
        $_response['proModelName'] = $proModelName;
        $_response['colorVarient'] = $colorVarient;
        $_response['proDiscountPrice'] = $proDiscountPrice;
        $_response['proPriceGuarantee'] = $proPriceGuarantee;
        $_response['proInfoTab'] = $proInfoTab;
        $_response['polarizedImage'] = $polarizedImage;
        $_response['lensSizeVal'] = $lensSizeVal;
        $_response['bridgeSize'] = $bridgeSize;
        $_response['templeLen'] = $templeLen;
        $_response['stockStatus'] = $stockStatus;
        $_response['sezzleEmi'] = $sezzleEmi;
        $_response['templeLenDim'] = $templeLenDim;
        $_response['bridgeSizeDim'] = $bridgeSizeDim;
        $_response['lensSizeDim'] = $lensSizeDim;
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($_response);
    }

    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }
}
