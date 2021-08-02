<?php
namespace CjPublicis\UniversalTag\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $defaultConnection = $setup->getConnection();

        $defaultConnection->delete(
            $this->getTableNameWithPrefix($setup, 'core_config_data'),
            "path LIKE 'universaltag/%'"
        );
    }

    private function getTableNameWithPrefix(SchemaSetupInterface $setup, $tableName)
    {
        return $setup->getTable($tableName);
    }
}
