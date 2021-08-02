<?php

namespace Funowls\Plp\Plugin\Block\ConfigurableProduct\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ConfigFactory;
use Magento\Catalog\Helper\Image;
class Configurable
{

    protected $jsonEncoder;
    protected $jsonDecoder;
    protected $stockRegistry;
    protected $eavConfigFactory;
    protected $productFactory;
    protected $_storeManager;
    protected $filterProvide;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    public function __construct(
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        StockRegistryInterface $stockRegistry,
        ProductFactory $productFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProviderFactory $filterProvide,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        ConfigFactory $eavConfigFactory
    ) {

        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->stockRegistry = $stockRegistry;
        $this->productFactory = $productFactory;
        $this->eavConfigFactory = $eavConfigFactory;
        $this->_blockFactory = $blockFactory;
        $this->_imageHelper = $imageHelper;
        $this->_storeManager = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->filterProvide = $filterProvide;
    }

    public function aroundGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    )
    {
        $brandArr = [];
        $proModelNameArr = [];
        $modelNameArr = $sezzleEmisArr = $colorVarientArr = $imagesArr = $countimagesArr = [];
        $proDiscountPriceArr = $bridgeSizeArr = $bridgeSizeDimArr = $proPriceArr = $proUrlColorVarientArr = [];
        $proUrlLensWidthArr = $proInfoTabArr = $proUrlArr = [];
        $proDiscountArr = $pro_shortdescArr = $lensSizeDimArr = $pro_descArr = $proPriceGuaranteeArr =$polarizedImageArr = $stockStatusArr = [];
        $templeLenDimArr = $templeLenArr = [];
        $maxDiscount = array();
        $maxProArr = array();
        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);
        $checkInStockArr = [];
        $currentConfProId = $config['productId'];
        $configProduct = $this->productFactory->create()->load($currentConfProId);
        $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
        $maxDiscountPro = $stockStatusclassArr = [];
        $maxSaleDiscoutArr = [];
        $maxFinalSaleDiscoutArr = [];
        $attributesLens = [];
        $attributesFrame = [];
        foreach ($_children as $child)
        {
            $simpleProduct = $this->productFactory->create()->load($child->getId());
            if($simpleProduct->getSpecialPrice())
            {
                $maxDiscountPro[$simpleProduct->getId()] = $simpleProduct->getColorVariant();
            }
            
        }

        if(!empty($maxDiscountPro))
        {
            $maxSaleDiscoutArr = array_count_values($maxDiscountPro);

            foreach ($maxDiscountPro as $key => $value) {
                if($maxSaleDiscoutArr[$value] > 1)
                {
                    $maxFinalSaleDiscoutArr[$value][] = $key;
                }
            }
        }
        
        foreach ($subject->getAllowProducts() as $simpleProduct) {
            if($simpleProduct->getSpecialPrice())
            {
                $orgprice = $simpleProduct->getPrice();
                $specialprice = $simpleProduct->getSpecialPrice();
                $saleAmt = round($orgprice - $specialprice);
                $discountAmt =  round($saleAmt * 100 / $orgprice);
                $proPriceArr[$simpleProduct->getId()] = '<div class="pro-price splPrice"><div class="org-price">CAD$'.round($orgprice).'</div><div class="spl-price">C$'.round($specialprice).'</div></div>';
            }
            else
            {
                $orgprice = $simpleProduct->getPrice();
                $proPriceArr[$simpleProduct->getId()] = '<div class="pro-price"><div class="org-price">CAD$'.round($orgprice).'</div></div>';
            }

            $simpleProductInfo = $this->productFactory->create()->load($simpleProduct->getId());

            $attributesLens[$simpleProductInfo->getId()] = $simpleProductInfo->getFrameColor() ? $simpleProductInfo->getFrameColor() : '';
            $attributesFrame[$simpleProductInfo->getId()] = $simpleProductInfo->getLensColor() ? $simpleProductInfo->getLensColor() : '';

            $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'mgs_brand');
            $brandName =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getMgsBrand());
            $proModelName = $brandName . ' '. $simpleProductInfo->getModelName();
        
            $brandArr[$simpleProduct->getId()] = $brandName;
            $proModelNameArr[$simpleProduct->getId()] = $proModelName;

            $modelNameArr[$simpleProduct->getId()] = $simpleProductInfo->getModelName();
            
            // $attrColorVarientLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'color_variant');
            // $colorVarient =  $attrColorVarientLabel->getSource()->getOptionText($simpleProductInfo->getColorVariant());

            // $proUrlColorVarientArr[$simpleProduct->getId()] = $simpleProductInfo->getColorVariant();

            // $proUrlLensWidthArr[$simpleProduct->getId()] = $simpleProductInfo->getLensWidth();

            // if(!empty($maxFinalSaleDiscoutArr) && isset($maxFinalSaleDiscoutArr[$simpleProductInfo->getColorVariant()]))
            // {
            //     foreach ($maxFinalSaleDiscoutArr[$simpleProductInfo->getColorVariant()] as $key => $value) {
            //         $simpleProduct = $this->productFactory->create()->load($value);
            //         $orgprice = $simpleProduct->getPrice();
            //         $specialprice = $simpleProduct->getSpecialPrice();
            //         $saleAmt = round($orgprice - $specialprice);
            //         $discountAmt =  round($saleAmt * 100 / $orgprice);
            //         $maxDiscount[$discountAmt] = $simpleProduct->getId();
            //         $maxProArr[$simpleProduct->getId()] = $discountAmt;

            //     }
                
            //     $maxValue = max(array_keys($maxDiscount));
            //     $minValue = min(array_keys($maxProArr));
            //     $maxValuePro = $maxDiscount[$maxValue];

            //     $simpleProductMax = $this->productFactory->create()->load($maxValuePro);

            //     $proUrlArr[$minValue] = $configProduct->getProductUrl().'#?color_variant='.$simpleProductMax->getColorVariant().'&lens_width='.$simpleProductMax->getLensWidth();
            // }
            // else
            // {
            //     $proUrlArr[$simpleProduct->getId()] = $configProduct->getProductUrl().'#?color_variant='.$simpleProductInfo->getColorVariant().'&lens_width='.$simpleProductInfo->getLensWidth();
            // }
           
            $proUrlArr[$simpleProduct->getId()] = $configProduct->getProductUrl().'#?color_variant='.$simpleProductInfo->getColorVariant().'&lens_width='.$simpleProductInfo->getLensWidth();


           $proDiscountPrice = '';
            if($simpleProductInfo->getSpecialPrice())
            {
                $orgprice = $simpleProductInfo->getPrice();
                $specialprice = $simpleProductInfo->getSpecialPrice();
                $saleAmt = round($orgprice - $specialprice);
                $discountAmt =  round($saleAmt * 100 / $orgprice);

                $proDiscountPrice = '<div class="discount_amount_tag"><div class="discount-amt"><span class="discount-per">'.$discountAmt.'%<span> off</div></div>';
            }

            $proDiscountPriceArr[$simpleProduct->getId()] = $proDiscountPrice;

            $polarized = '';
            if($simpleProductInfo->getPolarization())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'polarization');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getPolarization());
                
                if($optionlabel == 'Polarized')
                {
                    $polarized = '<div class="polarized_tag"><img class="polrized_imageTag" src="'.$this->_storeManager->getStore()->getBaseUrl().'pub/media/wysiwyg/polarized.svg" alt="polarized tag"></div>';
                   // $polarized .= '<div class="polarized_tag">Polarized</div>';
                }
            }

            $polarizedArr[$simpleProduct->getId()] = $polarized;

           $images = $simpleProductInfo->getMediaGalleryImages();
           $countImage = count($images);
           $allImages = array();
             foreach ($images as $image) {
               
                $allImages[] = $this->_imageHelper->init($simpleProductInfo, 'product_page_image_large')->constrainOnly(true)->keepAspectRatio(true)
                    ->keepFrame(true)->setImageFile($image->getFile())->resize(900, 405)->getUrl();
            }

           $imagesArr[$simpleProduct->getId()] = $allImages;
           $countimagesArr[$simpleProduct->getId()] = $countImage;

            $colorVarient = '';
            if($simpleProductInfo->getColorVariant())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'color_variant');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getColorVariant());
                
                $colorVarient = '<div class="pro_color_varient">'.$optionlabel.'</div>';
            }
            $colorVarientArr[$simpleProduct->getId()] = $colorVarient;

            if($simpleProductInfo->getLensWidth())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'lens_width');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getLensWidth());
                $lensSizeVal = '<li class="lens_size_val"><b>Lens Size</b>: '.$optionlabel.' mm</li>';
                $lensSizeDim = $optionlabel.' mm';
            }
            else
            {
                $lensSizeVal = '<li class="lens_size_val"><b>Lens Size</b>: </li>';
                $lensSizeDim = '';
            }
            $lensSizeDimArr[$simpleProduct->getId()] = $lensSizeDim;
            $lensSizeValArr[$simpleProduct->getId()] = $lensSizeVal;

            if($simpleProductInfo->getBridgeSize())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'bridge_size');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getBridgeSize());
                $bridgeSize = '<li class="bridge_size_val"><b>Bridge Size</b>: '.$optionlabel.'</li>';
                $bridgeSizeDim = $optionlabel;
            }
            else
            {
                $bridgeSize = '<li class="bridge_size_val"><b>Bridge Size</b>: </li>';
                $bridgeSizeDim = '';
            }
             $bridgeSizeArr[$simpleProduct->getId()] = $bridgeSize;
             $bridgeSizeDimArr[$simpleProduct->getId()] = $bridgeSizeDim;

            if($simpleProductInfo->getTempleLength())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'temple_length');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getTempleLength());
                $templeLen = '<li class="temple_length_val"><b>Temple Length</b>: '.$optionlabel.'</li>';
                $templeLenDim = $optionlabel;
            }
            else
            {
                $templeLen = '<li class="temple_length_val"><b>Temple Length</b>: </li>';
                $templeLenDim = '';
            }
           $templeLenArr[$simpleProduct->getId()] = $templeLen;
           $templeLenDimArr[$simpleProduct->getId()] = $templeLenDim;
        


            if($simpleProductInfo->getPolarization())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'polarization');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getPolarization());
                
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
                    $polarizedImage = '<hr><div class="polar_name"><span>Polarized Glass</span></div>'.$html_content;
                }
                else
                {
                    $polarizedImage = '';
                }
            }


           $polarizedImageArr[$simpleProduct->getId()] = $polarizedImage;

           $stockItem = $simpleProductInfo->getExtensionAttributes()->getStockItem();
            $stockData = $stockItem->getQty();
            $backorder=$simpleProductInfo->getExtensionAttributes()->getStockItem()->getBackorders();
            if($simpleProductInfo->isSaleable() && $stockData > 0)
            {
            //    $stockStatus = '<div class="stock available pro_status here"><span class="glyphicon glyphicon-ok"></span><span>In stock - Ready to ship <div class="tooltip-custom end">ⓘ<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></div></span></div>';
                $checkInStock = '';
                $stockStatus = '<span class="glyphicon glyphicon-ok"></span><span>In stock - Ready to ship <div class="tooltip-custom end">ⓘ<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></div></span>';

                $stockStatusclass = "available";
            }
            
            else
            {

                $stockStatusclass = "unavailable";

                if($backorder===0){
            //         $stockStatus = '<div class="stock unavailable pro_status here1"><span class="glyphicon glyphicon-remove"></span><span>Out of Stock <div class="tooltip-custom end">ⓘ<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></div></span></div>';
                 $checkInStock = 'product__outofstk';
                 $stockStatus = '<span class="glyphicon glyphicon-remove"></span><span>Out of Stock <div class="tooltip-custom end">ⓘ<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></div></span>';
                }else{
          //      $stockStatus = '<div class="stock unavailable pro_status here12"><span class="glyphicon glyphicon-ok"></span><span>Ships within 3-10 Days <div class="tooltip-custom end">ⓘ<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></div></span></div>';
                $checkInStock = '';
                $stockStatus = '<span class="glyphicon glyphicon-ok"></span><span>Ships within 3-10 Days <div class="tooltip-custom end">ⓘ<span class="tooltiptext">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</span></div></span>';
              }
            }
            
            $stockStatusclassArr[$simpleProduct->getId()] = $stockStatusclass;
            $stockStatusArr[$simpleProduct->getId()] = $stockStatus;
            $checkInStockArr[$simpleProduct->getId()] = $checkInStock;


            if($simpleProductInfo->getDescription())
            {
                $filterContent = $this->filterProvide->create()->getPageFilter()->filter($simpleProductInfo->getDescription());
                $proDesc = '<div class="pro_desc">'.$filterContent.'</div>';

            }else{
                    $mgsbrand = $simpleProductInfo->getMgsBrand();
                    $guaranteeBadgeId = 'productdetails_block_one';
                    
                    switch ($mgsbrand) {
                        case "5540":
                        $guaranteeBadgeId = 'carrera_certified_reseller_badge';
                        break;
                        case "5541":
                        $guaranteeBadgeId = 'fossils_certified_reseller_badge';
                        break;
                        case "5543":
                        $guaranteeBadgeId = 'jimmy_choo_certified_reseller_badge';
                        break;
                        case "5945":
                        $guaranteeBadgeId = 'hugo_certified_reseller_badge';
                        break;
                        case "5542":
                        $guaranteeBadgeId = 'kate_spade_certified_reseller_badge';
                        break;
                        case "5538":
                        $guaranteeBadgeId = 'marc_jacobs_certified_reseller_badge';
                        break;
                        case "5537":
                        $guaranteeBadgeId = 'oakley_certified_reseller_badge';
                        break;
                        case "5535":
                        $guaranteeBadgeId = 'polaroid_certified_reseller_badge';
                        break;
                        case "5539":
                        $guaranteeBadgeId = 'ray_ban_certified_reseller_badge';
                        break;
                        case "5536":
                        $guaranteeBadgeId = 'smith_certified_reseller_badge';
                        break;
                        default:
                        $guaranteeBadgeId = 'productdetails_block_one';
                    }
                $block_content = '';
                $storeId = $this->_storeManager->getStore()->getId();
                $block = $this->_blockFactory->create();
                $block->setStoreId($storeId)->load($guaranteeBadgeId);
                $block_content = $this->_filterProvider->getBlockFilter()->setStoreId($storeId)->filter($block->getContent());
                $proDesc = '<div class="pro_desc">'.$block_content.'</div>';
            }

            $pro_descArr[$simpleProduct->getId()] = $proDesc;

            /* Product details table start*/

            $proInfoTab = '';
            $proInfoTab .= '<table class="proinfo_table" style="width:100%">';
            if($simpleProductInfo->getMgsBrand())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'mgs_brand');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getMgsBrand());
                $proInfoTab .= '<tr class="proinfo_col"><td>Brand</td><td>'.$optionlabel.'</td></tr>';
            }


            if($simpleProductInfo->getModelNumber())
            {
                $proInfoTab .= '<tr class="proinfo_col"> <td>Model Number</td><td>'.$simpleProductInfo->getModelNumber().'</td></tr>';
            }

            if($simpleProductInfo->getUpc())
            {
                $proInfoTab .= '<tr class="proinfo_col"> <td>UPC</td><td>'.$simpleProductInfo->getUpc().'</td></tr>';
            }


            if($simpleProductInfo->getFrameColor())
            {
                $proInfoTab .= '<tr class="proinfo_col"> <td>Frame Color</td><td>'.$simpleProductInfo->getFrameColor().'</td></tr>';
            }

            if($simpleProductInfo->getLensColor())
            {
                $proInfoTab .= '<tr class="proinfo_col"> <td>Lens Color</td><td>'.$simpleProductInfo->getLensColor().'</td></tr>';
            }

            if($simpleProductInfo->getGender())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'gender');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getGender());
                $proInfoTab .= '<tr class="proinfo_col"> <td>Gender</td><td>'.$optionlabel.'</td></tr>';
            }

            if($simpleProductInfo->getLensWidth())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'lens_width');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getLensWidth());
                $proInfoTab .= '<tr class="proinfo_col"> <td>Lens Size</td><td>'.$optionlabel.' mm</td></tr>';
            }

            if($simpleProductInfo->getBridgeSize())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'bridge_size');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getBridgeSize());
                $proInfoTab .= '<tr class="proinfo_col"> <td>Bridge Size</td><td>'.$optionlabel.'</td></tr>';
            }

            if($simpleProductInfo->getTempleLength())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'temple_length');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getTempleLength());
                $proInfoTab .= '<tr class="proinfo_col"> <td>Temple Length</td><td>'.$optionlabel.'</td></tr>';
            }

            if($simpleProductInfo->getLensMaterial())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'lens_material');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getLensMaterial());
                $proInfoTab .= '<tr class="proinfo_col"><td>Lens Material</td><td>'.$optionlabel.'</td></tr>';
            }

            if($simpleProductInfo->getFrontMaterial())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'front_material');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getFrontMaterial());
                $proInfoTab .= '<tr class="proinfo_col"><td>Frame Front Material</td><td>'.$optionlabel.'</td></tr>';
            }

            if($simpleProductInfo->getTempleMaterial())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'temple_material');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getTempleMaterial());
                $proInfoTab .= '<tr class="proinfo_col"> <td>Temple Material</td><td>'.$optionlabel.'</td></tr>';
            }

            if($simpleProductInfo->getLensType())
            {
                $proInfoTab .= '<tr class="proinfo_col"> <td>Lens Type</td><td>'.$simpleProductInfo->getLensType().'</td></tr>';
            }

            if($simpleProductInfo->getHinges())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'hinges');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getHinges());
                $proInfoTab .= '<tr class="proinfo_col"> <td>Hinges</td><td>'.$optionlabel.'</td></tr>';
            }

            if($simpleProductInfo->getRimType())
            {
                $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'rim_type');
                $optionlabel =  $attrLabel->getSource()->getOptionText($simpleProductInfo->getRimType());
                $proInfoTab .= '<tr class="proinfo_col">  <td>Rim</td><td>'.$optionlabel.'</td></tr>';
            }

            $proInfoTab .= '</table>';

            $proInfoTabArr[$simpleProduct->getId()] = $proInfoTab;
           //  /* Product details table end  */




            $proShortDesc = '';
            if($simpleProductInfo->getShortDescription())
            {
                $filterContent = $this->filterProvide->create()->getPageFilter()->filter($simpleProductInfo->getShortDescription());

                $proShortDesc = '<div class="pro_shortdesc">'.$filterContent.'</div>';
            }
             $pro_shortdescArr[$simpleProduct->getId()] = $proShortDesc;

            $proPriceGuarantee = '';
            if($simpleProductInfo->getBestPriceGuarantee())
            {
                $bestPriceGuaranteeImage = $this->_storeManager->getStore()->getBaseUrl().'pub/media/wysiwyg/bestPriceGuarantee.jpeg';
                $proPriceGuarantee = '<div class="price_guarantee"><img src="'.$bestPriceGuaranteeImage.'" alt="Price Guarantee"></div>';
            }

            $proPriceGuaranteeArr[$simpleProduct->getId()] = $proPriceGuarantee;

            if($simpleProductInfo->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
                $proPrice = $simpleProductInfo->getFinalPrice();
                $proFinalPrice = number_format((float)$proPrice/4, 2, '.', '');
                $sezzleEmi = '<span class="sezzle-payment-amount sezzle-button-text">CAD $'.$proFinalPrice.'&nbsp;</span>';
            }
            else
            {
                if($simpleProductInfo->getSpecialPrice())
                {
                    $proPrice = $simpleProductInfo->getSpecialPrice();
                }
                else
                {
                    $proPrice = $simpleProductInfo->getPrice();
                }
                
                $proFinalPrice = number_format((float)$proPrice/4, 2, '.', '');
                $sezzleEmi = '<span class="sezzle-payment-amount sezzle-button-text">CAD $'.$proFinalPrice.'&nbsp;</span>';
            }


            $sezzleEmisArr[$simpleProduct->getId()] = $sezzleEmi;

            
        }

        $config['brandName'] = $brandArr;
        $config['bridgeSize'] = $bridgeSizeArr;
        $config['bridgeSizeDim'] = $bridgeSizeDimArr;
        $config['colorVarient'] = $colorVarientArr;
        $config['proDiscountPrice'] = $proDiscountPriceArr;
        $config['proPrice'] = $proPriceArr;
        $config['proModelName'] = $proModelNameArr;
        $config['modelName'] = $modelNameArr;
        $config['zgallery'] = $imagesArr;
        $config['zcountGallery'] = $countimagesArr;
        $config['polarized'] = $polarizedArr;
        $config['lensSizeDim'] = $lensSizeDimArr;
        $config['lensSizeVal'] = $lensSizeValArr;
        $config['templeLenDim'] = $templeLenDimArr;
        $config['templeLen'] = $templeLenArr;
        $config['polarizedImage'] = $polarizedImageArr;
        $config['stockStatus'] = $stockStatusArr;
        $config['stockStatusclass'] = $stockStatusclassArr;
        $config['pro_desc'] = $pro_descArr;
        $config['proUrl'] = $proUrlArr;
        $config['sezzleEmi'] = $sezzleEmisArr;
        $config['pro_shortdesc'] = $pro_shortdescArr;
        $config['proPriceGuarantee'] = $proPriceGuaranteeArr;
        $config['proinfo_table'] = $proInfoTabArr;
        $config['checkInStock'] = $checkInStockArr;
        $config['lens'] = $attributesLens;           
        $config['frames'] = $attributesFrame; 
        return $this->jsonEncoder->encode($config);
    }
}