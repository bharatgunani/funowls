<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


namespace Amasty\VisualMerch\Setup;

use Amasty\VisualMerch\Setup\Operation\DisableIsRequiredOptionForMerchAttributes;
use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ConfigInterface
     */
    private $resourceConfig;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DisableIsRequiredOptionForMerchAttributes
     */
    private $disableIsRequiredOptionForMerchAttributes;

    public function __construct(
        ConfigInterface $resourceConfig,
        Filesystem $filesystem,
        DisableIsRequiredOptionForMerchAttributes $disableIsRequiredOptionForMerchAttributes
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->filesystem = $filesystem;
        $this->disableIsRequiredOptionForMerchAttributes = $disableIsRequiredOptionForMerchAttributes;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.4.3', '<')) {
            $this->disableIsRequiredOptionForMerchAttributes->execute();
        }

        $setup->endSetup();
    }
}
