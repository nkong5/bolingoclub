var awEventBooking = Class.create();
awEventBooking.prototype = {
    initialize: function(config){
        this.popup = $$(config.popupCssSelector).first();
        this.optionsConfig = config.optionsConfig;
        this.ticketTitles = config.ticketTitles;
        this.overlay = $$(config.overlaySelector).first();
        this.popupClassName = config.popupClassName;
        this.optionsSelector = config.optionsSelector;
        this.buttonText = config.buttonText;
        this.popupTitle = config.popupTitle;
        this.submitPopupFn = config.submitPopupFn;
        this.optionsFormId = 'aw_eventbooking_options_form';
        this.customDataInput = $$(config.customDataInputSelector).first();
        this.nameLabel = config.nameLabel;
        this.emailLabel = config.emailLabel;
        this.displayEmail = config.displayEmail;
        this.optionsForm = null;

        this.init();
    },
    
    init: function(){
        this.overlay.observe('click', this.hidePopup.bind(this));
        this._disableQtyInput();
    },
    
    showPopup: function() {
        var me = this;
        this._createPopup();
        this._resizePopup(this.popup);

        this.overlay.show();
        this.popup.show();
        this.popup.onWindowResizeHandler = function(e) {
            me._resizePopup(me.popup);
        };
        Event.observe(window, 'resize', this.popup.onWindowResizeHandler);
    },
    
    hidePopup: function() {
        this.overlay.hide();
        this.popup.hide();
    },

    submitAddToCartForm: function() {
        if (this.optionsForm.validator.validate()) {
            var value = $(this.optionsFormId).serialize(true);
            $('product_addtocart_form').insert(this.customDataInput);
            this.customDataInput.value = JSON.stringify(value);
            this.submitPopupFn();
            this.hidePopup();
        }
    },
    
    _createPopup: function() {
        this.popup.addClassName(this.popupClassName);
        this.popup.innerHTML = this._generatePopupContent();
        this.optionsForm = new VarienForm(this.optionsFormId);

    },
    
    _generatePopupContent: function() {
        var me = this;
        var optionsInserted = false;
        var popupContent = '';
        $$(this.optionsSelector).each(function(element) {
            var optionId = 0;
            element.name.sub(/[0-9\_]+/, function (match) {
                optionId = match[0];
            });
            var qtyToBuy = parseInt(element.value);
            element.value = qtyToBuy;
            if (me.optionsConfig[optionId] && element.type == 'text' && qtyToBuy) {
                for (var i = 1; i <= qtyToBuy; i++) {
                    var htmlOptionId = optionId + '_c' + i ;
                    popupContent += me._generateRowOptionHtml(me.ticketTitles[optionId], htmlOptionId, i);
                    optionsInserted = true;
                }
            }
        });

        if (optionsInserted) {
            popupContent = this._generateCloseButton()
                + '<div class="aw-eb-popup-title">' + this.popupTitle + '</div>'
                + '<form id="' + this.optionsFormId + '">'
                + popupContent
                + '</form>';
            popupContent += this._generateContinueButton();
        }
        return popupContent;
    },

    _generateRowOptionHtml: function(optionTitle, htmlOptionId, number) {
        var rowHtml = '<div class="fieldset">'
        + '<span class="legend" style="border-bottom: none;">'+optionTitle + ' #'+ number +'</span>'
        + '<ul class="form-list">'
        + '<li class="fields">'
        + '<label for="firstname" class="required"><em>*</em>' + this.nameLabel + '</label>'
        + '<div class="input-box">'
        + '<input type="text" name="' + htmlOptionId + '_name" id="' + htmlOptionId + '_name" title="Name" class="input-text required-entry">'
        + '</div>'
        + '</li>';
        if (this.displayEmail) {
            rowHtml += '<li>'
            + '<label for="email" class="required"><em>*</em>' + this.emailLabel + '</label>'
            + '<div class="input-box">'
            + '<input type="email" name="' + htmlOptionId + '_email" id="' + htmlOptionId + '_email" title="Email" class="input-text required-entry validate-email">'
            + '</div>'
            + '</li>';
        }
        rowHtml += '</ul></div>';

        return rowHtml;
    },

    _generateContinueButton: function() {
        var button = '<div class="aw_eventbooing_buttons">'
            + '<button type="button" onclick="awEventBookingInstance.submitAddToCartForm();" class="aw-eb-continue_button button" >'
            + '<span>'
            + this.buttonText + '</span></button></div>';
        return button;
    },

    _generateCloseButton: function() {
        var button = '<span onclick="awEventBookingInstance.hidePopup();" class="aw-eb-close_button" ></span>';
        return button;
    },

    _resizePopup: function(el) {
        el.setStyle({
            height: 'auto',
            width: 'auto'
        });
        var docWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        var docHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        if ((docHeight - el.getHeight()) < 100) {
            el.setStyle({
                height: (docHeight - 100) + 'px'
            });
        }
        if (docWidth - el.getWidth() < 50) {
            el.setStyle({
                width: (docWidth - 30) + 'px'
            });
        } else {
            el.setStyle({
                width: el.getWidth() + 'px'
            });
        }
        var left = docWidth/2 - el.getWidth()/2;
        var top = docHeight/2 - el.getHeight()/2;
        var isIOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
        if (isIOS) {
            el.setStyle({'position': 'absolute'});
            left += window.pageXOffset ? window.pageXOffset : 0;
            top += window.pageYOffset ? window.pageYOffset : 0;
        }
        el.setStyle({
            'left': left + 'px',
            'top': top + 'px'
        });
    },

    _disableQtyInput: function() {
        var qtyInput = $$('.add-to-cart input[name="qty"]').first();
        if (qtyInput) {
            qtyInput.disabled = true;
            qtyInput.setStyle({'opacity': 0.5});

        }
    },

    validateOptionsQty: function() {
        var isValid = false;
        $$(this.optionsSelector).each(function(element) {
            if (parseInt(element.value)) {
                isValid = true;
            }
        });
        $$(this.optionsSelector).each(function(element) {
            if (!isValid) {
                element.addClassName('validation-failed');
            } else {
                element.removeClassName('validation-failed');
            }
        });

        return isValid;
    }
};