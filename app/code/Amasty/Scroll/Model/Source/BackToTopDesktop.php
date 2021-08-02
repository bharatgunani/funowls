<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Scroll
 */


namespace Amasty\Scroll\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class BackToTopDesktop implements OptionSourceInterface
{
    const ARROW = 'arrow';

    const TEXT = 'text';

    const EDGE = 'edge';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ARROW,
                'label' => __('Arrow only')
            ],
            [
                'value' => self::TEXT,
                'label' => __('Arrow and text')
            ],
            [
                'value' => self::EDGE,
                'label' => __('Arrow and text (page edge)')
            ]
        ];
    }
}
