<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Test;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\ObjectManager;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    private $moduleName = 'Onestepcheckout_Iosc';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function setUp()
    {
        $this->objectManager = ObjectManager::getInstance();
    }

    public function testTheModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $paths = $registrar->getPaths(ComponentRegistrar::MODULE);
        $this->assertArrayHasKey($this->moduleName, $paths);
    }

    public function testTheModuleIsEnabled()
    {
        /** @var ModuleList $moduleList */
        $moduleList = $this->objectManager->create(ModuleList::class);
        $message = sprintf('The module "%s" is not enabled', $this->moduleName);
        $this->assertTrue($moduleList->has($this->moduleName), $message);
    }
}
