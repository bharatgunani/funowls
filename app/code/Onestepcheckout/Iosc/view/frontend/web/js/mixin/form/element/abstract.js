/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    ["underscore"],
    function (_) {
        "use strict";

        return function (target) {

            var extendingObj = {};
            if (!_.isFunction(target.checkInvalid)) {
                extendingObj.checkInvalid = function () {
                    return this.error() && this.error().length ? this : null;
                };
            }

            return target.extend(extendingObj);
        };
    }
);
