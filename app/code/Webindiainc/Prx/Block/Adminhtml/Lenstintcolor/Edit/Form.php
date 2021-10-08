<?php

namespace Webindiainc\Prx\Block\Adminhtml\Lenstintcolor\Edit;

/**
 * Adminhtml attachment edit form block
 *
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{

    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
