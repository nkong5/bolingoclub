AWEventbookingUIPopup = Class.create();
AWEventbookingUIPopup.prototype = {
    initialize: function(config) {
        this.container = $$(config.containerSelector).first();
        this.overlay = $$(config.overlaySelector).first();
        this.buttons = {
            close: {
                enabled: config.buttons.close.enabled,
                el: $$(config.buttons.close.selector).first(),
                onClickFn: config.buttons.close.onClickFn || Prototype.emptyFunction
            },
            accept: {
                enabled: config.buttons.accept.enabled,
                el: $$(config.buttons.accept.selector).first(),
                onClickFn: config.buttons.accept.onClickFn || Prototype.emptyFunction
            }
        };
        this.initObservers();
    },

    initObservers: function() {
        if (this.buttons.close.el) {
            this.buttons.close.el.observe('click', this.onCloseClick.bind(this));
        }
        if (this.buttons.accept.el) {
            this.buttons.accept.el.observe('click', this.onAcceptClick.bind(this));
        }
    },

    showPopup: function() {
        this.container.setStyle({'display': 'block', 'left' : '9999999px'});
        this._resizePopup();
        this.container.setStyle({'display': 'none'});
        this._showOverlay();
        this._showPopup();
        Event.observe(window, 'resize', this._resizePopup.bind(this));
    },

    onOverlayClick: function(e) {
        this.onCloseClick(e);
    },

    onAcceptClick: function(e) {
        if (!this.buttons.accept.enabled) {
            return;
        }
        this.buttons.accept.onClickFn(e);
    },

    onCloseClick: function(e) {
        if (!this.buttons.close.enabled) {
            return;
        }
        this.buttons.close.onClickFn(e);
        this._hideOverlay();
        this._hidePopup();
        Event.stopObserving(window, 'resize', this._resizePopup.bind(this));
    },

    _resizePopup: function() {
        var top = (document.viewport.getHeight() - this.container.getHeight())/2;
        var left = (document.viewport.getWidth() - this.container.getWidth())/2;
        if (top < 50) {
            top = 50;
            this.container.setStyle({
                height: (document.viewport.getHeight() - 100) + 'px'
            });
        }
        var contentHeight = $$('.aw-eventbooking-popup-content').first().getHeight() + 60;
        this.container.setStyle({
            left: left + 'px',
            top: top + 'px',
            height: contentHeight + 'px',
            minHeight: contentHeight + 'px'
        });
    },

    _showOverlay: function() {
        this._applyShowEffect(this.overlay);
        this.overlay.observe('click', this.onOverlayClick.bind(this));
    },

    _hideOverlay: function() {
        this._applyHideEffect(this.overlay);
        this.overlay.stopObserving('click', this.onOverlayClick.bind(this));
    },

    _showPopup: function() {
        Object.values(this.buttons).each(function(btn){
            if (!btn.el) {
                return;
            }
            if (btn.enabled) {
                btn.el.show();
            } else {
                btn.el.hide();
            }
        });
        this._applyShowEffect(this.container);
    },

    _hidePopup: function() {
        this._applyHideEffect(this.container);
    },

    _applyShowEffect: function(el) {
        var originalStyle = {
            '-moz-opacity': (el.getStyle('-moz-opacity') || '1') + "",
            'opacity':  (el.getStyle('opacity') || '1') + "",
            'filter':  (el.getStyle('filter') || 'alpha(opacity=100)') + ""
        };
        el.setStyle({
            '-moz-opacity': '0',
            'opacity': '0',
            'filter': 'alpha(opacity=0)',
            'display': 'block'
        });
        new Effect.Morph(el, {
            style: originalStyle,
            duration: 0.3
        });
    },

    _applyHideEffect: function(el) {
        var originalStyle = {
            '-moz-opacity': (el.getStyle('-moz-opacity') || '1') + "",
            'opacity':  (el.getStyle('opacity') || '1') + "",
            'filter':  (el.getStyle('filter') || 'alpha(opacity=100)') + ""
        };
        new Effect.Morph(el, {
            style: {
                '-moz-opacity': '0',
                'opacity': '0',
                'filter': 'alpha(opacity=0)'
            },
            duration: 0.3,
            afterFinish: function() {
                el.setStyle({
                    'display': 'none'
                });
                el.setStyle(originalStyle);
            }
        });
    }
};