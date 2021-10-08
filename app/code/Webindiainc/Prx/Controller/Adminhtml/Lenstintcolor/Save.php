<?php

namespace Webindiainc\Prx\Controller\Adminhtml\Lenstintcolor;

class Save extends \Magento\Backend\App\Action
{

    var $modelFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webindiainc\Prx\Model\LenstintcolorFactory $modelFactory
    ) {
        parent::__construct($context);
        $this->modelFactory = $modelFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
		if( isset($data['products']) && $data['products']!='' ) {
			$data['lenstintcolor_products'] = str_replace('&', ',', $data['products']);
		}
		
		if( isset($data['lenstype_id']) && $data['lenstype_id'] != '' )  {
			$arr = $data['lenstype_id'];
			if(is_array($data['lenstype_id'])) {
				$data['lenstype_id'] = ',' . implode(",",$arr) . ',';
			}
		}
		
        if (!$data) {
            $this->_redirect('prx/lenstintcolor/addrow');
            return;
        }
        try {
            $rowData = $this->modelFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setLenstintcolorId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Lenstintcolor has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
		
		// Check if 'Save and Continue'
		if ($this->getRequest()->getParam('back')) {
			$this->_redirect('*/*/addrow', ['id' => $rowData->getId(), '_current' => true]);
			return;
		}
        $this->_redirect('prx/lenstintcolor/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webindiainc_Prx::save');
    }
}
