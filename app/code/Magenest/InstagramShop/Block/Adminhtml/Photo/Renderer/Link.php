<?php
namespace Magenest\InstagramShop\Block\Adminhtml\Photo\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

/**
 * Class Link
 * @package Magenest\InstagramShop\Block\Adminhtml\Photo\Renderer
 */
class Link extends AbstractRenderer
{
    /**
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }

    /**
     * Renders grid column
     *
     * @param \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $url = $this->_getValue($row);

        return '<a href="' . $url . '" target="_blank" >'.$url."</a>";
    }
}
