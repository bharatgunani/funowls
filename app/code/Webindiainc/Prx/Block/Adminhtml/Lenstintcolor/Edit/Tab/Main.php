<?php
namespace Webindiainc\Prx\Block\Adminhtml\Lenstintcolor\Edit\Tab;

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
		
        if ($model->getLenstintcolorId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Lenstintcolor Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('lenstintcolor_id', 'hidden', ['name' => 'lenstintcolor_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Lens Tintcolor Data'), 'class' => 'fieldset-wide']
            );
        }
		
		/* $fieldset->addField(
            'lensusage_id',
            'select',
            [
                'name' => 'lensusage_id',
                'label' => __('Select Lensusage'),
                'id' => 'lensusage_id',
                'title' => __('Select Lensusage'),
                'values' => $this->helper->getLensusageIds(),
                'class' => '',
                'required' => false,
            ]
        );*/
		
		$fieldset->addField(
            'lenstype_id',
            'multiselect',
            [
                'name' => 'lenstype_id',
                'label' => __('Select Lenstype'),
                'id' => 'lenstype_id',
                'title' => __('Select Lenstype'),
                'values' => $this->helper->getLenstypeIds(),
                'class' => '',
                'required' => false,
            ]
        );

		/*$fieldset->addField(
            'lensthickness_id',
            'select',
            [
                'name' => 'lensthickness_id',
                'label' => __('Select Lensthickness'),
                'id' => 'lensthickness_id',
                'title' => __('Select Lensthickness'),
                'values' => $this->helper->getLensthicknessIds(),
                'class' => '',
                'required' => false,
            ]
        );*/
		
        $fieldset->addField(
            'lenstintcolor_title',
            'text',
            [
                'name' => 'lenstintcolor_title',
                'label' => __('Title'),
                'id' => 'lenstintcolor_title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'lenstintcolor_subtitle',
            'text',
            [
                'name' => 'lenstintcolor_subtitle',
                'label' => __('Sub-Title'),
                'id' => 'lenstintcolor_subtitle',
                'title' => __('Sub-Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		
		//Replace your database field name with "color_code" 
		$field = $fieldset->addField(
			   'color_code',
			   'text',
			   [
				  'name' => 'color_code',
				  'label' => __('Color Code'),
				  'title' => __('Color Code')
				]
		 );
		 $renderer = $this->getLayout()->createBlock('Webindiainc\Prx\Block\Adminhtml\Color');
		 $field->setRenderer($renderer);
 
		/*$fieldset->addField(
            'lenstintcolor_price',
            'text',
            [
                'name' => 'lenstintcolor_price',
                'label' => __('Price'),
                'id' => 'lenstintcolor_price',
                'title' => __('Price'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );*/

        $fieldset->addField(
            'lenstintcolor_content',
            'textarea',
            [
                'name' => 'lenstintcolor_content',
                'label' => __('Content'),
                'id' => 'lenstintcolor_content',
                'title' => __('Content'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lenstintcolor_status',
            'select',
            [
                'name' => 'lenstintcolor_status',
                'label' => __('Status'),
                'id' => 'lenstintcolor_status',
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
