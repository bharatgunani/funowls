<?php

namespace CjPublicis\UniversalTag\Controller\Index;

use Magento\Framework\App\Action\Action;

class Index extends Action
{
    public function execute()
    {
        $currentUrl = $this->_url->getCurrentUrl();
        $urlQueryString = parse_url($currentUrl, PHP_URL_QUERY);
        parse_str($urlQueryString, $paramsArray);
        foreach ($paramsArray as $key => $value) {
            $this->getResponse()->setHeader("Set-Cookie", $key . "={$value}; Max-Age=31556952; Path=/;");
        }
        $this->getResponse()->setHeader('Content-type', 'image/png');
        $this->getResponse()->setContent('');
        $this->getResponse()->setHttpResponseCode(200);
        return $this->getResponse();
    }
}
