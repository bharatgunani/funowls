<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Controller\Adminhtml\Product;

class Remove extends ControllerAbstract
{
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $this->initCategory();

        $removeProductData = $this->getRequest()->getParam('remove_product_data', []);
        if (!empty($removeProductData)) {
            $this->dataProvider->resortPositionData($removeProductData['source_position']);
            $this->dataProvider->unsetCategoryProductId($removeProductData['entity_id']);
            $this->dataProvider->unsetProductPositionData($removeProductData['entity_id']);
        }

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData([]);
        return $resultJson;
    }
}
