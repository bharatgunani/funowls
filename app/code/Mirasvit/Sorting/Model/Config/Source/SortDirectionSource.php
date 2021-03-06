<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-sorting
 * @version   1.1.7
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Sorting\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class SortDirectionSource implements ArrayInterface
{
    public function toOptionArray()
    {
        $result = [
            [
                'label' => 'Ascending',
                'value' => 'asc',
            ],
            [
                'label' => 'Descending',
                'value' => 'desc',
            ],
        ];

        return $result;
    }
}
