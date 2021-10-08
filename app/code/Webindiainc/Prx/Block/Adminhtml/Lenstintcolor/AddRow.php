<?php

namespace Webindiainc\Prx\Block\Adminhtml\Lenstintcolor;

class AddRow extends \Magento\Backend\Block\Widget\Form\Container
{

    protected $_coreRegistry = null;
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'lenstintcolor_id';
        $this->_blockGroup = 'Webindiainc_Prx';
        $this->_controller = 'adminhtml_lenstintcolor';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Lenstintcolor'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            -100
        );
		
		$model = $this->_coreRegistry->registry('row_data');
			$id = $model->getLenstintcolorId();
			if($id) {
				$this->buttonList->add(
					'delete',
					[
						'label' => __('Delete'),
						'class' => ' delete primary ',
						'onclick'   => 'deleteConfirm(\''. __('Are you sure you want to do this?') . '\', \'' . $this->getDeleteRowUrl($id) . '\')',
						'data_attribute' => [
							'sort_order' => 20,
						]
					]
				);
			}
			
    }
	
	public function getDeleteRowUrl($id) {
		return $this->getUrl('*/*/delete', ['id' => $id]);
    }

    protected function _isAllowedAction($resourceId) {
        return $this->_authorization->isAllowed($resourceId);
    }

    protected function _getSaveAndContinueUrl() {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }

}
