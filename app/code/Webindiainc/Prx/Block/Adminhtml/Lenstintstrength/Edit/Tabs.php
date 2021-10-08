<?php

namespace Webindiainc\Prx\Block\Adminhtml\Lenstintstrength\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lenstintstrength_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Lenstintstrength Information'));
    }
}
