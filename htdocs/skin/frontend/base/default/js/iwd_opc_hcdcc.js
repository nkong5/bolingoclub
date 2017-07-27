/**
 * IWD save order extension
 *
 * NOTICE OF LICENSE
 *
 * Copyright (C) Bolingo UG, - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Vlado, 24 November 2016
 *
 * @copyright Bolingo UG
 * @author Vlado  (IWD)
 * @version 0.1
 * @package base_default
 *
 * last change: $Date: 2016-11-25
 * last author: $Author: Edgar Bongkishiy <edgar.bongkishiy@bolingo.com>
 */

;
// define jquery
if (typeof($j_opc) == 'undefined' || $j_opc == undefined || !$j_opc) {
    $j_opc = false;

    if (typeof($ji) != 'undefined' && $ji != undefined && $ji)
        $j_opc = $ji; // from iwd_all 2.x
    else {
        if (typeof(jQuery) != 'undefined' && jQuery != undefined && jQuery)
            $j_opc = jQuery;
    }
}
var IWD_OPC = IWD_OPC || {};

IWD_OPC.HCDCC = {
    /** CREATE EVENT FOR SAVE ORDER **/
    initSaveOrder: function () {
        $j_opc(document).on('click', '.opc-btn-checkout', function (e) {
            e.preventDefault();

            if (IWD.OPC.Checkout.disabledSave == true)
            {
                return;
            }

            // check agreements
            var mis_aggree = false;
            $j_opc('#checkout-agreements input[name*="agreement"]').each(function () {
                if (!$j_opc(this).is(':checked')) {
                    mis_aggree = true;
                }
            });

            if (mis_aggree) {
                $j_opc('.opc-message-container').html($j_opc('#agree_error').html());
                $j_opc('.opc-message-wrapper').show();
                IWD.OPC.Checkout.hideLoader();
                IWD.OPC.Checkout.unlockPlaceOrder();
                IWD.OPC.saveOrderStatus = false;
                return false;
            }
            ///

            var addressForm = new VarienForm('opc-address-form-billing');
            if (!addressForm.validator.validate()) {
                return;
            }

            if (!$j_opc('input[name="billing[use_for_shipping]"]').prop('checked')) {
                var addressForm = new VarienForm('opc-address-form-shipping');
                if (!addressForm.validator.validate()) {
                    return;
                }
            }

            // check if LIPP enabled
            if (typeof(IWD.LIPP) != 'undefined' && IWD.LIPP != undefined && IWD.LIPP != '' && IWD.LIPP) {
                if (IWD.LIPP.lipp_enabled) {
                    var method = payment.currentMethod;
                    if (typeof(method) != 'undefined' && method != undefined && method != '' && method) {
                        if (method.indexOf('paypaluk_express') != -1 || method.indexOf('paypal_express') != -1) {
                            if (IWD.OPC.Checkout.config.comment !== "0")
                                IWD.OPC.saveCustomerComment();
                            //IWD.LIPP.redirectPayment();
                            if (payment.currentMethod == 'paypal_express') {
                                var urlConnect = PayPalLightboxConfig.setExpressCheckout
                            }

                            if (payment.currentMethod == 'paypaluk_express') {
                                var urlConnect = PayPalLightboxConfig.setExpressCheckoutUk;
                            }

                            paypal.checkout.initXO();
                            $ji.support.cors = true;
                            $ji.ajax({
                                url: urlConnect,
                                type: "GET",
                                async: true,
                                crossDomain: false,

                                success: function (token) {

                                    if (token.indexOf('cart') != -1 || token.indexOf('login') != -1) {
                                        paypal.checkout.closeFlow();
                                        setLocation(token);
                                    } else {
                                        var url = paypal.checkout.urlPrefix + token;
                                        paypal.checkout.startFlow(url);
                                    }

                                },
                                error: function (responseData, textStatus, errorThrown) {
                                    alert("Error in ajax post" + responseData.statusText);
                                    //Gracefully Close the minibrowser in case of AJAX errors
                                    paypal.checkout.closeFlow();
                                }
                            });
                            return;
                        }
                    }
                }
            }
            ////

            IWD.OPC.saveOrderStatus = true;
            if (payment.currentMethod == 'hcdcc') {
                var frame = document.getElementById('hcdcc_payment_frame');
                frame.contentWindow.postMessage('{}', '*');
            }

            IWD.OPC.Plugin.dispatch('saveOrderBefore');
            if (IWD.OPC.Checkout.isVirtual === false) {
                IWD.OPC.Checkout.lockPlaceOrder();
                IWD.OPC.Shipping.saveShippingMethod();
            } else {
                IWD.OPC.validatePayment();
            }
        });

    }
};
$j_opc(document).ready(function () {
    IWD_OPC.HCDCC.initSaveOrder();
});

