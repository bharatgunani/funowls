<?php
namespace Webindiainc\Prx\Block\Adminhtml\Lensthickness\Edit\Tab;

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
		
        if ($model->getLensthicknessId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Lensthickness Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('lensthickness_id', 'hidden', ['name' => 'lensthickness_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Lens Type Data'), 'class' => 'fieldset-wide']
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
                'required' => true,
            ]
        ); */
		
		$fieldset->addField(
            'lenstype_id',
            'select',
            [
                'name' => 'lenstype_id',
                'label' => __('Select Lenstype'),
                'id' => 'lenstype_id',
                'title' => __('Select Lenstype'),
                'values' => $this->helper->getLenstypeIds(),
                'class' => '',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lensthickness_title',
            'text',
            [
                'name' => 'lensthickness_title',
                'label' => __('Title'),
                'id' => 'lensthickness_title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'lensthickness_subtitle',
            'text',
            [
                'name' => 'lensthickness_subtitle',
                'label' => __('Sub-Title'),
                'id' => 'lensthickness_subtitle',
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
            'lensthickness_price',
            'text',
            [
                'name' => 'lensthickness_price',
                'label' => __('Price'),
                'id' => 'lensthickness_price',
                'title' => __('Price'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lensthickness_content',
            'textarea',
            [
                'name' => 'lensthickness_content',
                'label' => __('Content'),
                'id' => 'lensthickness_content',
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
            'lensthickness_status',
            'select',
            [
                'name' => 'lensthickness_status',
                'label' => __('Status'),
                'id' => 'lensthickness_status',
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
