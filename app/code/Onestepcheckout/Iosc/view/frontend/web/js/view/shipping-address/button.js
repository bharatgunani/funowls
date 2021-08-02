/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    [ "uiComponent", "Magento_Checkout/js/view/shipping", "uiRegistry", "mage/utils/wrapper" ],
    function (uiComponent, shippingView, uiRegistry, wrapper) {
        'use strict';

        return uiComponent.extend({

            initialize: function () {
                this._super();
                uiRegistry.async("checkout.steps.shipping-step.shippingAddress")(
                    function (shippingView) {
                        shippingView.showFormPopUp = wrapper.wrap(
                            shippingView.showFormPopUp,
                            this.showFormPopUp.bind(this, shippingView.showFormPopUp, shippingView)
                        );

                        this.showFormPopUp = shippingView.showFormPopUp.bind(shippingView);
                        this.isNewAddressAdded = shippingView.isNewAddressAdded.bind(shippingView);
                        this.isFormInline = shippingView.isFormInline;
                        this.shippingView = shippingView;
                        this.isFormPopUpVisible = shippingView.isFormPopUpVisible;

                    }.bind(this)
                );
            },

            showFormPopUp: function (originalMethod, shippingView) {
                if (shippingView.isFormPopUpVisible()) {
                    shippingView.isFormInline = false;
                    shippingView.isFormPopUpVisible(false);
                } else {
                    shippingView.isFormInline = true;
                    shippingView.isFormPopUpVisible(true);
                }
            },

            saveNewAddress: function () {
                this.shippingView.saveNewAddress();

                this.shippingView.source.set('params.invalid', false);
                this.shippingView.source.trigger('shippingAddress.data.validate');

                if (!this.shippingView.source.get('params.invalid')) {
                    this.shippingView.isFormPopUpVisible(false);
                    return true;
                } else {
                    return false;
                }

            },

            isRendered: function (element, index, data) {
                jQuery(element)
                    .insertAfter(jQuery('#opc-new-shipping-address'));
            }

        });
    }
);
