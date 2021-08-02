<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Sparx\Overridedefault\Block\Products;

use Magento\Framework\Stdlib\DateTime\DateTime;
/**
 * Main contact form block
 */
class Deals extends \MGS\Mpanel\Block\Products\Deals
{
	/**
     * Product collection initialize process
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getProductCollection($category)
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		if($category->getId()){
			$categoryIdArray = [$category->getId()];
			$categoryFilter = ['eq'=>$categoryIdArray];
			$collection->addCategoriesFilter($categoryFilter);
		}

        $collection = $this->_addProductAttributesAndPrices($collection)
			->addAttributeToSelect(['image', 'name', 'short_description','best_price_guarantee'])
            ->addStoreFilter()
			->addFinalPrice()
            ->addAttributeToSort('created_at', 'desc')
			//->addAttributeToFilter('special_to_date', ['notnull'=>true]);
            ->addAttributeToFilter('is_deal_product', 1);
		
		//$collection->getSelect()->where('price_index.final_price < price_index.price');

        $collection->setPageSize($this->getProductsCount())
            ->setCurPage($this->getCurrentPage());
        return $collection;
    }
	
	public function getDealsByCategories($categoryIds){
		$filterAttribute = $this->getAttribute();

		if($filterAttribute == "bestseller"){
			return $this->getBestSellerCollection();
		}else{
			$collection = $this->_productCollectionFactory->create();
	        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
			
			if($categoryIds!=''){
				$categoryIdArray = explode(',',$categoryIds);
				if(count($categoryIdArray)>0){
					$categoryFilter = ['eq'=>$categoryIdArray];
					$collection->addCategoriesFilter($categoryFilter);
				}
			}
			
	        $collection = $this->_addProductAttributesAndPrices($collection)
				->addAttributeToSelect(['image', 'name', 'short_description',$filterAttribute])
	            ->addStoreFilter()
				->addFinalPrice()
	            ->addAttributeToSort('created_at', 'desc')
				//->addAttributeToFilter('special_to_date', ['notnull'=>true]);
	            ->addAttributeToFilter($filterAttribute, 1);
			
			//$collection->getSelect()->where('price_index.final_price < price_index.price');

	        $collection->setPageSize($this->getProductsCount())
	            ->setCurPage($this->getCurrentPage());
	        return $collection;	
		}
		
	}

	public function getBestSellerCollection(){
		$collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$productCollection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory');
		$bestSellers = $productCollection->create()->setModel('Magento\Catalog\Model\Product')->setPeriod('yearly');
		//$bestSellers->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		$configurable = $objectManager->create('\Magento\ConfigurableProduct\Model\Product\Type\Configurable');
		

		foreach ($bestSellers as $product) {
            if($product->isSaleable()){
                if(!empty($configurable->getParentIdsByChild($product->getProductId()))){
                    $productIds[] = $configurable->getParentIdsByChild($product->getProductId())[0];  
                }else{
                    $productIds[] = $product->getProductId();    
                }    
            }
        }
        $finalProductIds = array_unique($productIds);

        $collection = $this->_addProductAttributesAndPrices($collection)
			->addAttributeToSelect(['entity_id','image', 'name', 'short_description','is_trending_product'])
            ->addStoreFilter()
			->addFinalPrice()
            ->addAttributeToSort('created_at', 'desc')
			->addAttributeToFilter('entity_id', ['in'=>$finalProductIds]);
		
		//$collection->getSelect()->where('price_index.final_price < price_index.price');

        $collection->setPageSize($this->getProductsCount())
            ->setCurPage($this->getCurrentPage());
        return $collection;
	}

	public function getTrendingProductCollection($categoryIds){
		$collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		if($categoryIds!=''){
			$categoryIdArray = explode(',',$categoryIds);
			if(count($categoryIdArray)>0){
				$categoryFilter = ['eq'=>$categoryIdArray];
				$collection->addCategoriesFilter($categoryFilter);
			}
		}

        $collection = $this->_addProductAttributesAndPrices($collection)
			->addAttributeToSelect(['image', 'name', 'short_description','is_trending_product'])
            ->addStoreFilter()
			->addFinalPrice()
            ->addAttributeToSort('created_at', 'desc')
			//->addAttributeToFilter('special_to_date', ['notnull'=>true]);
            ->addAttributeToFilter('is_trending_product', 1);
		
		//$collection->getSelect()->where('price_index.final_price < price_index.price');

        $collection->setPageSize($this->getProductsCount())
            ->setCurPage($this->getCurrentPage());
        return $collection;
	}
}

