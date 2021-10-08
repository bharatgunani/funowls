<?php
namespace Webindiainc\Prx\Plugin\Quote;

use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
use Magento\Framework\Serialize\Serializer\Json;

class ToOrderItem
{    
    public function __construct(Json $serializer = null)
    {        
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
    }

    public function aroundConvert(QuoteToOrderItem $subject,
        \Closure $proceed,
        $item,
        $data = []
    ) {
        // Get Order Item
        $orderItem = $proceed($item, $data);               

        $additionalOptions = $item->getOptionByCode('additional_options');
        // Check if there is any additional options in Quote Item
        if($additionalOptions){
            //if (count($additionalOptions) > 0) {
                // Get Order Item's other options
                $options = $orderItem->getProductOptions();
                // Set additional options to Order Item
                $options['additional_options'] = $this->serializer->unserialize($additionalOptions->getValue());
                $orderItem->setPrxdataAttribute($item->getPrxdataAttribute());
                $orderItem->setProductOptions($options);
            //}    
        }
        return $orderItem;
    }
}