<?php

namespace Webindiainc\Prx\Controller\Adminhtml\Lensprescription;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class ProductsGrid extends \Magento\Backend\App\Action
{

    protected $_resultLayoutFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->_resultLayoutFactory = $resultLayoutFactory;
    }

    protected function _isAllowed() {
        return true;
    }

    public function execute() {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('productsgrid.edit.tab.products')
                     ->setInBanner($this->getRequest()->getPost('contact_products', null));

        return $resultLayout;
    }

}
