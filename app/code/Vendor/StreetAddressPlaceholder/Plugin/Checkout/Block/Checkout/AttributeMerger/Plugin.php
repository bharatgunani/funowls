<?php
namespace Vendor\StreetAddressPlaceholder\Plugin\Checkout\Block\Checkout\AttributeMerger;

class Plugin
{
  public function afterMerge(\Magento\Checkout\Block\Checkout\AttributeMerger $subject, $result)
  {

    if (array_key_exists('street', $result)) {
      $result['street']['children'][0]['placeholder'] = __('Address Line 1');
      $result['street']['children'][1]['placeholder'] = __('(Optional: Apt, Building, etc.)');
      $result['street']['children'][2]['placeholder'] = __('Landmark');
    }

    if (array_key_exists('prefix', $result)) {
      $result['prefix']['placeholder'] = __('Title');
    }

    if (array_key_exists('firstname', $result)) {
      $result['firstname']['placeholder'] = __('First Name');
    }
    
    if (array_key_exists('lastname', $result)) {
      $result['lastname']['placeholder'] = __('Last Name');
    }

    if (array_key_exists('telephone', $result)) {
      $result['telephone']['placeholder'] = __('Phone Number');
    }    

    if (array_key_exists('region_id', $result)) {
      $result['region_id']['placeholder'] = __('State/Province');
    }

    if (array_key_exists('city', $result)) {
      $result['city']['placeholder'] = __('City');
    }

    if (array_key_exists('postcode', $result)) {
      $result['postcode']['placeholder'] = __('Zip/Postal Code');
    }

    return $result;

   }

}