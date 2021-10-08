<?php

namespace Webindiainc\Prx\Block\Adminhtml\Lenstintcolor\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lenstintcolor_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Lenstintcolor Information'));
    }
}
