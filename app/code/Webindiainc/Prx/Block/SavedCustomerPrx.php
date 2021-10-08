<?php

namespace Webindiainc\Prx\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Webindiainc\Prx\Helper\Data as HelperData;


class SavedCustomerPrx extends Template {
	
	protected $helper;
	protected $resourceConnection;

	public function __construct(
		Context $context,
		HelperData $helper,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		array $data = []
	)
	{
		$this->helper = $helper;
		$this->storeManager = $context->getStoreManager();
		$this->resourceConnection = $resourceConnection;
		parent::__construct($context, $data);
	}
	
	public function getSavedPrx($customer_id) {
		$table = $this->resourceConnection->getTableName('prx_customerdata');
		$connection = $this->resourceConnection->getConnection();
		$sql = "SELECT * FROM  $table WHERE `prx_customerid` = $customer_id";
		$result = $connection->fetchAll($sql);
		return $result;
	}
	
	public function getCustomerId() {
		return $this->helper->getCustomerId();
	}

}