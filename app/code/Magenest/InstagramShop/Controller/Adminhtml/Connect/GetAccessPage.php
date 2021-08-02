<?php
namespace Magenest\InstagramShop\Controller\Adminhtml\Connect;

use Magenest\InstagramShop\Helper\Helper;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

class GetAccessPage extends AbstractClient
{

    /**
     * @return Page|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $pageData = [];
        try {
            $params = $this->getRequest()->getParams();
            if (isset($params['message']) && isset($params['access_token'])) {
                if ($params['message'] == '1') {
                    $this->_writer->save(Helper::FACEBOOK_TOKEN, $params['access_token']);
                    $this->cleanConfigCache();
                    $pageData = $this->_clientModel->getInstagramBusinessId();
                } else {
                    $this->_logger->critical("Connect Instagram: access_token param is null");
                    $this->messageManager->addSuccessMessage(__('Cannot get Access Token!'));
                }
            } else {
                $this->_logger->critical("Connect Instagram: Message params does not exist.");
                throw new \Exception(__("Something went wrong!"));
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $this->_coreRegistry->register(Helper::PAGE_DATA, $pageData);
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_InstagramShop::instagramshop');
        $resultPage->addBreadcrumb(__('Magenest'), __('Magenest'));
        $resultPage->getConfig()->getTitle()->prepend(__('Choose an Instagram account'));
        return $resultPage;
    }
}
