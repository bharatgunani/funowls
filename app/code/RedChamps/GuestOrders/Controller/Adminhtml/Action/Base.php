<?php
namespace RedChamps\GuestOrders\Controller\Adminhtml\Action;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

/*
 * Package: GuestOrders
 * Class: Processed
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class Base extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Processed constructor.
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->aclResource);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $this->_setActiveMenu($this->aclResource);
        $resultPage->getConfig()->getTitle()->prepend(__($this->title));
        return $resultPage;
    }
}
