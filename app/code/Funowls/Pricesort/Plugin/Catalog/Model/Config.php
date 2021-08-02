<?php
namespace Funowls\Pricesort\Plugin\Catalog\Model;
class Config
{
	/**
     * Adding custom options and changing labels
     *
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param [] $options
     * @return []
     */
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig,
    $options) 
    {
    	unset($options['price']);
    	unset($options['position']);
        $options['price_asc'] = __('Price - Low To High');
        $options['price_desc'] = __('Price - High To Low');
        $options['newest'] = __('By Latest');
        $options['bestseller'] = __('Best Seller');
        
        return array_reverse($options);
    }
}