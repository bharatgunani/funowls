<?php

namespace Webindiainc\Prx\Plugin\Quote;

class Splitcart {

	protected $_request;

	public function __construct(
		\Magento\Framework\App\RequestInterface $request
	) { 
		$this->_request = $request;
	}
	
	public function afterRepresentProduct(\Magento\Quote\Model\Quote\Item $subject, $result)
    {
		if($this->_request->getPost('lensprice')) {
			$price = $this->_request->getPost('lensprice');
		}
		$result = false; // every time create new quote
        if(isset($price)) {
             $result = false;
        }
        return $result;
    }
}