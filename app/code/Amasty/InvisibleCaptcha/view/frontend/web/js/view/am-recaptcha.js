define([
    'Amasty_InvisibleCaptcha/js/view/am-recaptcha-abstract',
    'jquery',
    'ko',
    'underscore',
    'mageUtils',
    'Amasty_InvisibleCaptcha/js/model/am-recaptcha',
    'mage/loader',
    'domReady!'
], function (
    Component,
    $,
    ko,
    _,
    utils,
    amReCaptchaModel
) {
    'use strict';

    return Component.extend({
        defaults: {
            formsToProtect: '',
            reCaptchaId: 'am-recaptcha-place-order'
        },

        /**
         * @inheritDoc
         * @returns {Object}
         */
        initObservable: function () {
            this._super();

            this.formsToProtect = $(amReCaptchaModel.getFormsList());
            this.renderCaptcha();

            return this;
        },

        /**
         * @returns {void}
         */
        renderCaptcha: function () {
            $(window).on('recaptchaapiready', this.initFormHandler.bind(this));

            // eslint-disable-next-line consistent-return
            _.debounce(function () {
                this.formsToProtect.on('submit', function (event) {
                    if (amReCaptchaModel.isScriptLoaded) {
                        return;
                    }

                    event.preventDefault();
                    event.stopImmediatePropagation();

                    this.firstSubmittedForm = $(event.target);
                    this.loadApi();
                }.bind(this));

                this._eventOrderChange();
            }.bind(this), 200)();
        },

        /**
         * @private
         * @returns {void}
         */
        _eventOrderChange: function () {
            _.each(this.formsToProtect, function (form) {
                var $form = $(form);

                $form.data('recaptchaFormId', utils.uniqueid());

                this._swapSubmit($form);
            }.bind(this));
        },

        /**
         * @param {Element} form
         * @private
         * @returns {void}
         */
        _swapSubmit: function (form) {
            var $form = $(form),
                listeners;

            listeners = $._data($form[0], 'events').submit;
            listeners.unshift(listeners.pop());
        },

        /**
         * @param {Element} tokenField
         * @param {Element} form
         * @returns {Object}
         */
        getParameters: function (tokenField, form) {
            return _.extend(amReCaptchaModel.getRecaptchaConfig(), {
                'callback': function (token) {
                    if (this.showLoaderOnCaptchaLoading) {
                        $('body').trigger('processStop');
                    }

                    tokenField.val(token);
                    $(form).submit();
                }.bind(this),
                'expired-callback': this.resetCaptcha
            });
        },

        /**
         * Append hidden input into each form
         * @returns {void}
         */
        initFormHandler: function () {
            _.each(this.formsToProtect, function (form) {
                var $form = $(form),
                    widgetId,
                    tokenField = $('<input type="hidden" name="amasty_invisible_token">'),
                    $button = $form.find("[type='submit']");

                this.appendCaptcha();

                $form.append(tokenField);

                widgetId = window.grecaptcha.render($button[0], this.getParameters(tokenField, $form));

                $form.submit(function (event) {
                    if (!tokenField.val() && $(event.target).valid()) {
                        window.grecaptcha.execute(widgetId);

                        event.preventDefault();
                        event.stopImmediatePropagation();
                    }
                });

                this._swapSubmit($form);

                amReCaptchaModel.tokenFields.push(tokenField);

                if (this.firstSubmittedForm
                    && this.firstSubmittedForm.data('recaptchaFormId') === $form.data('recaptchaFormId')) {
                    if (this.showLoaderOnCaptchaLoading) {
                        $('body').trigger('processStart');
                    }

                    window.grecaptcha.execute(widgetId);

                    this.firstSubmittedForm = null;
                }
            }.bind(this));
        }
    });
});
