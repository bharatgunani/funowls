<?php
namespace RedChamps\GuestOrders\Model;

use Magento\Email\Model\TemplateFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MessageInterfaceFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;

/*
 * Package: GuestOrders
 * Class: EmailSender
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class EmailSender
{
    const XML_PATH_EMAIL_COPY_TO     = 'email/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD = 'email/copy_method';
    const XML_PATH_EMAIL_TEMPLATE    = 'email/template';
    const XML_PATH_EMAIL_IDENTITY    = 'email/identity';
    const XML_PATH_EMAIL_ENABLED     = 'email/enabled';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var MessageInterfaceFactory
     */
    protected $frameworkMessageInterfaceFactory;

    /**
     * @var TemplateFactory
     */
    protected $emailTemplateFactory;

    /**
     * @var ConfigReader
     */
    private $configReader;

    public function __construct(
        ConfigReader $configReader,
        TransportBuilder $transportBuilder,
        TemplateFactory $emailTemplateFactory
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->configReader = $configReader;
    }

    public function sendEmail($order, $registerUrl)
    {
        $store = $order->getStore();
        $storeId = $store->getId();

        if (!$this->canSendEmail($storeId)) {
            return $this;
        }

        $mailer = $this->transportBuilder;
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO, $storeId);
        $copyMethod = $this->getConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        $area = \Magento\Framework\App\Area::AREA_FRONTEND;
        // Retrieve corresponding email template id and customer name\
        $emailTemplatePath = self::XML_PATH_EMAIL_TEMPLATE;

        $templateId = $this->getConfig($emailTemplatePath, $storeId);

        $customerName = preg_replace(
            "/[^A-Za-z0-9 ]/",
            '',
            $order->getBillingAddress()->getName()
        );
        $mailer->addTo($order->getCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $mailer->addBcc($email);
            }
        }

        // Email copies are sent as separated emails if their copy method is 'copy' or a customer should not be notified
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $mailer->addTo($email);
            }
        }

        $mailer->setTemplateOptions(
            [
                'area' => $area,
                'store' =>$storeId
            ]
        );

        // Set all required params and send emails
        $mailer->setFrom($this->getConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setTemplateIdentifier($templateId);
        $mailer->setTemplateVars(
            [
                'order' => $order,
                'store' => $store,
                'register_link' => $registerUrl
            ]
        );
        $mailer->getTransport()->sendMessage();

        return $this;
    }

    /*
     * Get emails from admin config and explode them by comma
     * */
    protected function _getEmails($configPath, $storeId = 0)
    {
        $data = $this->getConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /*
     * Check if email can be sent to customer
     * */
    public function canSendEmail($store = null)
    {
        return $this->getConfig(self::XML_PATH_EMAIL_ENABLED, $store);
    }

    protected function getConfig($path, $store = null)
    {
        return $this->configReader->getConfig($path, $store);
    }
}
