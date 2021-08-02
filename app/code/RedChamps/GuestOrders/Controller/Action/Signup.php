<?php
namespace RedChamps\GuestOrders\Controller\Action;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;

/*
 * Package: GuestOrders
 * Class: Signup
 * Company: RedChamps
 * author: rav(rav@redchamps.com)
 * */
class Signup extends Action
{
    protected $customerSession;

    protected $orderRepository;

    /**
     * Signup constructor.
     * @param Context $context
     * @param Session $session
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        Context $context,
        Session $session,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->customerSession = $session;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $orderId = base64_decode($orderId);
        if ($orderId && is_numeric($orderId)) {
            $order = $this->orderRepository->get($orderId);
            if ($order) {
                $address = $order->getBillingAddress();
                if ($address) {
                    $data = [
                        "firstname" => $address->getFirstname(),
                        "lastname" => $address->getLastname(),
                        'email' => $order->getCustomerEmail()
                    ];
                    $this->customerSession->setCustomerFormData($data);
                }
            }
        }
        $this->_redirect('customer/account/create');
    }
}
