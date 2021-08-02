/**
 * {{COPYRIGHT_NOTICE}}
 */
define([ "uiComponent", "uiRegistry" ], function (uiComponent, uiRegistry) {
    "use strict";
    return uiComponent.extend({
        initialize : function () {
            this.element = null;
            this.paramName = '';
            this._super();
            uiRegistry.async("checkout.sidebar.subscribe.iosc-subscribe")(
                    function (subscribe) {
                        this.element = subscribe;
                        this.paramName = this.element.inputName;

                        if (this.cnf.autoselect === "1") {
                            subscribe.checked(true);
                        }
                        uiRegistry.async('checkout.iosc.ajax')(
                            function (ajax) {
                                ajax.addMethod(
                                    'params',
                                    'subscribe',
                                    this.paramsHandler.bind(this)
                                );
                            }.bind(this)
                        );
                    }.bind(this));
        },

        paramsHandler : function () {
            var response = {};
            response[this.paramName] = this.element.value();
            return response;
        }

    });

});
