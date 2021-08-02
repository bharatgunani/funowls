<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerchCore
 */


declare(strict_types=1);

namespace Amasty\VisualMerchCore\Model\Indexer\Catalog\Product;

use Amasty\VisualMerchCore\Model\Indexer\Catalog\Product\Eav\Action\Row;
use Amasty\VisualMerchCore\Model\Indexer\Catalog\Product\Eav\Action\Rows;
use Amasty\VisualMerchCore\Model\Indexer\Catalog\Product\Eav\Action\Full;

class Eav implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var Row
     */
    private $indexerRow;

    /**
     * @var Rows
     */
    private $indexerRows;

    /**
     * @var Full
     */
    private $indexerFull;

    public function __construct(
        Row $productEavIndexerRow,
        Rows $productEavIndexerRows,
        Full $productEavIndexerFull
    ) {
        $this->indexerRow = $productEavIndexerRow;
        $this->indexerRows = $productEavIndexerRows;
        $this->indexerFull = $productEavIndexerFull;
    }

    /**
     * @param int[] $ids
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $this->indexerRows->execute($ids);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeFull()
    {
        $this->indexerFull->execute();
    }

    /**
     * @param array $ids
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeList(array $ids)
    {
        $this->indexerRows->execute($ids);
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeRow($id)
    {
        $this->indexerRow->execute((int)$id);
    }
}
