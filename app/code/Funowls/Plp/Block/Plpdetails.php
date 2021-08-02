<?php

namespace Funowls\Plp\Block;

use Magento\Catalog\Model\ProductFactory;
use Magento\Eav\Model\ConfigFactory;

class Plpdetails extends \Magento\Framework\View\Element\Template
{
    protected $productFactory;
    protected $eavConfigFactory;

    public function __construct(ProductFactory $productFactory, ConfigFactory $eavConfigFactory)
    {
        $this->productFactory = $productFactory;
        $this->eavConfigFactory = $eavConfigFactory;
    }

    public function getIsSpecialPrice($productId)
    {
        $configProduct = $this->productFactory->create()->load($productId);
        if($configProduct->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
            $maxDiscount = array();
            foreach ($_children as $child)
            {
                $simpleProduct = $this->productFactory->create()->load($child->getId());
                if($simpleProduct->getSpecialPrice())
                {
                    $orgprice = $simpleProduct->getPrice();
                    $specialprice = $simpleProduct->getSpecialPrice();
                    $saleAmt = round($orgprice - $specialprice);
                    $discountAmt =  round($saleAmt * 100 / $orgprice);
                    $maxDiscount[$discountAmt] = $discountAmt;
                }
            }
            if(!empty($maxDiscount))
            {
                $maxValue = 'upto '.(int)max(array_keys($maxDiscount)).'% off';
               
                return $maxValue;
            }
        }
    }

    public function getIsSpecialPrice_custom($productId)
    {
        
        $configProduct = $this->productFactory->create()->load($productId);
        if($configProduct->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
            $maxDiscount = array();
            $maxDiscount_int = array();

            foreach ($_children as $child)
            {
                $simpleProduct = $this->productFactory->create()->load($child->getId());
                if($simpleProduct->getSpecialPrice())
                {
                    $orgprice = $simpleProduct->getPrice();
                    $specialprice = $simpleProduct->getSpecialPrice();
                    $saleAmt = round($orgprice - $specialprice);
                    $discountAmt =  round($saleAmt * 100 / $orgprice);

                    $maxDiscount[$discountAmt] = $discountAmt;
                    $maxDiscount_int[$discountAmt] = (int)$discountAmt;
                   
                }
            }
            
             if(!empty($maxDiscount_int))
            {
               $maxValue_int = max(array_keys($maxDiscount_int));
               
                return $maxValue_int;
            }
        }
    }

    public function getBestPriceGuarantee($productId)
    {
        $configProduct = $this->productFactory->create()->load($productId);
        if($configProduct->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
            $bestPriceGuarantee = false;
            foreach ($_children as $child)
            {
                $simpleProduct = $this->productFactory->create()->load($child->getId());
                if($simpleProduct->getBestPriceGuarantee())
                {
                    $bestPriceGuarantee = true;
                }
            }
            if($bestPriceGuarantee)
            {
                return $bestPriceGuarantee;
            }
        }
    }

    public function countAvailableCount($productId)
    {
        $configProduct = $this->productFactory->create()->load($productId);
        if($configProduct->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $_children = $configProduct->getTypeInstance()->getUsedProducts($configProduct);
         //   $availableColor = count($_children);
            $totalColors = array();
            foreach ($_children as $child)
            {
                $simpleProduct = $this->productFactory->create()->load($child->getId());
                if($simpleProduct->getData('color_variant'))
                {
                    $totalColors[] = $simpleProduct->getData('color_variant');
                }
            } 
            $colors = array_unique($totalColors);
            $availableColor = count($colors);
            return $availableColor;
        }
    }

    public function getConfBrandName($productId)
    {
        $configProduct = $this->productFactory->create()->load($productId);
        $attrLabel = $this->eavConfigFactory->create()->getAttribute('catalog_product', 'mgs_brand');
        $brandName =  $attrLabel->getSource()->getOptionText($configProduct->getMgsBrand());
        return $brandName;
    }

    public function getConfModelName($productId)
    {
        $configProduct = $this->productFactory->create()->load($productId);
        $modelName = $configProduct->getModelName();
        return $modelName;
    }

    public function getConfPriceName($productId)
    {
        $configProduct = $this->productFactory->create()->load($productId);
        $pricePro = $configProduct->getPrice();
        return $pricePro;
    }

    public function loadProductById($productId)
    {
        return $this->productFactory->create()->load($productId);
    }
}