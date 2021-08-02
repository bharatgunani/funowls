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
 * @package   mirasvit/module-optimize
 * @version   1.2.7
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\OptimizeCss\Processor;

use Mirasvit\Optimize\Api\Processor\OutputProcessorInterface;
use Mirasvit\OptimizeCss\Model\Config;

class MoveToBottomProcessor implements OutputProcessorInterface
{
    private $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    public function process($content)
    {
        if (!$this->config->isMoveCss()) {
            return $content;
        }

        // fonts may contain + symbol in the link
        preg_match_all('#(<link([^+^>]*|[^>]*font[^>]*)?>)#is', $content, $matches);

        $css = '';
        foreach ($matches[0] as $value) {
            if ($this->config->isMoveException($value)) {
                continue;
            }

            $css .= $value;

            $content = str_replace($value, '', $content);
        }

        $content = str_replace('</body>', $css . '</body>', $content);

        return $content;
    }
}
