<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Sparx\Couponss\Model\Rewrite\SalesRule\Quote\Address\Total;

use Magento\Quote\Api\Data\ShippingAssignmentInterface as ShippingAssignment;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\SalesRule\Model\Quote\Discount as DiscountCollector;
use Magento\SalesRule\Model\Validator;

/**
 * Total collector for shipping discounts.
 */
class ShippingDiscount extends \Magento\SalesRule\Model\Quote\Address\Total\ShippingDiscount 
{
    /**
     * @var Validator
     */
    private $calculator;

    /**
     * @param Validator $calculator
     */
    
    /**
     * @inheritdoc
     *
     * @param Quote $quote
     * @param ShippingAssignment $shippingAssignment
     * @param Total $total
     * @return ShippingDiscount
     */
    
    /**
     * @inheritdoc
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Total $total): array
    {
        $result = [];
        $amount = $total->getDiscountAmount();

        if ($amount != 0) {
            $description = (string)$total->getDiscountDescription() ?: '';
            $result = [
                'code' => DiscountCollector::COLLECTOR_TYPE_CODE,
                'title' => strlen($description) ? __('Coupon :') : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }
}
