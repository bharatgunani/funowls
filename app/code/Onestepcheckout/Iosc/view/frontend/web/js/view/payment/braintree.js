/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    [
    "uiComponent",
    "uiRegistry",
    "ko",
    "jquery",
    "Magento_Checkout/js/model/full-screen-loader",
    "mage/utils/wrapper",
    "Magento_Checkout/js/model/payment/additional-validators",
    "Magento_Braintree/js/view/payment/adapter",
    "Magento_Checkout/js/model/quote"
    ],
    function (uiComponent, uiRegistry, ko, jQuery, fullScreenLoader, wrapper, additionalValidators, Braintree, quote) {
    "use strict";

    return uiComponent.extend({
        initialize: function () {
            this._super();
            uiRegistry.async("checkout.iosc.payments")(
                function (payments) {
                    if(typeof payments.getComponentButtonElem === 'function' ) {
                        payments.getComponentButtonElem = wrapper.wrap(
                            payments.getComponentButtonElem,
                            function(originalMethod, buttonElem) {
                               if(buttonElem.id === "braintree_paypal"){
                                   return jQuery(buttonElem).parents(".payment-method")
                                   .find(".actions-toolbar:not([style*='display: none']) button.action.primary.checkout:not(.disabled)").first();
                               }
                               return originalMethod();
                            }
                        );
                    }
                    if(typeof payments.getComponentFromButton === 'function' ) {
                        payments.getComponentFromButton = wrapper.wrap(
                            payments.getComponentFromButton,
                            function(originalMethod, buttonElem) {
                                var component = originalMethod(buttonElem);
                                if(typeof component.modules.hostedFields !== 'undefined'
                                    && component.modules.hostedFields == "checkout.steps.billing-step.payment.payments-list.braintree"
                                ){
                                    component = uiRegistry.get(component.modules.hostedFields);
                                };
                                return component;
                            }
                        );
                    }

                }.bind(this)
            );
            uiRegistry.async("checkout.steps.billing-step.payment.payments-list.braintree_paypal")(
                function (braintreePayment) {
                    if(typeof braintreePayment.payWithPayPal === 'function' ) {
                        braintreePayment.payWithPayPal = wrapper.wrap(
                            braintreePayment.payWithPayPal,
                            function(originalMethod) {
                                if (!additionalValidators.validate()) {
                                    braintreePayment.enableButton();
                                    return;
                                }
                                setTimeout(function(){
                                    try {
                                        Braintree.checkout.paypal.initAuthFlow();
                                    } catch (e) {
                                        braintreePayment.messageContainer.addErrorMessage({
                                            message: $t('Payment ' + braintreePayment.getTitle() + ' can\'t be initialized.')
                                        });
                                        braintreePayment.enableButton();
                                    }
                                }, 1000);
                            }.bind(braintreePayment)
                        );
                    }
                    if(typeof braintreePayment.getPlaceOrderDeferredObject === 'function' ) {
                        braintreePayment.getPlaceOrderDeferredObject = wrapper.wrap(
                            braintreePayment.getPlaceOrderDeferredObject,
                            function(originalMethod) {
                                fullScreenLoader.startLoader();
                                braintreePayment.isPlaceOrderActionAllowed(false);
                                var deferred = originalMethod();
                                fullScreenLoader.startLoader();
                                braintreePayment.isPlaceOrderActionAllowed(false);
                                return deferred;
                            }
                        );
                    }
                    if(typeof braintreePayment.getShippingAddress === 'function' ) {
                        braintreePayment.getShippingAddress = wrapper.wrap(
                            braintreePayment.getShippingAddress,
                            function(originalMethod) {
                                var address = quote.shippingAddress();
                                var response = {};
                                if (address.postcode === null || typeof address.street === "undefined") {
                                    response = {};
                                } else {
                                    response = originalMethod();
                                }
                               return response;
                            }
                        );
                    }
                }
            );
            uiRegistry.async("checkout.steps.billing-step.payment.payments-list.braintree")(
                function (braintreePayment) {

                    if(typeof braintreePayment.validateCardType === 'function' ) {
                        braintreePayment.validateCardType = wrapper.wrap(
                            braintreePayment.validateCardType,
                            function(originalMethod) {
                                var result = originalMethod();
                                if(result === false) {
                                    braintreePayment.isPlaceOrderActionAllowed(true)
                                }
                                return result;
                            }
                        );
                    }

                    if(typeof braintreePayment.getPlaceOrderDeferredObject === 'function' ) {
                        braintreePayment.getPlaceOrderDeferredObject = wrapper.wrap(
                            braintreePayment.getPlaceOrderDeferredObject,
                            function(originalMethod) {
                                fullScreenLoader.startLoader();
                                braintreePayment.isPlaceOrderActionAllowed(false);
                                var deferred = originalMethod();
                                fullScreenLoader.startLoader();
                                braintreePayment.isPlaceOrderActionAllowed(false);
                                return deferred;
                            }
                        );
                    }

                }.bind(this)
            );
        }
    });
});
