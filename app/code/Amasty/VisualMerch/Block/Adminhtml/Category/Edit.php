<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Block\Adminhtml\Category;

class Edit extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Store\Api\Data\StoreInterface
     */
    private $defaultStore;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->defaultStore = current($context->getStoreManager()->getStores());
        $this->setTemplate('Amasty_VisualMerch::category/form.phtml');
    }

    /**
     * @return int
     */
    public function getIsCategoryDynamic()
    {
        $category = $this->registry->registry('current_category');
        return (int)($category && $category->getData('amlanding_is_dynamic'));
    }

    /**
     * @return string
     */
    public function getChangeDisplayModeUrl()
    {
        $storeId = (int)$this->_request->getParam('store', $this->defaultStore->getId());
        $params = ['store_id' => $storeId];
        if($category = $this->registry->registry('current_category')) {
            $params['entity_id'] = $category->getId();
        }
        return $this->getUrl('amasty_visual_merch/product/mode', $params);
    }
}
