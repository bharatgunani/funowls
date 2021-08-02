<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Setup;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\RemoveStoreColumn
     */
    private $removeStoreColumn;

    public function __construct(
        Operation\RemoveStoreColumn $removeStoreColumn
    ) {
        $this->removeStoreColumn = $removeStoreColumn;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws LocalizedException
     * @throws \Zend_Db_Exception
     * @throws \Zend_Validate_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.3.8', '<')) {
            $this->removeStoreColumn->execute($setup);
        }

        $setup->endSetup();
    }
}
