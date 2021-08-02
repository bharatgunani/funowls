<?php

namespace Magenest\InstagramShop\Block\Instagram;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

/**
 * Class LinkedProducts
 * @package Magenest\InstagramShop\Block\Instagram
 */
class LinkedProducts extends Template
{
    /**
     * @var string
     */
    protected $_template = 'instagram/linked_products.phtml';

    /**
     * @var Product[]
     */
    protected $productList = [];

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Configurable
     */
    private $_configurableResources;

    /**
     * @var Image
     */
    protected $imageHelper;

    /**
     * LinkedProducts constructor.
     * @param Template\Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param Configurable $configurableResources
     * @param Image $image
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        ProductRepositoryInterface $productRepository,
        Configurable $configurableResources,
        Image $image,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
        $this->imageHelper       = $image;
        $this->_configurableResources = $configurableResources;
    }

    /**
     * @param $productIds
     * @return $this
     */
    public function setProductList($productIds)
    {
        if (is_string($productIds)) {
            $productIds = str_replace(' ', '', $productIds);
            $productIds = explode(',', $productIds);
        }
        if (is_array($productIds)) {
            foreach ($productIds as $productId) {
                try {
                    $this->productList[] = $this->productRepository->getById($productId);
                } catch (NoSuchEntityException $e) {
                }
            }
        }
        return $this;
    }

    /**
     * @return Product[]
     */
    public function getProductList()
    {
        return $this->productList;
    }

    /**
     * @param Product $product
     * @param string $imageId
     * @param int $width
     * @param int $height
     * @return Image
     */
    public function getProductImageUrl(Product $product, $imageId = 'product_page_image_small', $width = 150, $height = 150)
    {
        return $this->imageHelper->init($product, $imageId)
            ->setImageFile($product->getFile())
            ->resize($width, $height);
    }

    /**
     * @param Product $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function getProductUrl(Product $product)
    {
        $parentProductId = $this->_configurableResources->getParentIdsByChild($product->getId());
        if (!empty($parentProductId)) {
            $parentProductId = reset($parentProductId);
            $product = $this->productRepository->getById($parentProductId, false, $this->_storeManager->getStore()->getId());
        }
        return $product->getUrlModel()->getUrl($product);
    }
}
