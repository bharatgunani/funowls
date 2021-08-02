<?php
namespace CjPublicis\UniversalTag\Cookie;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;

class CjEvent
{
    const COOKIE_NAME = 'cje';

    private $cookieManager;

    private $cookieMetadataFactory;

    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    public function get()
    {
        return $this->cookieManager->getCookie(self::COOKIE_NAME);
    }

    public function set($value)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDurationOneYear()
            ->setPath('/')
            ->setHttpOnly(false)
            ->setSecure(false);

        $this->cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            $value,
            $metadata
        );
    }
}
