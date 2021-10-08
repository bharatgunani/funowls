<?php

namespace Webindiainc\Prx\Controller\Adminhtml\Lenstype;

class Index extends \Magento\Backend\App\Action
{

    private $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webindiainc_Prx::grid_list');
        $resultPage->getConfig()->getTitle()->prepend(__('Lenstype List'));
        return $resultPage;
    }

    /**
     * Check Order Import Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webindiainc_Prx::grid_list');
    }
}
