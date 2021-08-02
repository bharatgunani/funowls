<?php

namespace Magenest\InstagramShop\Model;

use Magenest\InstagramShop\Helper\Helper;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;

/**
 * Class Client
 * @package Magenest\InstagramShop\Model
 */
class Client
{
    const URI = 'https://graph.facebook.com';
    const VERSION = 'v4.0';
    const INSTAGRAM_SHOP_CONFIGURATION_SECTION = 'adminhtml/system_config/edit/section/magenest_instagram_shop';

    /** @var Json */
    protected $_jsonFramewrok;

    /** @var CurlFactory */
    protected $_curlFactory;

    /**  @var ScopeConfigInterface */
    protected $_scopeConfig;

    /** @var UrlInterface */
    protected $_url;

    /**
     * @var CollectionFactory
     */
    protected $_configCollection;

    /**
     * @var string[]
     */
    protected $media = [
        'id',
        'ig_id',
        'comments_count',
        'like_count',
        'media_type',
        'media_url',
        'permalink',
        'thumbnail_url',
        'timestamp',
        'caption',
        'children'
    ];

    /**
     * Client constructor.
     *
     * @param Json $jsonFramewrok
     * @param CurlFactory $curlFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlInterface $url
     * @param CollectionFactory $configCollection
     */
    public function __construct(
        Json $jsonFramewrok,
        CurlFactory $curlFactory,
        ScopeConfigInterface $scopeConfig,
        UrlInterface $url,
        CollectionFactory $configCollection
    ) {
        $this->_jsonFramewrok = $jsonFramewrok;
        $this->_curlFactory = $curlFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_url = $url;
        $this->_configCollection = $configCollection;
    }

    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @return array|mixed|string
     * @throws LocalizedException
     */
    protected function _httpRequest($url, $method = 'GET', $params = [])
    {
        $curl = $this->_curlFactory->create();
        $curl->setConfig([
            'timeout' => 2,
            'useragent' => 'Magenest Instagram Shop',
            'referer' => $this->_url->getUrl('*/*/*')
        ]);
        switch ($method) {
            case 'GET':
                if (!empty($params)) {
                    $url .= '?' . http_build_query($params);
                }
                $curl->write($method, $url);
                break;
            case 'POST':
                $curl->write($method, $url, '1.1', [], http_build_query($params));
                break;
        }
        $response = $curl->read();
        $curl->close();
        if ($response === false) {
            throw new LocalizedException(__('HTTP error occurred while issuing request. Please contact Administrator for more information.'));
        }
        $response = preg_split('/^\r?$/m', $response, 2);
        if (count($response) != 2) {
            $decodedResponse = trim($response[0]);
        } else {
            $response = trim($response[1]);
            $decodedResponse = json_decode($response, true);
        }

        if (is_array($decodedResponse) && !empty($decodedResponse)) {
            if (isset($decodedResponse['error'])) {
                $error = $decodedResponse['error'];
                throw new LocalizedException(__(implode(', ', $error)));
            }
            if (isset($decodedResponse['data'])) {
                // reverser list photo to arrange the latest photos for the highest id
                $decodedResponse['data'] = array_reverse($decodedResponse['data']);
            }
            return $decodedResponse;
        } else {
            throw new LocalizedException(__('Empty response.'));
        }
    }

    /**
     * @return mixed|string|void
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getPageId()
    {
        // api: GET /me/accounts?access_token
        $accessToken = $this->getStoreConfig(Helper::FACEBOOK_TOKEN);
        $query = [
            "access_token" => "$accessToken"
        ];
        $endpoint = self::URI . "/" . self::VERSION . "/me/accounts";
        $url = $endpoint . '?' . http_build_query($query);
        $response = $this->_httpRequest($url);
        if (is_array($response) && isset($response['data'])) {
            $data = $response['data'];
            foreach ($data as $item) {
                if (isset($item['id'])) {
                    return $item['id'];
                }
            }
        } else {
            throw new \Exception(__("Something went wrong. Please try again!"));
        }
    }

    /**
     * @return array|mixed
     * @throws LocalizedException
     */
    public function getInstagramBusinessId()
    {
        // api: GET /me/accounts?fields=instagram_business_account,access_token
        $accessToken = $this->getConfig(Helper::FACEBOOK_TOKEN);
        $query = [
            "fields" => 'instagram_business_account',
            "access_token" => "$accessToken"
        ];
        $endpoint = self::URI . "/" . self::VERSION . "/me/accounts";
        $url = $endpoint . '?' . http_build_query($query);
        $response = $this->_httpRequest($url);
        $data = [];
        if (isset($response['data'])) {
            $data = $response['data'];
        }
        return $data;
    }

