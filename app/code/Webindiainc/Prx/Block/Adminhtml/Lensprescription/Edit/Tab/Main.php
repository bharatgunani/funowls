<?php
namespace Webindiainc\Prx\Block\Adminhtml\Lensprescription\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $store;
	protected $helper;
	protected $_options;

    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Webindiainc\Prx\Helper\Data $helper,
        \Webindiainc\Prx\Model\Status $_options,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_options = $_options;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('row_data');

        $form = $this->_formFactory->create();
		$form->setHtmlIdPrefix('prx_');
		
        if ($model->getLensprescriptionId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Lensprescription Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('lensprescription_id', 'hidden', ['name' => 'lensprescription_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Lensprescription Data'), 'class' => 'fieldset-wide']
            );
        }
		
		$fieldset->addField(
            'lensprescription_identifier',
            'text',
            [
                'name' => 'lensprescription_identifier',
                'label' => __('Identifier'),
                'id' => 'lensprescription_identifier',
                'title' => __('Identifier'),
                'class' => 'required-entry',
                'required' => true,
				'note' => 'do not change identifier value'
            ]
        );
		
		$fieldset->addField(
            'lensprescription_title',
            'text',
            [
                'name' => 'lensprescription_title',
                'label' => __('Title'),
                'id' => 'lensprescription_title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'lensprescription_subtitle',
            'text',
            [
                'name' => 'lensprescription_subtitle',
                'label' => __('Sub-Title'),
                'id' => 'lensprescription_subtitle',
                'title' => __('Sub-Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
        $fieldset->addField(
            'lensprescription_content',
            'textarea',
            [
                'name' => 'lensprescription_content',
                'label' => __('Content'),
                'id' => 'lensprescription_content',
                'title' => __('Content'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lensprescription_status',
            'select',
            [
                'name' => 'lensprescription_status',
                'label' => __('Status'),
                'id' => 'lensprescription_status',
                'title' => __('Status'),
                'values' => $this->_options->getOptionArray(),
                'class' => 'status',
                'required' => true,
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel() {
        return __('Main');
    }

    public function getTabTitle() {
        return __('Main');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        return false;
    }

    protected function _isAllowedAction($resourceId) {
        return $this->_authorization->isAllowed($resourceId);
    }
}
