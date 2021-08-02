<?php
namespace Magenest\InstagramShop\Helper;

use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Helper
 * @package Magenest\InstagramShop\Helper
 */
class Helper extends \Magento\Framework\App\Helper\AbstractHelper
{
    const FACEBOOK_TOKEN                   = 'magenest_instagram_shop/facebook/access_token';
    const FACEBOOK_PAGEID                  = 'magenest_instagram_shop/facebook/page_id';
    const INSTA_BUSSINESS_ID               = 'magenest_instagram_shop/instagram/account_id';
    const INSTA_ACCOUNT                    = 'magenest_instagram_shop/instagram/account';
    const INSTA_HASHTAGGED                 = 'magenest_instagram_shop/instagram_tags/tags';
    const PAGE_DATA                        = 'pageData';

    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;

    public function __construct(
        ProductMetadataInterface $productMetadata,
        \Magento\Framework\App\Helper\Context $context
    ){
        $this->productMetadata = $productMetadata;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getMagentoVersion()
    {
        return $this->productMetadata->getVersion();
    }
}