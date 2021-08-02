<?php
namespace CjPublicis\UniversalTag\Test\Unit\Helper;
use CjPublicis\UniversalTag\Helper\Data;

class DataTest extends \PHPUnit\Framework\TestCase
{
    protected $dataHelper;
    public function setUp()
    {

        $this->cjEvent = $this->getMockBuilder('CjPublicis\UniversalTag\Cookie\CjEvent')
                ->disableOriginalConstructor()
                ->getMock();

        $this->order = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfigInterface = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->http = $this->getMockBuilder('\Magento\Framework\App\Request\Http')
            ->disableOriginalConstructor()
            ->getMock();

        $this->files = $this->getMockBuilder('\Magento\Framework\App\Utility\Files')
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataHelper = new Data(
            $this->cjEvent,
            $this->order,
            $this->scopeConfigInterface,
            $this->http,
            $this->files
        );
    }

    public function testGetDefaultConfig()
    {
        $this->scopeConfigInterface->expects($this->exactly(3))
            ->method('getValue')
            ->withConsecutive(['vendor/module/enterprise_id'], ['vendor/module/action_id'], ['vendor/module/tag_id'])
            ->willReturnOnConsecutiveCalls($this->returnValue('123456'),$this->returnValue('105'), $this->returnValue('3'));

        $enterpriseIdValue = $this->dataHelper->getDefaultConfig('vendor/module/enterprise_id');
        $actionIdValue = $this->dataHelper->getDefaultConfig('vendor/module/action_id');
        $tagIdValue = $this->dataHelper->getDefaultConfig('vendor/module/tag_id');
        $this->assertEquals($enterpriseIdValue, '123456');
        $this->assertEquals($actionIdValue, '105');
        $this->assertEquals($tagIdValue, '3');
    }


    public function testGetParams()
    {

        $this->http->expects($this->once())
            ->method('getParams')
            ->will($this->returnValue(array("cjevent" => "1234")));
        $params = $this->dataHelper->getParams();
        $this->assertEquals($params, array("cjevent" => "1234"));
    }


    private function setUpOrderCollection($orderId)
    {
        $orderCollection = $this->createMock(\Magento\Reports\Model\ResourceModel\Order\Collection::class);
        $this->order->expects($this->any())->method('getCollection')->will($this->returnValue($orderCollection));

        $orderCollection
            ->expects($this->any())
            ->method('addFieldToFilter')
            ->will($this->returnValue($orderCollection));
        $orderCollection
            ->expects($this->any())
            ->method('setOrder')
            ->will($this->returnValue($orderCollection));
        $orderCollection
            ->expects($this->any())
            ->method('getFirstItem')
            ->will($this->returnValue($this->order));
        $this->order
            ->expects($this->any())
            ->method('getIncrementId')
            ->will($this->returnValue($orderId));
    }

    public function testNewCustomer()
    {
        $orderId = "101";
        $this->setUpOrderCollection($orderId);
        $customerStatus = $this->dataHelper->getCustomerStatus("1", $orderId);

        $this->assertEquals("New", $customerStatus);
    }

    public function testOldCustomer()
    {
        $orderId = "101";
        $this->setUpOrderCollection($orderId);
        $customerStatus = $this->dataHelper->getCustomerStatus("1", "102");

        $this->assertEquals("Return", $customerStatus);
    }

    public function testRoundTo2()
    {
       $amt1 = "10.0000";
       $amt2 = "10";
       $amt3 = "10.125";
       $amt4 = "-10.0000";
       $amt5 = "-10";
       $amt6 = "-10.125";
       $this->assertEquals("10.00", $this->dataHelper->roundTo2($amt1));
       $this->assertEquals("10.00", $this->dataHelper->roundTo2($amt2));
       $this->assertEquals("10.13", $this->dataHelper->roundTo2($amt3));
       $this->assertEquals("-10.00", $this->dataHelper->roundTo2($amt4));
       $this->assertEquals("-10.00", $this->dataHelper->roundTo2($amt5));
       $this->assertEquals("-10.13", $this->dataHelper->roundTo2($amt6));
    }

