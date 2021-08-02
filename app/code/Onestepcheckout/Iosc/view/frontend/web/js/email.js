/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    [
     "uiComponent",
     "uiRegistry",
     "Magento_Customer/js/model/customer",
     "jquery",
     "Magento_Ui/js/lib/view/utils/dom-observer"

     ],
    function (
        uiComponent,
        uiRegistry,
        customer,
        jQuery,
        domObserver
    ) {
     
    "use strict";
    return uiComponent.extend({
        initialize : function () {
            this.customerEmail = null;
            this._super();

            if (!customer.isLoggedIn()) {
                uiRegistry.async("checkout.steps.shipping-step.shippingAddress.customer-email")(
                    function (customerEmail) {
                        domObserver.get(
                            "#checkoutSteps input[type='email']",
                            function (elem) {
                                this.customerEmail = customerEmail;
                                uiRegistry.async('checkout.iosc.ajax')(
                                    function (ajax) {
                                        ajax.addMethod(
                                            'params',
                                            'customerEmail',
                                            this.paramsHandler.bind(this)
                                        );
                                    }.bind(this)
                                );
                            }.bind(this)
                        );
                    }.bind(this)
                );
            }

        },

        paramsHandler : function () {
            var response = false;
            var isValid;

            isValid = this.customerEmail.validateEmail(false);

            if (isValid) {
                response = this.customerEmail.email();
            }

            return response;
        }

    });

    }
);
