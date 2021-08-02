<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


declare(strict_types=1);

namespace Amasty\VisualMerch\Model\Rule\Condition\Optimization;

use Magento\Rule\Model\Condition\Combine as MagentoCombineConditions;

interface ConditionsOptimizerInterface
{
    public function optimize(MagentoCombineConditions $conditions): void;
}
