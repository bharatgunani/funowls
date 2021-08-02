<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Scroll
 */


namespace Amasty\Scroll\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PageNumberStyle implements OptionSourceInterface
{
    const AS_BUTTON = 'button';

    const TEXT_WITH_DIVIDER = 'divider';

    const TEXT_WITH_BACKGROUND = 'background';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::AS_BUTTON,
                'label' => __('Button')
            ],
            [
                'value' => self::TEXT_WITH_DIVIDER,
                'label' => __('Text with divider')
            ],
            [
                'value' => self::TEXT_WITH_BACKGROUND,
                'label' => __('Text with background')
            ]
        ];
    }
}
