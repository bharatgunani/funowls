define([
    'jquery',
    'mage/storage',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/error-processor'
], function ($, storage, wrapper, quote, shippingService, rateRegistry, customerAddressProcessor, newAddressProcessor, getTotalsAction, customer, errorProcessor) {
    $(document).on('change',"[name='postcode']",function(){
            console.log('postcode changed reload totals');

            var addressInformation;
            if(customer.isLoggedIn()){
                var serviceUrl = 'rest/default/V1/carts/mine/shipping-information';
            }else{
                var serviceUrl = 'rest/default/V1/guest-carts/'+quote.getQuoteId()+'/shipping-information';
            }
            
            var payload = JSON.stringify({
                    addressInformation : {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code
                    }
                }
            );
            storage.post(
            serviceUrl, payload, false
            ).done(
                function (result) {
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            );
    });

    $(document).on('change',"[name='region_id']",function(){
            console.log('region changed reload totals');

            var addressInformation;
            if(customer.isLoggedIn()){
                var serviceUrl = 'rest/default/V1/carts/mine/shipping-information';
            }else{
                var serviceUrl = 'rest/default/V1/guest-carts/'+quote.getQuoteId()+'/shipping-information';
            }
            
            var payload = JSON.stringify({
                    addressInformation : {
                        shipping_address: quote.shippingAddress(),
                        billing_address: quote.billingAddress(),
                        shipping_method_code: quote.shippingMethod().method_code,
                        shipping_carrier_code: quote.shippingMethod().carrier_code
                    }
                }
            );
            storage.post(
            serviceUrl, payload, false
            ).done(
                function (result) {
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                }
            );
    });
     return function (setShippingInformationAction) {
         return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
             return originalAction(); 
         });
     };
});
