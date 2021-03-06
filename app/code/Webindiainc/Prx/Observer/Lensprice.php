<?php

namespace Webindiainc\Prx\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Webindiainc\Prx\Helper\Data as HelperData;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class Lensprice implements ObserverInterface {

	protected $_request;
	protected $resourceConnection;
	protected $helper;
	protected $logger;
	protected $_checkoutSession;
	protected $PricingHelper;


	public function __construct(
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\App\ResourceConnection $resourceConnection,
		HelperData $helper,
		PricingHelper $PricingHelper,
		\Psr\Log\LoggerInterface $logger,
		\Magento\Checkout\Model\Session $checkoutSession
	) { 
		$this->_request = $request;
		$this->resourceConnection = $resourceConnection;
		$this->helper = $helper;
		$this->PricingHelper = $PricingHelper;
		$this->logger = $logger;
		$this->_checkoutSession = $checkoutSession;
	}
	
    public function execute(\Magento\Framework\Event\Observer $observer) {
		
		if($this->_request->getPost('lenstotalprice')) {
			
			$frameprice = $this->_request->getPost('frameprice'); //product price
			$lenstotalprice = $this->_request->getPost('lenstotalprice');
			$lensprice = $lenstotalprice - $frameprice;
			$lensusagetitle = $this->_request->getPost('lensusagetitle');
			$lensusageprice = $this->_request->getPost('lensusageprice');
			$lenstypeprice = $this->_request->getPost('lenstypeprice');
			$lenstypetitle = $this->_request->getPost('lenstypetitle');
			$lensthicknesstitle = $this->_request->getPost('lensthicknesstitle');
			$lensthicknessprice = $this->_request->getPost('lensthicknessprice');
			
			$istint_strength_exist = $this->_request->getPost('istint_strength_exist');
			$istint_color_exist = $this->_request->getPost('istint_color_exist');
			
			$prxjsondata = $this->_request->getPost('prxdata');
			$prxdata = array('prxdata'=>$prxjsondata);
			$prx_save_filename = $prxjsondata['save_filename'];
			$lenstintcolor = $this->_request->getPost('lenstintcolor');
			$lenstintstrength = $this->_request->getPost('lenstintstrength');
		
			/* $lensusageprice = $this->PricingHelper->currency($lensusageprice, true, false);
			$lenstypeprice = $this->PricingHelper->currency($lenstypeprice, true, false);
			$lensthicknessprice = $this->PricingHelper->currency($lensthicknessprice, true, false); */
			
			$additionalOptions['frameprice'] = [
				'label' => 'Frame Price',
				'value' => $frameprice,
			];
			$additionalOptions['lensprice'] = [
				'label' => 'Lens Price',
				'value' => $lensprice,
			];
			$additionalOptions['lenstotalprice'] = [
				'label' => 'Total Lens Price Incl Frame',
				'value' => $lenstotalprice,
			];
			$additionalOptions['lensusage'] = [
				'label' => $lensusagetitle,
				'value' => $lensusageprice,
			];
			$additionalOptions['lenstype'] = [
				'label' => $lenstypetitle,
				'value' => $lenstypeprice,
			];
			$additionalOptions['lensthickness'] = [
				'label' => $lensthicknesstitle,
				'value' => $lensthicknessprice,
			];
			
			$additionalOptions['admin_lensusage'] = [
				'label' => 'Lens Usage',
				'value' => $lensusagetitle . ' (' .$lensusageprice . ')',
			];
			$additionalOptions['admin_lenstype'] = [
				'label' => 'Lens Type',
				'value' => $lenstypetitle . ' (' . $lenstypeprice . ')',
			];
			$additionalOptions['admin_lensthickness'] = [
				'label' => 'Lens Thickness',
				'value' => $lensthicknesstitle . ' (' . $lensthicknessprice . ')',
			];
			
			if($istint_color_exist == 'yes') {
				$additionalOptions['lenstintcolor'] = [
					'label' => 'Lens Tint Color',
					'value' => $lenstintcolor,
				];
			}
			if($istint_strength_exist == 'yes') {
				$additionalOptions['lenstintstrength'] = [
					'label' => 'Lens Tint Strength',
					'value' => $lenstintstrength,
				];
			}
			//$this->logger->info(print_r($prxdata, true));

			$price = $lenstotalprice;
			$item = $observer->getEvent()->getData('quote_item');
			$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
			
			/*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylogfile.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info(print_r($this->_checkoutSession->getQuote()->getId(), true));*/
			$quote_id = $this->_checkoutSession->getQuote()->getId();
			if(isset($price)) {
				
				

				$prxdata_encode = json_encode($prxdata);
				/* $item->setCustomPrice($price);
				$item->setOriginalCustomPrice($price); */
				
				
				$item->setCustomPrice($this->convertBasePrice($price));
				$item->setOriginalCustomPrice($this->convertPrice($price));
				$item->getProduct()->setIsSuperMode(true);
					
				$item->setPrxdataAttribute($prxdata_encode);
				$item->addOption(array(
					'code' => 'additional_options',
					'value' => json_encode($additionalOptions)
				));
				
				if( $this->helper->getCustomerId() ) {
					$customer_id = $this->helper->getCustomerId();
					$table = $this->resourceConnection->getTableName('prx_customerdata');
					
					$selectsql = "SELECT `prx_savename` FROM " . $table . " WHERE `prx_customerid` = '$customer_id' AND `prx_savename` = '$prx_save_filename' ";
					$connection = $this->resourceConnection->getConnection();
					$result = $connection->fetchAll($selectsql);

					if( count($result) > 0 ) {
						$sql = "UPDATE " . $table . " SET `prx_savedata` =  '$prxdata_encode' WHERE `prx_customerid`= '$customer_id' AND `prx_savename`= '$prx_save_filename' ";
						$connection = $this->resourceConnection->getConnection()->query($sql);
					} else {
						$sql = "INSERT INTO " . $table . "(prx_savename, prx_customerid, prx_savedata) Values ('". $prx_save_filename ."', '" . $customer_id . "', '" . $prxdata_encode . "' )";
						$connection = $this->resourceConnection->getConnection()->query($sql);
					}
				}
			}
		}
	}
	
	public function convertPrice($amount = 0, $store = null, $currency = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceCurrencyObject = $objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface'); //instance of PriceCurrencyInterface
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); //instance of StoreManagerInterface

        if (!$currency) {
            $currency = $storeManager->getStore()->getCurrentCurrencyCode();
        }

        if ($store == null) {
            $store = $storeManager->getStore()->getStoreId(); //get current store id if store id not get passed
        }
        $amount = $priceCurrencyObject->convert($amount, $store, $currency); //it return price according to current store from base currency
        
        return $amount;//You can round off to it or you can return it in its original form
    }

    public function convertBasePrice($amount = 0, $store = null, $currency = null)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceCurrencyObject = $objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface'); //instance of PriceCurrencyInterface
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); //instance of StoreManagerInterface

        if (!$currency) {
            $currency = $storeManager->getStore()->getBaseCurrency()->getCode();
        }

        if ($store == null) {
            $store = $storeManager->getStore()->getStoreId(); //get current store id if store id not get passed
        }
        $amount = $priceCurrencyObject->convert($amount, $store, "$currency"); //it return price according to current store from base currency
        
        return $amount;//You can round off to it or you can return it in its original form
    }


}