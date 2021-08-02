<?php
/**
 * @author andy
 * @email andyworkbase@gmail.com
 * @team MageCloud
 * @package MageCloud_BaseSeoChanges
 */
namespace MageCloud\BaseSeoChanges\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Data
 * @package MageCloud\BaseSeoChanges\Helper
 */
class Data extends AbstractHelper
{
    /**
     * XML path
     */
    const XML_PATH_ENABLED = 'base_seo_changes/general/enabled';
    const XML_PATH_ENABLED_REDIRECTS = 'base_seo_changes/general/enabled_redirects';
    const XML_PATH_CATEGORY_SNIPPET_ENABLED = 'base_seo_changes/category_snippet/enabled';
    const XML_PATH_CATEGORY_SNIPPET_RATING_VALUE = 'base_seo_changes/category_snippet/rating_value';
    const XML_PATH_CATEGORY_SNIPPET_REVIEW_COUNT = 'base_seo_changes/category_snippet/review_count';
    const XML_PATH_CATEGORY_SNIPPET_BEST_RATING = 'base_seo_changes/category_snippet/best_rating';

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param null $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function redirectsIsEnable($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED_REDIRECTS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function isCategorySnippetEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CATEGORY_SNIPPET_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getRatingValue($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_SNIPPET_RATING_VALUE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getReviewCount($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_SNIPPET_REVIEW_COUNT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getBestRating($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CATEGORY_SNIPPET_BEST_RATING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}