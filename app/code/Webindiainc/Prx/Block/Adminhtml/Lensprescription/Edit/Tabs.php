<?php

namespace Webindiainc\Prx\Block\Adminhtml\Lensprescription\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lensprescription_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Lensprescription Information'));
    }
}
