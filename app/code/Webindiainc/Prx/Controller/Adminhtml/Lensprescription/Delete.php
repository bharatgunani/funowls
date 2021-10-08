<?php

namespace Webindiainc\Prx\Controller\Adminhtml\Lensprescription;

use Magento\Backend\App\Action\Context;

class Delete extends \Magento\Backend\App\Action
{

    protected $modelFactory;

    public function __construct(
        Context $context,
        \Webindiainc\Prx\Model\LensprescriptionFactory $modelFactory
    ) {
        $this->modelFactory = $modelFactory;
        parent::__construct($context);
    }

    public function execute() {
		
		$id = $this->getRequest()->getParam('id');
        try {
            $model = $this->modelFactory->create();
            $model->load($id);
            $model->delete();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        $this->messageManager->addSuccess(__('Record has been deleted.'));
		$this->_redirect('*/*/');
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Webindiainc_Prx::row_data_delete');
    }
}
