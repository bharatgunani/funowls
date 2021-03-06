<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */
namespace Onestepcheckout\Iosc\Observer\Adminhtml;

class LayoutHandler implements \Magento\Framework\Event\ObserverInterface
{

    /**
     *
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     *
     * @var \Magento\Framework\App\Request\Http
     */
    public $paramName = 'section';

    /**
     *
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(\Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
    }

    /**
     * Add handles to the page if admin sectiom matches current module name.
     *
     * @param Observer $observer
     * @event layout_load_before
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $params = $this->request->getParams();

        if (! empty($params['section'])) {
            $moduleName = $this->getModuleName();
            if ($params['section'] === $moduleName) {
                /** @var LayoutInterface $layout */
                $layout = $observer->getData('layout');
                $layout->getUpdate()->addHandle('adminhtml_system_config_edit_section_' . $moduleName);
            }
        }
    }

    /**
     * Return lower case module name from class name
     *
     * @return string $moduleName
     */
    private function getModuleName()
    {
        $class = get_class($this);
        $moduleName = strtolower(
            str_replace('\\', '_', substr($class, 0, strpos($class, '\\Observer')))
        );
        return (string) $moduleName;
    }
}
