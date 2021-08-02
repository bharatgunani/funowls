<?php

namespace CjPublicis\UniversalTag\Helper;

use CjPublicis\UniversalTag\Model\CjOrder;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MODULE_NAME = 'CjPublicis_UniversalTag';
    private $cjEvent;
    private $scopeConfigInterface;
    private $order;
    private $httpRequest;
    private $files;
    private $resourceConnection;

    public function __construct(
        \CjPublicis\UniversalTag\Cookie\CjEvent $cjEvent,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        \Magento\Framework\App\Request\Http $httpRequest,
        \Magento\Framework\App\Utility\Files $files,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
    {
        $this->cjEvent = $cjEvent;
        $this->order = $order;
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->httpRequest = $httpRequest;
        $this->files = $files;
        $this->resourceConnection = $resourceConnection;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function getCjEventOrderValue()
    {
        return $this->cjEvent->get();
    }

    public function getParams()
    {
        return $this->httpRequest->getParams();
    }

    public function getDefaultConfig($config_path)
    {
        return $this->scopeConfigInterface->getValue(
            $config_path
        );
    }

    public function getStoreConfig($config_path)
    {
        return $this->scopeConfigInterface->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getWebsiteConfig($config_path)
    {
        return $this->scopeConfigInterface->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getCustomerStatus($customerStatusFlag, $incrementId, $emailTrimmed)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['so' => 'sales_order'], ['customer_id', 'customer_email', 'increment_id']
            )
            ->where('customer_email = ?', $emailTrimmed)
            ->order('entity_id ASC')
            ->limit(1);
        $customerStatus = '';
        if ($customerStatusFlag === '1') {
            $resultSet = $connection->fetchAll($select);
            if ($resultSet[0]['increment_id'] == $incrementId):
                $customerStatus = 'New';
            else:
                $customerStatus = 'Return';
            endif;
        }
        return $customerStatus;
    }

    public function convertOrderToCjOrder($incrementId)
    {
        $cjOrder = new CjOrder();
        $connection = $this->resourceConnection->getConnection();
        $shippingAddressType = "'shipping'";
        $select = $connection->select()
            ->from(
                ['so' => 'sales_order'], ['so.customer_id', 'so.customer_email', 'so.order_currency_code', 'so.discount_amount', 'so.subtotal', 'so.coupon_code', 'so.tax_amount']
            )
            ->join(
                ['soi' => 'sales_order_item'],
                'soi.order_id = so.entity_id AND soi.parent_item_id is NULL',
                ['line_sku' => 'soi.sku', 'line_price' => 'soi.price', 'line_name' => 'soi.name', 'line_discount_amount' => 'soi.discount_amount', 'line_qty_ordered' => 'soi.qty_ordered']
            )
            ->join(
                ['soa' => 'sales_order_address'],
                'soa.parent_id = so.entity_id AND soa.address_type =' . $shippingAddressType,
                ['soa.country_id']
            )
            ->where('increment_id = ?', $incrementId);
        $resultSet = $connection->fetchAll($select);
        $cjOrder->setEnterpriseId($this->getStoreConfig('universaltag/general/enterprise_id'));
        $cjOrder->setActionTrackerId($this->getStoreConfig('universaltag/general/action_id'));
        $customerStatusFlag = $this->getStoreConfig('universaltag/general/customer_status');
        $_productLineSkus = [];
        $_productLineNames = [];
        $_productLineQuantities = [];
        $_productLinePrices = [];
        $_productLineDiscounts = [];
        $index = 0;

        for ($i = 0; $i < count($resultSet); $i++) {
            if ($i == 0) {
                $cjOrder->setSubTotal($this->roundTo2($resultSet[$i]['subtotal']));
                $cjOrder->setCurrencyCode($resultSet[$i]['order_currency_code']);
                $cjOrder->setDiscountAmt($resultSet[$i]['discount_amount']);
                $cjOrder->setTaxAmount($this->roundTo2($resultSet[$i]['tax_amount']));
                $cjOrder->setCouponCode($resultSet[$i]['coupon_code']);
                $cjOrder->setCustomerId($resultSet[$i]['customer_id']);
                $cjOrder->setCountryCode($resultSet[$i]['country_id']);
                $emailTrimmed = trim($resultSet[$i]['customer_email']);
                $cjOrder->setEmailHash(hash("sha256", $emailTrimmed));
            }
            $_productLineSkus[$index] = $resultSet[$i]['line_sku'];
            $_productLineNames[$index] = $resultSet[$i]['line_name'];
            $_productLineQuantities[$index] = intval($resultSet[$i]['line_qty_ordered']);
            $_productLinePrices[$index] = $this->roundTo2($resultSet[$i]['line_price']);
            $_productLineDiscounts[$index] = $this->roundTo2($resultSet[$i]['line_discount_amount']);

            $index += 1;
        }

        $orderDiscountAmount = $cjOrder->getDiscountAmt();
        foreach ($_productLineDiscounts as $itemDiscount) {
            $orderDiscountAmount += $itemDiscount;
        }

        $cjOrder->setDiscountAmt($this->roundTo2($orderDiscountAmount));
        $cjOrder->setProductLineSkus($_productLineSkus);
        $cjOrder->setProductLineNames($_productLineNames);
        $cjOrder->setProductLineQuantities($_productLineQuantities);
        $cjOrder->setProductLinePrices($_productLinePrices);
        $cjOrder->setProductLineDiscounts($_productLineDiscounts);

        $customerStatus = $this->getCustomerStatus($customerStatusFlag, $incrementId, $emailTrimmed);
        $cjOrder->setCustomerStatus($customerStatus);
        $cjOrder->setCjEventOrder($this->getCjEventOrderValue());
        return $cjOrder;
    }

    public function roundTo2($amt)
    {
        return number_format((float)$amt, 2, '.', '');
    }

    public function getExtensionVersion()
    {
        $pathToNeededModule = '';

        $composerFilePaths = array_keys(
            $this->files->getComposerFiles(\Magento\Framework\Component\ComponentRegistrar::MODULE)
        );

        foreach ($composerFilePaths as $path) {
            if (strpos($path, "CjPublicis/UniversalTag/") !== false || strpos($path, "universal-tag-module") !== false) {
                $pathToNeededModule = $path;
                break;
            }
        }
        if ($pathToNeededModule) {
            $content = file_get_contents(BP . '/' . $pathToNeededModule);
            if ($content) {
                $jsonContent = json_decode($content, true);

                if (!empty($jsonContent['version'])) {
                    return $jsonContent['version'];
                }
            }
        }
        return null;
    }
}
