/**
 * {{COPYRIGHT_NOTICE}}
 */
define([ "uiComponent", "uiRegistry", "ko", "jquery", 'Magento_Checkout/js/model/payment/additional-validators' ], function (uiComponent, uiRegistry, ko, jQuery, additionalValidators) {
    "use strict";
    return uiComponent.extend({
        initialize : function () {
            this._super();
        },

        placeOrder: function () {

            uiRegistry.async("checkout.steps.shipping-step.shippingAddress")(
                function (shippingValidation) {
                    var methodsAvailable = shippingValidation.rates().length;
                    if (methodsAvailable <= 0) {
                        uiRegistry.async("checkout.steps.shipping-step.shippingAddress.before-shipping-method-form.iosc_shipping_validationmessage")(
                            function (shippingValidation) {
                                if (typeof shippingValidation.setValidationMessage === "function") {
                                    shippingValidation.setValidationMessage(jQuery.mage.__("Please specify address info to get rates!"));
                                }
                            }
                        );
                    } else {
                        if (typeof shippingValidation.setValidationMessage !== "undefined") {
                            shippingValidation.setValidationMessage(false);
                        }
                    }
                }
            );

            uiRegistry.async("checkout.steps.billing-step.payment.afterMethods.iosc-payment-validationmessage")(
                function (paymentValidation) {
                    var methodSelected = paymentValidation.validatePaymentMethods();
                    if (!methodSelected) {
                        paymentValidation.setValidationMessage(jQuery.mage.__("Please choose a payment method!"));
                    } else {
                        paymentValidation.setValidationMessage(false);
                    }
                }
            );

            additionalValidators.validate();
        }
    });

});
