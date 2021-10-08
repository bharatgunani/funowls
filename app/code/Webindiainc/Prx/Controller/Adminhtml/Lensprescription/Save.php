<?php

namespace Webindiainc\Prx\Controller\Adminhtml\Lensprescription;

class Save extends \Magento\Backend\App\Action
{

    var $modelFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webindiainc\Prx\Model\LensprescriptionFactory $modelFactory
    ) {
        parent::__construct($context);
        $this->modelFactory = $modelFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('prx/lensprescription/addrow');
            return;
        }
        try {
            $rowData = $this->modelFactory->create();
            $rowData->setData($data);
            if (isset($data['id'])) {
                $rowData->setLensprescriptionId($data['id']);
            }
            $rowData->save();
            $this->messageManager->addSuccess(__('Lensprescription has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
		
		// Check if 'Save and Continue'
		if ($this->getRequest()->getParam('back')) {
			$this->_redirect('*/*/addrow', ['id' => $rowData->getId(), '_current' => true]);
			return;
		}
        $this->_redirect('prx/lensprescription/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webindiainc_Prx::save');
    }
}
