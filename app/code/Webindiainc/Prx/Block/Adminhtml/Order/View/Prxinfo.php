<?php
namespace Webindiainc\Prx\Block\Adminhtml\Order\View;

class Prxinfo extends \Magento\Backend\Block\Template
{
	protected $orderRepo;
	
	public function __construct(
    	\Magento\Backend\Block\Template\Context $context,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepo
	) {
		$this->orderRepo = $orderRepo;
		parent::__construct($context);
    }
	
	public function getOrderId() {
		$order_id = $this->getRequest()->getParam('order_id');
		return $order_id;
	}

	public function getOrderData() {
		$order_id = $this->getRequest()->getParam('order_id');
		return $order = $this->orderRepo->get($order_id);
		
	}
}