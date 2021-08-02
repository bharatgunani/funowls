<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */

namespace Amasty\VisualMerch\Model\Rule\Condition\Price;

class FinalPrice extends AbstractPrice
{
    public function getAttributeElementHtml()
    {
        return __('Final Price');
    }

    protected function _getAttributeCode()
    {
        return 'final_price';
    }
}
