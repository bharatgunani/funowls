<?php
/**
 * {{COPYRIGHT_NOTICE}}
 */

namespace Onestepcheckout\Iosc\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Framework\Module\PackageInfo
     */
    public $packageInfo;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\PackageInfo $packageInfo,
        array $data = []
    ) {
            $this->packageInfo = $packageInfo;
            parent::__construct($context, $data);
    }

    /**
     * Return the content of the custom version field
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $this->packageInfo->getVersion($this->getModuleName()) . '
            <br /><a href="https://www.onestepcheckout.com/" target="_blank">www.onestepcheckout.com</a>
            <br /><a href="http://help-m2.onestepcheckout.com/help_center" target="_blank">Knowledge Base</a>
            ';
        return $html;
    }
}
