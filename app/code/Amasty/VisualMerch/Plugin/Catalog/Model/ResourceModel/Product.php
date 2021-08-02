<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


declare(strict_types=1);

namespace Amasty\VisualMerch\Plugin\Catalog\Model\ResourceModel;

use Magento\Catalog\Model\Indexer\Category\Product\Processor;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;

class Product
{
    /**
     * @var Processor
     */
    private $categoryProductIndexerProcessor;

    public function __construct(
        Processor $categoryProductIndexerProcessor
    ) {
        $this->categoryProductIndexerProcessor = $categoryProductIndexerProcessor;
    }

    public function afterSave(
        ProductResourceModel $subject,
        ProductResourceModel $result,
        ProductModel $product
    ): ProductResourceModel {
        $this->categoryProductIndexerProcessor->reindexRow($product->getId());

        return $result;
    }
}
