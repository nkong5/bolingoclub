CAWEventbookingTicketsGrid = Class.create({
    urls: {},
    constants: {},

    initialize: function() {
    },

    _reloadGrid: function() {
        aw_evbook_tickets_gridJsObject.reload();
    },

    _changeField: function(ticketId, field, newValue) {
        new Ajax.Request(this.urls.changeField, {
            parameters: {
                ticketid: ticketId,
                field: field,
                newvalue: newValue
            },
            onComplete: this._reloadGrid.bind(this)
        });
    },

    redeemTicket: function(ticketId) {
        this._changeField(ticketId, 'redeemed', this.constants.redeemed);
    },

    undoRedeemTicket: function(ticketId) {
        this._changeField(ticketId, 'redeemed', this.constants.not_redeemed);
    },

    refundTicket: function(ticketId) {
        this._changeField(ticketId, 'payment_status', this.constants.ps_refunded);
    },

    undoRefundTicket: function(ticketId) {
        this._changeField(ticketId, 'payment_status', this.constants.ps_paid);
    }
});

var AWEventbookingTicketsGrid = new CAWEventbookingTicketsGrid();
