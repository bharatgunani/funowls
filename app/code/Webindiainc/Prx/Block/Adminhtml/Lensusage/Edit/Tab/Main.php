<?php
namespace Webindiainc\Prx\Block\Adminhtml\Lensusage\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $store;
	protected $helper;
	protected $_options;
	protected $_wysiwygConfig;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Webindiainc\Prx\Helper\Data $helper,
        \Webindiainc\Prx\Model\Status $_options,
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_options = $_options;
		$this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('row_data');

        $form = $this->_formFactory->create();
		$form->setHtmlIdPrefix('prx_');
		
        if ($model->getLensusageId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Lensusage Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('lensusage_id', 'hidden', ['name' => 'lensusage_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Lensusage Data'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'lensusage_title',
            'text',
            [
                'name' => 'lensusage_title',
                'label' => __('Title'),
                'id' => 'lensusage_title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'lensusage_subtitle',
            'text',
            [
                'name' => 'lensusage_subtitle',
                'label' => __('Sub-Title'),
                'id' => 'lensusage_subtitle',
                'title' => __('Sub-Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'image',
            'image',
            array(
                'name' => 'image',
                'label' => __('Image'),
				'class' => 'file image',
                'title' => __('Image'),
            )
        );
		
		 $fieldset->addField(
            'lensusage_has_prescription',
            'select',
            [
                'name' => 'lensusage_has_prescription',
                'label' => __('Is Prescription'),
                'id' => 'lensusage_has_prescription',
                'title' => __('Status'),
                'values' => $this->_options->getOptionArray(),
                'class' => 'status',
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'lensusage_price',
            'text',
            [
                'name' => 'lensusage_price',
                'label' => __('Price'),
                'id' => 'lensusage_price',
                'title' => __('Price'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lensusage_content',
            'textarea',
            [
                'name' => 'lensusage_content',
                'label' => __('Content'),
                'id' => 'lensusage_content',
                'title' => __('Content'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		$fieldset->addField('youtube_content', 'editor', [
		   'name'      => 'youtube_content',
		   'label'   => 'Youtube Content',
		   'config'    => $this->_wysiwygConfig->getConfig(),
		   'wysiwyg'   => true,
		   'required'  => false,
		   //'after_element_html' => '<small>YOURCOMMENT.</small>',
		 ]);

        $fieldset->addField(
            'lensusage_status',
            'select',
            [
                'name' => 'lensusage_status',
                'label' => __('Status'),
                'id' => 'lensusage_status',
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
