<?php
/*
|--------------------------------------------------------------------------
|   Autocomplete SearchDataProvider
|--------------------------------------------------------------------------
|
|   Autocomplete
|
|   @author Sparx
|   @date 07 augustus 2015
|   @time 17:35
*/
namespace Sparx\SmartSearch\Model\Autocomplete;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaFactory as FullTextSearchCriteriaFactory;
use Magento\Framework\Api\Search\SearchInterface as FullTextSearchApi;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 *  Full text search implementation of autocomplete.
 *
 * @package        Sparx\SmartSearch
 * @author         Sparx
 * @copyright      Copyright (c) 2015, Sparx. All rights reserved
 */
class SearchDataProvider implements DataProviderInterface
{
    const PRODUCTS_NUMBER_IN_SUGGEST = 7;

    /** @var QueryFactory */
    protected $queryFactory;

    /** @var ItemFactory */
    protected $itemFactory;

    /** @var \Magento\Framework\Api\Search\SearchInterface */
    protected $fullTextSearchApi;

    /** @var FullTextSearchCriteriaFactory */
    protected $fullTextSearchCriteriaFactory;

    /** @var FilterGroupBuilder */
    protected $searchFilterGroupBuilder;

    /** @var FilterBuilder */
    protected $filterBuilder;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /** @var \Magento\Catalog\Helper\Image */
    protected $imageHelper;

    protected $_productCollectionFactory;
    protected $_productVisibility;
    protected $_configurable;

    /**
     * Initialize dependencies.
     *
     * @param QueryFactory                                      $queryFactory
     * @param ItemFactory                                       $itemFactory
     * @param FullTextSearchApi                                 $search
     * @param FullTextSearchCriteriaFactory                     $searchCriteriaFactory
     * @param FilterGroupBuilder                                $searchFilterGroupBuilder
     * @param FilterBuilder                                     $filterBuilder
     * @param ProductRepositoryInterface                        $productRepository
     * @param SearchCriteriaBuilder                             $searchCriteriaBuilder
     * @param StoreManagerInterface                             $storeManager
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Catalog\Helper\Image                     $imageHelper
     */
    public function __construct(
        QueryFactory $queryFactory,
        ItemFactory $itemFactory,
        FullTextSearchApi $search,
        FullTextSearchCriteriaFactory $searchCriteriaFactory,
        FilterGroupBuilder $searchFilterGroupBuilder,
        FilterBuilder $filterBuilder,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        Image $imageHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    )
    {
        $this->queryFactory                  = $queryFactory;
        $this->itemFactory                   = $itemFactory;
        $this->fullTextSearchApi             = $search;
        $this->fullTextSearchCriteriaFactory = $searchCriteriaFactory;
        $this->filterBuilder                 = $filterBuilder;
        $this->searchFilterGroupBuilder      = $searchFilterGroupBuilder;
        $this->productRepository             = $productRepository;
        $this->searchCriteriaBuilder         = $searchCriteriaBuilder;
        $this->storeManager                  = $storeManager;
        $this->priceCurrency                 = $priceCurrency;
        $this->imageHelper                   = $imageHelper;
        $this->_productCollectionFactory     = $productCollectionFactory;
        $this->_productVisibility = $productVisibility;
        $this->_configurable = $configurable;
    }

    /**
     * getItems method
     *
     * @return array
     */
    public function getItems()
    {
        $result     = [];
        $resultIds  = [];
        $query      = $this->queryFactory->get()->getQueryText();
        $products = $this->_productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter([
                ['attribute' => 'name', 'like' => '%'.$query.'%'],
                ['attribute' => 'sku',  'like' => '%'.$query.'%'],
                ['attribute' => 'upc',  'like' => '%'.$query.'%'],
            ]);


        if ( $products )
        {
            foreach ( $products as $product )
            {
                if ($product->getTypeId() == 'configurable'
                    && !in_array($product->getData('visibility'), $this->_productVisibility->getVisibleInSiteIds())) {
                    continue;
                }
                if ($product->getTypeId() == 'simple'
                    && !in_array($product->getData('visibility'), $this->_productVisibility->getVisibleInSiteIds())) {

                    $parents = $this->_configurable->getParentIdsByChild($product->getId());

                    if (empty($parents)) {
                        continue;
                    }

                    $parent = $this->productRepository->getById($parents[0]);

                    if (empty($parent)) {
                        continue;
                    }

                    $product = $parent;
                }

                if (in_array($product->getId(), $resultIds)) {
                    continue;
                }

                $resultIds[] = $product->getId();

                $image = $this->imageHelper->init($product, 'product_page_image_small')->getUrl();

                if ($product->getTypeId() == 'configurable') {
                    $childProductimage = [];
                    $_children = $product->getTypeInstance()->getUsedProducts($product);
                    foreach ($_children as $child) {
                        $childProductimage[] = $this->imageHelper->init($child, 'product_page_image_small')->getUrl();
                    }

                    $image = $childProductimage[0];
                }

                $resultItem = $this->itemFactory->create([
                    'type'              => $product->getTypeId(),
                    'title'             => $product->getName(),
                    'price'             => $this->priceCurrency->format($product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue(),false),
                    'special_price'     => $this->priceCurrency->format($product->getPriceInfo()->getPrice('special_price')->getAmount()->getValue(),false),
                    'has_special_price' => $product->getSpecialPrice() > 0 ? true : false,
                    'image'             => $image,
                    'url'               => $product->getProductUrl()
                ]);

                $result[]   = $resultItem;
            }
        }

        return $result;
    }

    /**
     * Perform full text search and find IDs of matching products.
     *
     * @param $query
     *
     * @return array
     */
    protected function searchProductsFullText($query)
    {
        $searchCriteria = $this->fullTextSearchCriteriaFactory->create();

        /** To get list of available request names see Magento/CatalogSearch/etc/search_request.xml */
        $searchCriteria->setRequestName('quick_search_container');
        $filter      = $this->filterBuilder->setField('search_term')->setValue($query)->setConditionType('like')->create();
        $filterGroup = $this->searchFilterGroupBuilder->addFilter($filter)->create();
        $currentPage = 1;
        $searchCriteria->setFilterGroups([ $filterGroup ])
            ->setCurrentPage($currentPage)
            ->setPageSize(self::PRODUCTS_NUMBER_IN_SUGGEST);
        $searchResults = $this->fullTextSearchApi->search($searchCriteria);
        $productIds    = [ ];

        /**
         * Full text search returns document IDs (in this case product IDs),
         * so to get products information we need to load them using filtration by these IDs
         */
        foreach ( $searchResults->getItems() as $searchDocument )
        {
            $productIds[] = $searchDocument->getId();
        }

        return $productIds;
    }
}