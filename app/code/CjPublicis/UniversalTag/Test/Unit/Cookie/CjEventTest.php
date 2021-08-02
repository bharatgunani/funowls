<?php
namespace CjPublicis\UniversalTag\Test\Unit\Cookie;
use CjPublicis\UniversalTag\Cookie\CjEvent;

class CjEventTest extends \PHPUnit\Framework\TestCase
{
    protected $cjEvent;
    public function setUp()
    {

        $this->cookieManagerInterface = $this->getMockBuilder('Magento\Framework\Stdlib\CookieManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->cookieMetadataFactory = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\CookieMetadataFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->publicCookieMetadata = $this->getMockBuilder('Magento\Framework\Stdlib\Cookie\PublicCookieMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->cjEvent = new CjEvent(
            $this->cookieManagerInterface,
            $this->cookieMetadataFactory
        );
     }

     public function testGet()
     {
        $this->cookieManagerInterface->expects($this->once())
                    ->method('getCookie')
                    ->with($this->equalTo(\CjPublicis\UniversalTag\Cookie\CjEvent::COOKIE_NAME))
                    ->will($this->returnValue('1234'));

       $actualValue = $this->cjEvent->get();
       $this->assertEquals($actualValue, '1234');
     }


     public function testSet()
      {
         $this->cookieMetadataFactory->expects($this->once())
                     ->method('createPublicCookieMetadata')
                     ->will($this->returnValue($this->publicCookieMetadata));

         $this->publicCookieMetadata->expects($this->once())
                    ->method('setDurationOneYear')
                    ->will($this->returnValue($this->publicCookieMetadata));

         $this->publicCookieMetadata->expects($this->once())
                     ->method('setPath')
                     ->with($this->equalTo('/'))
                     ->will($this->returnValue($this->publicCookieMetadata));

         $this->publicCookieMetadata->expects($this->once())
                      ->method('setHttpOnly')
                      ->with($this->equalTo(false))
                      ->will($this->returnValue($this->publicCookieMetadata));

        $this->publicCookieMetadata->expects($this->once())
                       ->method('setSecure')
                       ->with($this->equalTo(false))
                       ->will($this->returnValue($this->publicCookieMetadata));

        $this->cjEvent->set('1234');
        $this->cookieManagerInterface->expects($this->once())
                            ->method('getCookie')
                            ->with($this->equalTo(\CjPublicis\UniversalTag\Cookie\CjEvent::COOKIE_NAME))
                            ->will($this->returnValue('1234'));
        $actualValue = $this->cjEvent->get();
        $this->assertEquals($actualValue, '1234');
      }

}
