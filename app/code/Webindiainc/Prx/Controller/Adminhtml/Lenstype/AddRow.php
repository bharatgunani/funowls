<?php

namespace Webindiainc\Prx\Controller\Adminhtml\Lenstype;

use Magento\Framework\Controller\ResultFactory;

class AddRow extends \Magento\Backend\App\Action
{
    private $coreRegistry;
    private $lenstypeFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Webindiainc\Prx\Model\LenstypeFactory $lenstypeFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->lenstypeFactory = $lenstypeFactory;
    }

    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->lenstypeFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getLenstypeId()) {
                $this->messageManager->addError(__('row data no longer exist.'));
                $this->_redirect('prx/lenstype/rowdata');
                return;
            }
        }

        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Lens Type Data ').$rowTitle : __('Add Lens Type Data');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webindiainc_Prx::add_row');
    }
}
