<?php

namespace Webindiainc\Coupon\Model;

use Magento\Framework\Exception\LocalizedException;
use \Magento\Quote\Api\CouponManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Coupon management object.
 */
class CouponManagement implements CouponManagementInterface
{

	protected $_productloader;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Constructs a coupon read service object.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository Quote repository.
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,\Magento\Catalog\Model\ProductFactory $_productloader
    ) {
    	$this->_productloader = $_productloader;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @inheritDoc
     */
    public function get($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        return $quote->getCouponCode();
    }

    /**
     * @inheritDoc
     */
    public function set($cartId, $couponCode)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        $items = $quote->getAllItems();
        $testCheck = false;
		    foreach ($items as $item) 
		    {
		    	$checkProductDiscount = $this->getLoadProduct($item->getProductId());
		    	if($checkProductDiscount){
		    		$testCheck = true;
		    	}
		    }
		if($testCheck === true){
			throw new CouldNotSaveException(__("Coupon discount already applied."));
		}
		else{
		
	        if (!$quote->getItemsCount()) {
	            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
	        }
	        if (!$quote->getStoreId()) {
	            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
	        }
	        $quote->getShippingAddress()->setCollectShippingRates(true);

	        try {

	            $quote->setCouponCode($couponCode);
	            $this->quoteRepository->save($quote->collectTotals());
	        } catch (LocalizedException $e) {
	            throw new CouldNotSaveException(__('The coupon code couldn\'t be applied: ' .$e->getMessage()), $e);
	        } catch (\Exception $e) {
	            throw new CouldNotSaveException(
	                __("The coupon code couldn't be applied. Verify the coupon code and try again."),
	                $e
	            );
	        }

	        if ($quote->getCouponCode() != $couponCode) {
	            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again.aditya-module"));
	        }
	    }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function remove($cartId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        try {
            $quote->setCouponCode('');
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __("The coupon code couldn't be deleted. Verify the coupon code and try again.")
            );
        }
        if ($quote->getCouponCode() != '') {
            throw new CouldNotDeleteException(
                __("The coupon code couldn't be deleted. Verify the coupon code and try again.")
            );
        }
        return true;
    }

    public function getLoadProduct($id)
    {
    	$productCollection = $this->_productloader->create()->load($id);
    	$test = $productCollection->getProdCouponCode();
        return $test;
    }
}
