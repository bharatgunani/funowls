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




namespace Mirasvit\OptimizeImage\Processor;


use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Optimize\Api\Processor\OutputProcessorInterface;
use Mirasvit\OptimizeImage\Model\Config;
use Mirasvit\OptimizeImage\Repository\FileRepository;
use Mirasvit\OptimizeImage\Service\FileListSynchronizationService;

class FotoramaProcessor implements OutputProcessorInterface
{
    private $config;

    private $fileRepository;

    private $syncService;

    public function __construct(
        Config $config,
        FileRepository $fileRepository,
        FileListSynchronizationService $syncService
    ) {
        $this->config         = $config;
        $this->fileRepository = $fileRepository;
        $this->syncService    = $syncService;
    }

    /**
     * {@inheritdoc}
     */
    public function process($content)
    {
        if(!$this->config->isUseWebpForFotorama()) {
            return $content;
        }

        $content = preg_replace_callback(
            '/<script type="text\/x-magento-init">([^<]*magnifier\/magnify[^<]*)<\/script>/ims',
            [$this, 'replaceCallback'],
            $content
        );

        return $content;
    }

    /**
     * @param array $match
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Json_Exception
     */
    private function replaceCallback(array $match)
    {
        $imgKeys = ['thumb', 'img', 'full'];
        $config  = SerializeService::decode($match[1]);
        $data    = $config["[data-gallery-role=gallery-placeholder]"]["mage/gallery/gallery"]["data"];

        foreach ($data as $idx => $imageConfig) {
            if ($imageConfig["type"] !== 'image') {
                continue;
            }

            foreach ($imageConfig as $key => $value) {
                if(!in_array($key, $imgKeys) || strpos($value, '.webp') !== false) {
                    continue;
                }

                $absolutePath = $this->config->retrieveImageAbsPath($value);
                $image        = $this->syncService->ensureFile($absolutePath);

                if($image && $webpPath = $image->getWebpPath()) {
                    $relativePath = $this->config->getRelativePath($absolutePath);
                    $webpPath     = str_replace($relativePath, $webpPath, $value);

                    $imageConfig[$key] = $webpPath;
                }
            }

            $data[$idx] = $imageConfig;
        }

        $config["[data-gallery-role=gallery-placeholder]"]["mage/gallery/gallery"]["data"] = $data;

        $script = SerializeService::encode($config);
        $script = str_replace('mage\/gallery\/gallery', 'mage/gallery/gallery', $script);
        $script = str_replace('magnifier\/magnify', 'magnifier/magnify', $script);

        return '<script type="text/x-magento-init">' . $script . '</script>';
    }
}
