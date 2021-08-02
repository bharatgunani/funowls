<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */

namespace Amasty\VisualMerch\Model\Rule\Condition\Price;

class Max extends AbstractPrice
{
    public function getAttributeElementHtml()
    {
        return __('Max Price');
    }

    protected function _getAttributeCode()
    {
        return 'max_price';
    }
}
