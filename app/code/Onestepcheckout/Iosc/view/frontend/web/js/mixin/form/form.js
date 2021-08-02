/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    ["underscore"],
    function (_) {
        "use strict";

        return function (target) {

            var extendingObj = {};
            if (!_.isFunction(target.focusInvalid)) {
                extendingObj.focusInvalid = function () {
                    var invalidField = _.find(this.delegate("checkInvalid"));
                    if (!_.isUndefined(invalidField) && _.isFunction(invalidField.focused)) {
                        invalidField.focused(true);
                    }
                };
            }

            return target.extend(extendingObj);
        };
    }
);
