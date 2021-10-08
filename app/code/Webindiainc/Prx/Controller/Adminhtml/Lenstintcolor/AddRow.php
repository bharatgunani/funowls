<?php

namespace Webindiainc\Prx\Controller\Adminhtml\Lenstintcolor;

use Magento\Framework\Controller\ResultFactory;

class AddRow extends \Magento\Backend\App\Action
{

    private $coreRegistry;
    private $modelFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Webindiainc\Prx\Model\LenstintcolorFactory $modelFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->modelFactory = $modelFactory;
    }

    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $rowData = $this->modelFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
            $rowData = $rowData->load($rowId);
            $rowTitle = $rowData->getTitle();
            if (!$rowData->getLenstintcolorId()) {
                $this->messageManager->addError(__('Lenstintcolor data no longer exist.'));
                $this->_redirect('prx/lenstintcolor/rowdata');
                return;
            }
        }

        $this->coreRegistry->register('row_data', $rowData);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $title = $rowId ? __('Edit Lenstintcolor Data ').$rowTitle : __('Add Lenstintcolor Data');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }

    protected function _isAllowed() {
        return $this->_authorization->isAllowed('Webindiainc_Prx::add_row');
    }
}
