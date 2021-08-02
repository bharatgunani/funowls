/**
 * {{COPYRIGHT_NOTICE}}
 */
define([ "uiComponent", "uiRegistry" ], function (uiComponent, uiRegistry) {
    "use strict";
    return uiComponent.extend({
        initialize : function () {
            this.customerNote = null;
            this._super();
            uiRegistry.async("checkout.sidebar.comments.iosc-comment")(
                    function (customerNote) {
                        this.customerNote = customerNote;
                        uiRegistry.async('checkout.iosc.ajax')(
                                function (ajax) {
                                    ajax.addMethod(
                                        'params',
                                        'customerNote',
                                        this.paramsHandler.bind(this)
                                    );
                                }.bind(this));

                    }.bind(this));
        },

        paramsHandler : function () {
            var response = false;

            if (this.customerNote.value().length > 0) {
                response = {
                    "customerNote" : this.customerNote.value()
                };
            }

            return response;
        }

    });

});
