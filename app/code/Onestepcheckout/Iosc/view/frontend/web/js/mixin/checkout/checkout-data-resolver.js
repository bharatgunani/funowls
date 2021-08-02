/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    [
         "Magento_Checkout/js/model/quote",
         "Magento_Checkout/js/checkout-data",
         "Magento_Checkout/js/action/select-shipping-method",
         "underscore",
         "mage/utils/wrapper"
     ],
    function (
        quote,
        checkoutData,
        selectShippingMethodAction,
        _,
        wrapper
    ) {
        "use strict";

        return function (target) {

            target.resolveShippingRates = wrapper.wrap(target.resolveShippingRates, function (originalmethod, ratesData) {
                var selectedShippingRate = checkoutData.getSelectedShippingRate(),
                availableRate = false;

                if (quote.shippingMethod()) {
                    availableRate = _.find(ratesData, function (rate) {
                        return rate['carrier_code'] == quote.shippingMethod()['carrier_code'] && //eslint-disable-line
                            rate['method_code'] == quote.shippingMethod()['method_code']; //eslint-disable-line eqeqeq
                    });
                }

                if (!availableRate && selectedShippingRate) {
                    availableRate = _.find(ratesData, function (rate) {
                        return rate['carrier_code'] + '_' + rate['method_code'] === selectedShippingRate;
                    });
                }

                if (!availableRate && window.checkoutConfig.selectedShippingMethod) {
                    availableRate = window.checkoutConfig.selectedShippingMethod;
                    selectShippingMethodAction(window.checkoutConfig.selectedShippingMethod);

                    return;
                }
                if (!availableRate) {
                    selectShippingMethodAction(null);
                } else {
                    selectShippingMethodAction(availableRate);
                }
            });

            return target;
        };
    }
);
