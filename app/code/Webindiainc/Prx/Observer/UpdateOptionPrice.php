<?php
namespace Webindiainc\Prx\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

class UpdateOptionPrice implements ObserverInterface
{
    protected $dHelpler;
    protected $request;
    /**
     * @var FormatPrice
     */
    protected $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Directory\Helper\Data $dHelpler,
        CheckoutSession $checkoutSession
    ) {
        $this->request = $request;
        $this->dHelpler = $dHelpler;
        $this->checkoutSession  = $checkoutSession;
    }

    public function execute(Observer $observer)
    {   
        $action = $this->request->getFullActionName();
        if ($action == 'directory_currency_switch' || $action == 'geoip_switcher_index') {
            $quote = $this->checkoutSession->getQuote();
            if ($quote ) {
                $cartData = $quote->getAllVisibleItems();
                foreach ($cartData as $item) {
                    $itemId = $item->getId();
                    if ($itemId) {
                        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
                        $item->getCustomPrice();
                        $itemAmount = $item->getCustomPrice(); // product price
                        $itemAmount = $this->convertPrice($itemAmount);
                        $item->setOriginalCustomPrice($itemAmount);
                        $item->getProduct()->setIsSuperMode(true);
                        $item->save();
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

        return $amount;
    }
}
?>