<?php
namespace Magenest\InstagramShop\Controller\Adminhtml\Connect;

use Magenest\InstagramShop\Helper\Helper;
use Magenest\InstagramShop\Model\Client;

/**
 * Class SelectAccount
 * @package Magenest\InstagramShop\Controller\Adminhtml\Connect
 */
class SelectAccount extends \Magenest\InstagramShop\Controller\Adminhtml\Connect\AbstractClient
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try{
            $params = $this->getRequest()->getParams();
            if(isset($params['instagram_business_id']) && isset($params['page_id'])){
                $instBusinessId = $params['instagram_business_id'];
                $pageId = $params['page_id'];
                $this->_writer->save(Helper::FACEBOOK_PAGEID, $pageId);
                $this->_writer->save(Helper::INSTA_BUSSINESS_ID, $instBusinessId);
                $response = $this->_clientModel->getInstagramInfo($instBusinessId);
                $this->_writer->save(Helper::INSTA_ACCOUNT, $this->_jsonFramework->serialize($response));
                $this->messageManager->addSuccessMessage(__('Get Access Token Successfully!'));
            }else{
                $this->messageManager->addErrorMessage("Something went wrong!");
                $this->_logger->info('Can not get params');
            }
        }catch (\Exception $exception){
            $this->_logger->info($exception->getMessage());
        }
        $this->cleanConfigCache();
        $this->_redirect(Client::INSTAGRAM_SHOP_CONFIGURATION_SECTION);
    }
}