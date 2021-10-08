<?php
namespace Webindiainc\Prx\Block\Adminhtml\Lenstintstrength\Edit\Tab;

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
		
        if ($model->getLensLenstintstrengthId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Lenstintstrength Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('lenstintstrength_id', 'hidden', ['name' => 'lenstintstrength_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Lens Tintstrength Data'), 'class' => 'fieldset-wide']
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
            'lenstintstrength_title',
            'text',
            [
                'name' => 'lenstintstrength_title',
                'label' => __('Title'),
                'id' => 'lenstintstrength_title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'lenstintstrength_subtitle',
            'text',
            [
                'name' => 'lenstintstrength_subtitle',
                'label' => __('Sub-Title'),
                'id' => 'lenstintstrength_subtitle',
                'title' => __('Sub-Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		/*$fieldset->addField(
            'lenstintstrength_price',
            'text',
            [
                'name' => 'lenstintstrength_price',
                'label' => __('Price'),
                'id' => 'lenstintstrength_price',
                'title' => __('Price'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );*/

        $fieldset->addField(
            'lenstintstrength_content',
            'textarea',
            [
                'name' => 'lenstintstrength_content',
                'label' => __('Content'),
                'id' => 'lenstintstrength_content',
                'title' => __('Content'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lenstintstrength_status',
            'select',
            [
                'name' => 'lenstintstrength_status',
                'label' => __('Status'),
                'id' => 'lenstintstrength_status',
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
