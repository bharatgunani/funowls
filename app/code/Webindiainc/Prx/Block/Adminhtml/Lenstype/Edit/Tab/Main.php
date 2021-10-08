<?php
namespace Webindiainc\Prx\Block\Adminhtml\Lenstype\Edit\Tab;

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
		
        if ($model->getLenstypeId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Lenstype Data'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('lenstype_id', 'hidden', ['name' => 'lenstype_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Lenstype Data'), 'class' => 'fieldset-wide']
            );
        }
		
		$fieldset->addField(
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
        );

		if (!$model->getLenstypeId()) {
			$fieldset->addField(
				'lenstype_identifier',
				'text',
				[
					'name' => 'lenstype_identifier',
					'label' => __('Identifier'),
					'id' => 'lenstype_identifier',
					'title' => __('Identifier'),
					'class' => 'required-entry',
					'required' => true,
				]
			);
		} else {
			//disabled identifier when Edit data
			$fieldset->addField(
				'lenstype_identifier',
				'text',
				[
					'name' => 'lenstype_identifier',
					'label' => __('Identifier'),
					'id' => 'lenstype_identifier',
					'title' => __('Identifier'),
					'class' => 'required-entry',
					'required' => true,
					'disabled' => true,
				]
			);
		}
		
		$fieldset->addField(
            'lenstype_position',
            'text',
            [
                'name' => 'lenstype_position',
                'label' => __('Position'),
                'id' => 'lenstype_position',
                'title' => __('Position'),
                /* 'class' => 'required-entry', */
                'required' => true,
            ]
        );
		
        $fieldset->addField(
            'lenstype_title',
            'text',
            [
                'name' => 'lenstype_title',
                'label' => __('Title'),
                'id' => 'lenstype_title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
		
		$fieldset->addField(
            'lenstype_subtitle',
            'text',
            [
                'name' => 'lenstype_subtitle',
                'label' => __('Sub-Title'),
                'id' => 'lenstype_subtitle',
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
            'lenstype_price',
            'text',
            [
                'name' => 'lenstype_price',
                'label' => __('Price'),
                'id' => 'lenstype_price',
                'title' => __('Price'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lenstype_content',
            'textarea',
            [
                'name' => 'lenstype_content',
                'label' => __('Content'),
                'id' => 'lenstype_content',
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
            'lenstype_status',
            'select',
            [
                'name' => 'lenstype_status',
                'label' => __('Status'),
                'id' => 'lenstype_status',
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
