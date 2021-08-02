<?php

namespace CjPublicis\UniversalTag\Model;

class CjOrder
{
    private $productLineSkus;
    private $productLineQuantities;
    private $productLinePrices;
    private $productLineDiscounts;
    private $productLineNames;
    private $enterpriseId;
    private $orderId;
    private $actionTrackerId;
    private $currencyCode;
    private $discountAmt;
    private $couponCode;
    private $customerStatus;
    private $customerId;
    private $emailHash;
    private $taxAmount;
    private $subTotal;
    private $countryCode;
    private $cjEventOrder;

    public function getProductLineSkus()
    {
        return $this->productLineSkus;
    }

    public function getProductLineQuantities()
    {
        return $this->productLineQuantities;
    }

    public function getProductLinePrices()
    {
        return $this->productLinePrices;
    }

    public function getProductLineDiscounts()
    {
        return $this->productLineDiscounts;
    }

    public function getProductLineNames()
    {
        return $this->productLineNames;
    }

    public function getEnterpriseId()
    {
        return $this->enterpriseId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getActionTrackerId()
    {
        return $this->actionTrackerId;
    }

    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    public function getDiscountAmt()
    {
        return $this->discountAmt;
    }

    public function getCouponCode()
    {
        return $this->couponCode;
    }

    public function getCustomerId()
    {
        return $this->customerId;
    }

    public function getEmailHash()
    {
        return $this->emailHash;
    }

    public function getCustomerStatus()
    {
        return $this->customerStatus;
    }

    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    public function getSubTotal()
    {
        return $this->subTotal;
    }


    public function getCountryCode()
    {
        return $this->countryCode;
    }

    public function getCjEventOrder()
    {
        return $this->cjEventOrder;
    }

    public function setProductLineSkus($_productLineSkus)
    {
        $this->productLineSkus = $_productLineSkus;
    }

    public function setProductLineQuantities($_productLineQuantities)
    {
        $this->productLineQuantities = $_productLineQuantities;
    }

    public function setProductLinePrices($_productLinePrices)
    {
       $this->productLinePrices = $_productLinePrices;
    }

    public function setProductLineDiscounts($_productLineDiscounts)
    {
       $this->productLineDiscounts = $_productLineDiscounts;
    }

    public function setProductLineNames($_productLineNames)
    {
        $this->productLineNames = $_productLineNames;
    }

    public function setEnterpriseId($_enterpriseId)
    {
        $this->enterpriseId = $_enterpriseId;
    }

    public function setOrderId($_orderId)
    {
       $this->orderId = $_orderId;
    }

    public function setActionTrackerId($_actionTrackerId)
    {
       $this->actionTrackerId = $_actionTrackerId;
    }

    public function setCurrencyCode($_currencyCode)
    {
       $this->currencyCode = $_currencyCode;
    }

    public function setDiscountAmt($_discountAmt)
    {
       $this->discountAmt = $_discountAmt;
    }

    public function setCouponCode($_couponCode)
    {
        $this->couponCode = $_couponCode;
    }

    public function setCustomerId($_customerId)
    {
       $this->customerId = $_customerId;
    }

    public function setEmailHash($_emailHash)
    {
        $this->emailHash = $_emailHash;
    }

    public function setCustomerStatus($_customerStatus)
    {
        $this->customerStatus = $_customerStatus;
    }

    public function setTaxAmount($_taxAmount)
    {
       $this->taxAmount = $_taxAmount;
    }

    public function setSubTotal($_subTotal)
    {
        $this->subTotal = $_subTotal;
    }

    public function setCountryCode($_countryCode)
    {
        $this->countryCode = $_countryCode;
    }

    public function setCjEventOrder($_cjEventOrder)
    {
        $this->cjEventOrder = $_cjEventOrder;
    }
}
