<?php
namespace Magenest\InstagramShop\Block\Adminhtml\System\Config;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;
use Magenest\InstagramShop\Helper\Helper;

class PageAccessToken extends \Magento\Framework\View\Element\Template
{
    /** @var \Magenest\InstagramShop\Model\Client  */
    protected $_clientModel;

    /** @var Registry  */
    protected $_coreRegistry;

    /** @var \Magento\Framework\Serialize\Serializer\Json  */
    protected $_jsonFramework;

    /** @var \Psr\Log\LoggerInterface  */
    protected $_logger;

    public function __construct(
        \Magenest\InstagramShop\Model\Client $clientModel,
        Registry $coreRegistry,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\View\Element\Template\Context $context,
        \Psr\Log\LoggerInterface $logger,
        array $data = []
    ){
        $this->_clientModel = $clientModel;
        $this->_coreRegistry = $coreRegistry;
        $this->_jsonFramework = $json;
        $this->_logger = $logger;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getPageData()
    {
        return $this->_coreRegistry->registry(Helper::PAGE_DATA);
    }

    /**
     * @return array
     */
    public function getInstagramBusinessId()
    {
        $result = [];
        $pageData = $this->_coreRegistry->registry(Helper::PAGE_DATA);
        if(!empty($pageData) && count($pageData) > 0){
            foreach ($pageData as $page){
                if(isset($page['instagram_business_account'])){
                    $result[] = $page['instagram_business_account']['id'];
                }
            }
        }
        return $result;
    }
    /**
     * @param $instBusinessId
     *
     * @return array|mixed|string
     */
    public function getInstagramInfo($instBusinessId)
    {
        return $this->_clientModel->getInstagramInfo($instBusinessId);
    }

    /**
     * @param array $array
     *
     * @return bool|false|string
     */
    public function convertData($array = [])
    {
        $string = '';
        try{
            $string = $this->_jsonFramework->serialize($array);
        }catch (\Exception $exception){
            $this->_logger->info($exception->getMessage());
        }
        return $string;
    }

    /**
     * @param $instBusinessId
     * @param $pageId
     *
     * @return string
     */
    public function selectAccountUrl($instBusinessId, $pageId)
    {
        return $this->getUrl('instagram/connect/selectAccount',
            [
                'instagram_business_id' => $instBusinessId,
                'page_id' => $pageId
            ]);
    }
}