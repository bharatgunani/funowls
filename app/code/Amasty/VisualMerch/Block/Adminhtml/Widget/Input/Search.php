<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Block\Adminhtml\Widget\Input;

class Search extends \Magento\Backend\Block\Widget
{
    /**
     * Define block template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Amasty_VisualMerchUi::widget/input.phtml');
    }
}
