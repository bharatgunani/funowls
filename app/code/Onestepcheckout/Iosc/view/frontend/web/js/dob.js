/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    [
         "uiComponent",
         "uiRegistry",
         "jquery",
        "Magento_Ui/js/lib/view/utils/dom-observer",
        "Magento_Checkout/js/model/quote"
     ],
    function (uiComponent, uiRegistry, jQuery, domObserver, quote) {
    "use strict";
    return uiComponent.extend({

        initialize : function () {
            this._super();
            uiRegistry.async("checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.dob")(
                function (dobElement) {
                    this.cnf = dobElement.cnf;
                    this.setValue(dobElement.value());
                    dobElement.value.subscribe(this.setValue, dobElement, 'change');
                }.bind(this)
            );
        },

        setValue: function (value) {

            var shippingAddress =  quote.shippingAddress();

            if (!shippingAddress) {
                return;
            }

            if (typeof shippingAddress.extension_attributes === "undefined") {
                shippingAddress.extension_attributes = {};
            }

            if (typeof value !== "undefined") {
                shippingAddress.extension_attributes[this.cnf.field_id] = value;
            } else {
                shippingAddress.extension_attributes[this.cnf.field_id] = "";
            }

        }

    });

    }
);
