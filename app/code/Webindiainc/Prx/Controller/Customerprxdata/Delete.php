<?php

namespace Webindiainc\Prx\Controller\Customerprxdata;

use Webindiainc\Prx\Model\CustomerPrxDataFactory;

class Delete extends \Magento\Framework\App\Action\Action {

    protected $customerprxdataFactory;
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        CustomerPrxDataFactory $customerprxdataFactory
    ) {
        $this->customerprxdataFactory = $customerprxdataFactory;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        try {
            $model = $this->customerprxdataFactory->create();
            $model->load($id);
            $model->delete();
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }

}