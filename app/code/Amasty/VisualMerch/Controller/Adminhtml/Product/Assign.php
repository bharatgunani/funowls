<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Controller\Adminhtml\Product;

class Assign extends ControllerAbstract
{
    /**
     * @return $this
     */
    public function execute()
    {
        $this->initCategory();
        $block = $this->layoutFactory->create()->createBlock(
            \Amasty\VisualMerch\Block\Adminhtml\Products\AssignProducts::class,
            'product.listing'
        );

        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $block->toHtml()
        );
    }
}
