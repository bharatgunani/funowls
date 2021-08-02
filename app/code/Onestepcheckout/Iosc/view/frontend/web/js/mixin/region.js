/**
 * {{COPYRIGHT_NOTICE}}
 */
define(
    [ 'Magento_Ui/js/form/element/region', 'mageUtils', 'uiLayout' ],
    function (region, utils, layout) {
            'use strict';

            var inputNode = {
                parent : '${ $.$data.parentName }',
                component : 'Magento_Ui/js/form/element/abstract',
                template : '${ $.$data.template }',
                provider : '${ $.$data.provider }',
                name : '${ $.$data.index }_input',
                dataScope : '${ $.$data.customEntry }',
                customScope : '${ $.$data.customScope }',
                sortOrder : '${ $.$data.sortOrder }',
                displayArea : 'body',
                label : '${ $.$data.label }'
            };

            return region.extend({

                /**
                 * Creates input from template, renders it via renderer.
                 *
                 * @returns {Object} Chainable.
                 */
                initInput : function () {
                    var node = utils.template(inputNode, this);
                    node.additionalClasses = this.additionalClasses;
                    layout([ node ]);
                    return this;
                }

            });
    }
);
