<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_VisualMerch
 */


declare(strict_types=1);

namespace Amasty\VisualMerch\Model\Rule\Condition\Optimization\Optimizers;

use Amasty\VisualMerch\Model\Rule\Condition\Optimization\ConditionsOptimizerInterface;
use Magento\Rule\Model\Condition\Combine as MagentoCombineConditions;

class Combine implements ConditionsOptimizerInterface
{
    /**
     * @var ConditionsOptimizerInterface[]
     */
    private $optimizers;

    /**
     * @param ConditionsOptimizerInterface[] $optimizers
     */
    public function __construct(
        array $optimizers = []
    ) {
        $this->optimizers = $optimizers;
    }

    public function optimize(MagentoCombineConditions $conditions): void
    {
        foreach ($this->optimizers as $optimizer) {
            if ($optimizer instanceof ConditionsOptimizerInterface) {
                $optimizer->optimize($conditions);
            }
        }
    }
}
