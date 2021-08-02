/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    [
      "mage/utils/wrapper",
      "ko",
      "jquery",
      "Magento_Ui/js/modal/modal",
      "Magento_Checkout/js/model/full-screen-loader",
      "uiRegistry"
    ],
    function (wrapper, ko, jQuery, modal, fullScreenLoader, uiRegistry) {
        "use strict";

        return function (target) {
            var extendingObj = {};
            extendingObj.isPlaceOrderActionAllowed = ko.observable(true);
            extendingObj.initialize = function () {
                this._super();
                this.afterPlaceOrder = wrapper.wrap(this.afterPlaceOrder , function (originalMethod) {
                    if (arguments.length >= 1) {
                        var args = [].slice.call(arguments);
                        args.shift();
                        originalMethod.apply(this, args);
                    } else {
                        originalMethod.apply(this);
                    }
                    if (this.template === "Magento_Paypal/payment/iframe-methods") {
                        if (this.paymentReady() == true && this.iframeIsLoaded === true) {
                            var iframeWarning = jQuery("#iframe-warning");
                            if (iframeWarning) {
                                iframeWarning.parent("div").addClass("iframe-container");
                                iframeWarning.parent("div").modal({
                                    "autoOpen" : true,
                                    "clickableOverlay": false,
                                    "modalClass": "iframe-payment-modal",
                                    "type": "slide",
                                    "buttons": [{}]
                                });
                            }
                        }
                    }

                }.bind(this));
            };
            return target.extend(extendingObj);
        };
    }
);