    /**
     * @param $pageId
     * @return mixed|string
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getPageIdInstagram($pageId)
    {
        // api:  GET /{page-id}?fields=instagram_business_account&access_token={access-token}

        $accessToken = $this->getStoreConfig(Helper::FACEBOOK_TOKEN);
        $query = [
            "fields" => 'instagram_business_account',
            "access_token" => "$accessToken"
        ];
        $endpoint = self::URI . "/" . self::VERSION . "/" . $pageId;
        $url = $endpoint . '?' . http_build_query($query);
        $response = $this->_httpRequest($url);
        if (isset($response['instagram_business_account'])) {
            $id = $response['instagram_business_account']['id'];
            return $id;
        } else {
            throw new \Exception(__("Something went wrong!"));
        }
    }

    /**
     * @param $igUserId
     * @return array|mixed|string
     * @throws LocalizedException
     */
    public function getInstagramInfo($igUserId)
    {
        // api: GET /{ig-user-id}?fields={fields}&access_token
        $fields = [
            'biography',
            'id',
            'ig_id',
            'followers_count',
            'follows_count',
            'media_count',
            'name',
            'profile_picture_url',
            'username',
            'website'
        ];
        $accessToken = $this->getConfig(Helper::FACEBOOK_TOKEN);
        $query = [
            "fields" => implode(",", $fields),
            "access_token" => "$accessToken"
        ];
        $endpoint = self::URI . "/" . self::VERSION . "/" . $igUserId;
        $url = $endpoint . '?' . http_build_query($query);
        return $this->_httpRequest($url);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getAllMedia()
    {
        // api: GET /{ig-user-id}/media?access_token
        $accountId = $this->getStoreConfig(Helper::INSTA_BUSSINESS_ID);
        $accessToken = $this->getStoreConfig(Helper::FACEBOOK_TOKEN);
        $query = [
            'fields' => implode(',', $this->media),
            'access_token' => "$accessToken",
            'limit' => 1000
        ];
        $endpoint = self::URI . "/" . self::VERSION . "/" . $accountId . "/media";
        $url = $endpoint . '?' . http_build_query($query);
        $response = file_get_contents($url);
        $response = $this->_jsonFramewrok->unserialize($response, true);
        if (count($response) == 2 && isset($response['data'])) {
            $decodedResponse = array_reverse($response['data']);
            return $decodedResponse;
        } else {
            throw new LocalizedException(__('Empty response.'));
        }
    }

    /**
     * @param $xmlPath
     * @return mixed
     */
    protected function getStoreConfig($xmlPath)
    {
        return $this->_scopeConfig->getValue($xmlPath);
    }

    /**
     * @return array
     */
    public function getTags()
    {
        $str = preg_replace('/\s+/', '', $this->getStoreConfig(Helper::INSTA_HASHTAGGED));
        $tags = $str ? explode(',', $str) : [];
        foreach ($tags as $key => &$tag) {
            $tag = preg_replace('/[^A-Za-z0-9]/', '', $tag);
            if (!$tag) {
                unset($tags[$key]);
            }
        }
        return array_unique($tags);
    }

    /**
     * Get Store Config without caching
     *
     * @param $xmlPath
     * @return mixed
     */
    protected function getConfig($xmlPath)
    {
        $config = $this->_configCollection->create()->addFieldToFilter('path', $xmlPath);
        return $config->getFirstItem()->getValue();
    }
}
