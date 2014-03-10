trustedshops_buyerprotection
============================

Trusted Shops Buyer protection - http://www.trustedshops.eu/


### 0.5.2

* [FIX] SUPTRUSTEDSHOPS-150: Added additional validation on quote items in helper method getTsProductsInCart and removed param definition of event listener addProductToCart as it is unused.
The method Symmetrics_Buyerprotect_Helper_Data::getTsProductsInCart is used by the event listener registerTsSoapModel registered to the event 'sales_order_save_after' which is of course dispatched on order cancellation too thus the additional validation on existing quote items is necessary outside of the previous checkout onepage success event.


### 0.5.1

* [FIX] SUPTRUSTEDSHOPS-144: Switched the 'sales_order_save_after' listener Symmetrics_Buyerprotect_Model_Observer::registerTsSoapModel() to frontend area instead of global to prevent execution in Admin scope. This bug has been introduced in 5678949 (SUPTRUSTEDSHOPS-130) where the compatibility of the payment method 'paypal_express' was added.