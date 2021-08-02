<?php
namespace Magenest\InstagramShop\Controller\Adminhtml\Connect;

use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

abstract class AbstractClient extends \Magento\Backend\App\Action
{
    /**
     * @var \Magenest\InstagramShop\Model\Client
     */
    protected $_clientModel;

    /**
     * @var \Magento\Framework\App\Cache\Manager
     */
    protected $_cacheManager;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $_writer;

    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_jsonFramework;

    /**
     * AbstractClient constructor.
     *
     * @param \Magenest\InstagramShop\Model\Client $client
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $writer
     * @param LoggerInterface $logger
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonFramework
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magenest\InstagramShop\Model\Client $client,
        \Magento\Framework\App\Cache\Manager $cacheManager,
        \Magento\Framework\App\Config\Storage\WriterInterface $writer,
        LoggerInterface $logger,
        Registry $registry,
        PageFactory $resultPageFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonFramework,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_clientModel = $client;
        $this->_cacheManager = $cacheManager;
        $this->_writer = $writer;
        $this->_logger = $logger;
        $this->_coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->_jsonFramework = $jsonFramework;
        parent::__construct($context);
    }

    /** Clean config cache */
    public function cleanConfigCache()
    {
        $this->_cacheManager->clean([Config::TYPE_IDENTIFIER]);
    }
}