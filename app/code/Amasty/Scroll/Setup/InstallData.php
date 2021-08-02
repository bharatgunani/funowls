<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Scroll
 */


declare(strict_types=1);

namespace Amasty\Scroll\Setup;

use Amasty\Base\Helper\Deploy;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var Deploy
     */
    private $devDeployer;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    private $rootWrite;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Deploy $devDeployer,
        Filesystem $filesystem
    ) {
        $this->devDeployer = $devDeployer;
        $this->rootWrite = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->filesystem = $filesystem;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $path = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath('dev');
        if ($this->rootWrite->isWritable($path)) {
            $this->devDeployer->deployFolder(__DIR__ . '/../dev');
        }

        $setup->endSetup();
    }
}
