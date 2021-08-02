<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Cron;

class CatalogCategoryProductReindex
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Product
     */
    private $indexer;

    public function __construct(
        \Magento\Catalog\Model\Indexer\Category\Product $indexer
    ) {
        $this->indexer = $indexer;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $this->indexer->executeFull();
    }
}
