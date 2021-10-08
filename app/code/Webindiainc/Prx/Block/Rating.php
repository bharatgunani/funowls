<?php

namespace Webindiainc\Prx\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Rating extends Template {
	
	protected $_storeManager;
    protected $_productFactory;
    protected $_ratingFactory;
    protected $_reviewFactory;


	public function __construct(
		Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Review\Model\RatingFactory $ratingFactory,
		\Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewFactory,
		array $data = []
	)
	{
		$this->_storeManager = $storeManager;
		$this->_productFactory = $productFactory;
		$this->_ratingFactory = $ratingFactory;
		$this->_reviewFactory = $reviewFactory;
		//$this->storeManager = $context->getStoreManager();
		parent::__construct($context, $data);
	}
	
	public function getReviewCollection(){
        $collection = $this->_reviewFactory->create()
        ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)->setDateOrder()->addRateVotes()->setPageSize(3); 
		return $collection;
    }

    public function getRatingCollection(){
        $ratingCollection = $this->_ratingFactory->create()
        ->getResourceCollection()->setPositionOrder()->setStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->addRatingPerStoreName(
            $this->_storeManager->getStore()->getId()
        )->load();

        return $ratingCollection->getData();
    }
	
	
}