<?php
namespace Funowls\CustomAttributes\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Config;
   
class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
   
    public function __construct(EavSetupFactory $eavSetupFactory, Config $eavConfig)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $objectManager->get('Magento\Framework\App\State');
        $state->setAreaCode('adminhtml');
        
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $config = $objectManager->get(\Magento\Catalog\Model\Config::class);
        $attributeManagement = $objectManager->get(\Magento\Eav\Api\AttributeManagementInterface::class);

        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSet = $eavSetup->getAttributeSet($entityTypeId, 'Default');

        $attributeNames = array();
        $sortOrder = 100;
        foreach($attributeNames as $attributeName) {
            $attributeKey = strtolower(str_replace(" ", "_", $attributeName));
            $modelSource = 'Funowls\CustomAttributes\Model\Source\\'.str_replace(" ", "", $attributeName);
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeKey);
            $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeKey, [
                'group' => 'Product Specification',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'sort_order' => $sortOrder,
                'label' => $attributeName,
                'input' => 'select',
                'class' => '',
                'source' => $modelSource,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'apply_to' => ''
            ]);

            // assign attribute to group
            $group_id = $config->getAttributeGroupId($attributeSet['attribute_set_id'], 'Product Specification');
            $attributeManagement->assign(
                'catalog_product',
                $attributeSet['attribute_set_id'],
                $group_id,
                $attributeKey,
                100
            );

            $sortOrder = $sortOrder + 1;
        }
    }
}
?>