     public function testConvertOrderToCjOrder()
        {
            $orderId = "101";
            $orderFromApi = $this->getMockBuilder('Magento\Sales\Model\Order')
                       ->disableOriginalConstructor()
                       ->getMock();
            $orderFromApi->expects($this->any())->method('getSubTotal')->will($this->returnValue('261.0000'));
            $orderFromApi->expects($this->any())->method('getOrderCurrencyCode')->will($this->returnValue('USD'));
            $orderFromApi->expects($this->any())->method('getCouponCode')->will($this->returnValue('H20'));
            $orderFromApi->expects($this->any())->method('getCustomerId')->will($this->returnValue('1'));
            $orderFromApi->expects($this->any())->method('getCustomerEmail')->will($this->returnValue('abc@xyz.com'));
            $orderFromApi->expects($this->any())->method('getTaxAmount')->will($this->returnValue('16.8100'));
            $orderFromApi->expects($this->any())->method('getDiscountAmount')->will($this->returnValue('-57.2000'));

            $orderItem1FromApi = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
                               ->disableOriginalConstructor()
                               ->getMock();
            $orderItem2FromApi = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
                               ->disableOriginalConstructor()
                               ->getMock();
            $orderItem3FromApi = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
                               ->disableOriginalConstructor()
                               ->getMock();
            $orderItem4FromApi = $this->getMockBuilder('Magento\Sales\Model\Order\Item')
                              ->disableOriginalConstructor()
                              ->getMock();
            $shippingAddress = $this->getMockBuilder('Magento\Customer\Model\Address')
                                ->disableOriginalConstructor()
                                ->setMethods(['getCountryId'])
                                ->getMock();
            $orderItem1FromApi->expects($this->any())->method('getSku')->will($this->returnValue('A-sku'));
            $orderItem1FromApi->expects($this->any())->method('getName')->will($this->returnValue('A-name'));
            $orderItem1FromApi->expects($this->any())->method('getQtyOrdered')->will($this->returnValue('1.0000'));
            $orderItem1FromApi->expects($this->any())->method('getPrice')->will($this->returnValue('99.0000'));
            $orderItem1FromApi->expects($this->any())->method('getDiscountAmount')->will($this->returnValue('19.8000'));
            $orderItem2FromApi->expects($this->any())->method('getSku')->will($this->returnValue('B-sku'));
            $orderItem2FromApi->expects($this->any())->method('getName')->will($this->returnValue('B-name'));
            $orderItem2FromApi->expects($this->any())->method('getQtyOrdered')->will($this->returnValue('1.0000'));
            $orderItem2FromApi->expects($this->any())->method('getPrice')->will($this->returnValue('72.0000'));
            $orderItem2FromApi->expects($this->any())->method('getDiscountAmount')->will($this->returnValue('14.4000'));
            $orderItem3FromApi->expects($this->any())->method('getSku')->will($this->returnValue('C-sku'));
            $orderItem3FromApi->expects($this->any())->method('getName')->will($this->returnValue('C-name'));
            $orderItem3FromApi->expects($this->any())->method('getQtyOrdered')->will($this->returnValue('1.0000'));
            $orderItem3FromApi->expects($this->any())->method('getPrice')->will($this->returnValue('65.0000'));
            $orderItem3FromApi->expects($this->any())->method('getDiscountAmount')->will($this->returnValue('13.0000'));
            $orderItem4FromApi->expects($this->any())->method('getSku')->will($this->returnValue('D-sku'));
            $orderItem4FromApi->expects($this->any())->method('getName')->will($this->returnValue('D-name'));
            $orderItem4FromApi->expects($this->any())->method('getQtyOrdered')->will($this->returnValue('1.0000'));
            $orderItem4FromApi->expects($this->any())->method('getPrice')->will($this->returnValue('25.0000'));
            $orderItem4FromApi->expects($this->any())->method('getDiscountAmount')->will($this->returnValue('10.0000'));
            $orderFromApi->expects($this->any())->method('getAllVisibleItems')->will($this->returnValue([$orderItem1FromApi,$orderItem2FromApi,$orderItem3FromApi,$orderItem4FromApi]));
            $orderFromApi->expects($this->any())->method('getItems')->will($this->returnValue([$orderItem1FromApi,$orderItem2FromApi,$orderItem3FromApi,$orderItem4FromApi]));
            $orderFromApi->expects($this->any())->method('getShippingAddress')->will($this->returnValue($shippingAddress));
            $shippingAddress->expects($this->any())->method('getCountryId')->will($this->returnValue('US'));

            $this->scopeConfigInterface->expects($this->exactly(3))
                            ->method('getValue')
                            ->withConsecutive(['universaltag/general/enterprise_id'], ['universaltag/general/action_id'], ['universaltag/general/customer_status'])
                            ->willReturnOnConsecutiveCalls($this->returnValue('123456'), $this->returnValue('105'), $this->returnValue('1'));
            $orderNewCollection = $this->createMock(\Magento\Reports\Model\ResourceModel\Order\Collection::class);
            $orderFromApi->expects($this->any())->method('getCollection')->will($this->returnValue($orderNewCollection));
            $orderFromApi->expects($this->any())->method('getCustomerEmail')->will($this->returnValue('abc@xyz.com'));
            $orderNewCollection->expects($this->any())->method('addFieldToFilter')->will($this->returnValue($orderNewCollection));
            $orderNewCollection->expects($this->any())->method('setOrder')->will($this->returnValue($orderNewCollection));
            $orderNewCollection->expects($this->any())->method('getFirstItem')->will($this->returnValue($orderFromApi));
            $orderFromApi->expects($this->any())->method('getIncrementId')->will($this->returnValue($orderId));
            $dataHelperNew = new Data(
                            $this->cjEvent,
                            $orderFromApi,
                            $this->scopeConfigInterface,
                            $this->http,
                            $this->files
                        );
                $cjOrder = $dataHelperNew->convertOrderToCjOrder($orderFromApi);
                $this->assertEquals("USD", $cjOrder->getCurrencyCode());
                $this->assertEquals("H20", $cjOrder->getCouponCode());
                $this->assertEquals("1", $cjOrder->getCustomerId());
                $this->assertEquals("ee278943de84e5d6243578ee1a1057bcce0e50daad9755f45dfa64b60b13bc5d", $cjOrder->getEmailHash());
                $this->assertEquals("261.00", $cjOrder->getSubTotal());
                $this->assertEquals("16.81", $cjOrder->getTaxAmount());
                $this->assertEquals("0.00", $cjOrder->getDiscountAmt());

        }
}


