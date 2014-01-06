trustedshops_buyerprotection
============================

Trusted Shops Buyer protection - http://www.trustedshops.eu/


### 0.5.1

* [FIX] SUPTRUSTEDSHOPS-144: Switched the 'sales_order_save_after' listener Symmetrics_Buyerprotect_Model_Observer::registerTsSoapModel() to frontend area instead of global to prevent execution in Admin scope. This bug has been introduced in 5678949 (SUPTRUSTEDSHOPS-130) where the compatibility of the payment method 'paypal_express' was added.