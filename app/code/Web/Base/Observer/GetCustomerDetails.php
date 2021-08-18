<?php
namespace Web\Base\Observer;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Api\GroupRepositoryInterface;

class GetCustomerDetails implements ObserverInterface
{
	const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'customer/create_account/email_template';

    protected $request;

    public function __construct(TransportBuilder $transportBuilder,ScopeConfigInterface $scopeConfig,\Magento\Framework\App\Request\Http $request
    )
    {
    	 $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
    }

    public function execute(
    	\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $requestParms = $this->request->getPost();
        $customerOld = $observer->getOrigCustomerDataObject();
            $firstName = $customer->getFirstName().' '.$customer->getLastName();
            $customerEmail = $customer->getEmail();
            //$customerpass = $customer->setPassword($requestParms['password']);
             $groupVariables = [
                'name' => $firstName,
                'email' => $customerEmail,
                'password' => $requestParms['password']
            ];           


            $email = $this->scopeConfig->getValue('trans_email/ident_general/email',ScopeInterface::SCOPE_STORE);
            $name  = $this->scopeConfig->getValue('trans_email/ident_general/name',ScopeInterface::SCOPE_STORE);
            $setTemplateId = $this->scopeConfig->getValue(self::XML_PATH_REGISTER_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE);

            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($groupVariables);

             $transport = $this->transportBuilder
                ->setTemplateIdentifier($setTemplateId)
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
                ->setTemplateVars(['customer' => $postObject])
                ->setFrom(['name' => $name,'email' => $email])
                ->addTo([$customerEmail])
                ->getTransport();
            $transport->sendMessage();

        return $this;
    }
  }
