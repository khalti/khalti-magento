/**
* Fourwallsinn_Khalti
*
* @category    Payment Gateway
* @package     Fourwallsinn_Khalti
* @author      4 Walls Innovations
* @copyright   4 Walls Innovations (http://www.4wallsinn.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
 define(
     [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-payment-method',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url',
        'https://khalti.com/static/khalti-checkout.js'
     ],
     function ( $,Component,placeOrderAction,quote,selectPaymentMethodAction,customer,checkoutData,additionalValidators,url) {
         'use strict';
         
         var handler;
         var config = {
            // replace the publicKey with yours
            "publicKey": window.checkoutConfig.payment.khalti.khalti_public_key,
            "productIdentity": quote.getQuoteId(),
            "productName": "Product",
            "productUrl": new URL(window.checkoutConfig.checkoutUrl).origin,
            "eventHandler": {
                onSuccess (payload) {
                    // hit merchant api for initiating verfication
                    this.realPlaceOrder(payload);
                },
                onError (error) {
                    console.log(error);
                }
            }
        };
         return Component.extend({
            defaults: {
                template: 'Fourwallsinn_Khalti/payment/khalti-form',
                redirectAfterPlaceOrder: false,
            }, 

            initialize: function () {
                this._super();
                this.loadKhaltiCheckout();
            },

            loadKhaltiCheckout: function (callback) {
                if (typeof KhaltiCheckout === "undefined")
                {
                    var script = document.createElement('script');

                    script.onload = function() {
                        handler = new KhaltiCheckout(config);
                    };
                    script.onerror = function(response) {
                        console.log("khalti checkout load error");
                        console.log(response);
                    };
                    script.src = "https://khalti.com/static/khalti-checkout.js";
                    document.head.appendChild(script);
                }
                else {
                    handler = new KhaltiCheckout(config);
                }
            },
            
            getCode: function () {
                return 'khalti';
            },

            isActive: function () {
                return true;
            },
            /**
             * Get value of instruction field.
             * @returns {String}
             */
            getInstructions: function () {
                // return window.checkoutConfig.payment.instructions[this.item.method];
            },

            placeOrder: function (data, event) {
            if (event) {
                event.preventDefault();
            }

            var self = this;
            this.isPlaceOrderActionAllowed(false);
            var amount = quote.totals().base_grand_total*100;
            var config = {
                "publicKey": window.checkoutConfig.payment.khalti.khalti_public_key,
                "productIdentity": quote.getQuoteId(),
                "productName": "Product",
                "productUrl": new URL(window.checkoutConfig.checkoutUrl).origin,
                "eventHandler": {
                    onSuccess (payload) {
                        self.realPlaceOrder(payload);
                    },
                    onError (error) {
                        console.log(error);
                    }
                }
            };

            handler = new KhaltiCheckout(config);
            
            handler.show({amount: amount});
            this.isPlaceOrderActionAllowed(true);
        },

            realPlaceOrder: function (paylaod) {
                var self = this;
                this.getPlaceOrderDeferredObject()
                    .fail(
                        function () {
                            self.isPlaceOrderActionAllowed(true);
                        }
                    ).done(
                    function () {
                        self.afterPlaceOrder(paylaod);

                        if (self.redirectAfterPlaceOrder) {
                            redirectOnSuccessAction.execute();
                        }
                    }
                );
            },

            selectPaymentMethod: function() {
                selectPaymentMethodAction(this.getData());
                checkoutData.setSelectedPaymentMethod(this.item.method);
                return true;
            },

            afterPlaceOrder: function (payload) {
                var token = payload.token;
                var amount = payload.amount;
                $.mage.redirect('/khalti/response/response?token='+token+'&amount='+amount);
                return false;
            },

            continueToKhalti: function () {
                //if (additionalValidators.validate()) {
                 //   update payment method information if additional data was changed
                  this.selectPaymentMethod();
                  setPaymentMethodAction(this.messageContainer);
                   // return false;
                //}
               return false;
            }

        });
     }
);
