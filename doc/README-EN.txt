* DOCUMENTATION

** INSTALLATION
   Extract the contents of this archive into your Magento directory.

   Go to the backend and for the testing purposes enter the following access codes under
   ' Admin panel> System> Configuration> Trusted shops buyer protection ':


   TS ID: X41495A6E65ECDDCD554A02C0601D1C97
   TS User password: testExcellencePartner
   TS Password: test12345678
   TS WSDL Url: Test

   Generate a seal box of your choice under
   http://www.trustedshops.de/shopbetreiber/siegelbox/index.php, under
   your TS ID, and download the linked
   wallpaper and logo from there.
   (screenshot_009-ts_siegelboxgenerator_von_trusted_shops.png).
   The generated code of the seal, the wallpaper and the logo are to be entered/downloaded
   in the module configuration.

** USAGE
   This module handles the Trusted shops buyer protection.

** FUNCTIONALITY
*** A: (ATTENTION: in the module version 0.1.15, the migration script
       (0.1.5-0.1.7) is changed. The product names are in the following format in it:
           " Buyer protection up to [PROTECTION VALUE] EUR - ".
       Besides, the protection value is a constituent of the respective Trusted shops product ID.
       A later product update by means of migration
       script is not possible therefore after talking back
       to TW, the migration script, which initially created the TS products
       is changed.)
       With the version 0.1.7 the products defined in
       Symmetrics_Buyerprotect_Model_Type_Buyerprotect::_tsProductIds
       are created in the shop. 19% of VAT is already
       assigned for the prices of these products.

*** B: This module provides its own product type. With the help of this
       type it can be verified whether there are products of this type in the cart.
       Not all the settings for this
       product type are available (' Trusted Shops - Buyerprotection ').

*** C: TS products selection form appears only under particular
       conditions during payment method selection by checkout.

*** D: Cart and catalogue rules do not cover the product type
       ' Trusted Shops - Buyerprotect '.

*** E: This is a multi-web site and multi shop module.

*** F: Displays Trusted Shops seal on the homepage.

*** G: There is multilanguage information banner in the System Configuration
       with banners, actions, text and links.

** TECHNICAL
   The class Symmetrics_Buyerprotect_Model_Type_Buyerprotect has implemented in
   $ _tsProductIds all product TS ID's and Netto prices from the integration
   guide. At the time of module implementation the guide version was  3.00.

   The TS products taken from there are the products created in the shop:

             TS Product ID Netto (€) incl. 19% (€)
        'TS080501_500_30_EUR' => 0.82 => 0.97
        'TS080501_1500_30_EUR' => 2.47 => 2.94
        'TS080501_2500_30_EUR' => 4.12 => 4.90
        'TS080501_5000_30_EUR' => 8.24 => 9.81
        'TS080501_10000_30_EUR' => 16.47 => 16.60
        'TS080501_20000_30_EUR' => 32.94 => 39.20


   With Symmetrics_Buyerprotect_Model_Type_Buyerprotect a new
   product type is provided in the shop.
   In 'app/design/adminhtml/default/default/layout/buyerprotect.xml'
   most of the tabs for this product type are deleted. In addition, per JS in template
   ' app/design/adminhtml/default/default/template/buyerprotect/adminhtml/
   catalog/product/buyerprotect.js.phtml' other input fields are deleted.

   To check in checkout, whether a product of the product type ' Trusted Shops
   Buyerprotection' is in the cart, the method
   getTsProductsInCart () was implemented in the Helper. In this method the Singletone object
   Mage_Checkout_Model_Cart is taken and examined whether the products of the type
   Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT are available.
   The call of the help method takes place in the event method
   registerTsSoapModel () which is registered on the event
   'checkout_type_onepage_save_order_after'.
   In this event method, the order object 'Mage_Sales_Model_Order' is discarded in customer
   session and only with successful checkout calls the SOAP method
   Symmetrics_Buyerprotect_Model_Service_Soap::_requestV2 ().
   The communication with the TS SOAP API occurs in requestTsProtection () by
   ' checkout_onepage_controller_success_action ' event.

   In Symmetrics_Buyerprotect_Block_Checkout_Form::showForm () with the help of
   isBuyerprotectActive () and getAllTsProductTypes () methods,  the Helper's
   Symmetrics_Buyerprotect_Helper_Data is examined, whether it has actual store TS products
   and whether buyer protection is activated, before the form appears for the TS products selection
   (screenshot_008-ts_auswahl.png).

   In the step following payment information selection it is checked in the Observer for
   the parameter 'trusted_shops' in the method addProductToCart () and
   ensured that max. one TS product is available in the cart.


   To prevent the spreading of cart and catalogue rules (price discounts)
   to Trusted Shops products, the method
   quoteCalculateDiscountItem () intercepts the event
   'buyerprotect_catalogrule_before_apply' and sets the discounts for catalogue
   rules to 0 (zero), for the cart
   Symmetrics_Buyerprotect_Model_Type_Buyerprotect_Price::getFinalPrice () is
   permanent.

   (ATTENTION: As requested by Marco Verch, the functionality for the
   dispatch of error e-mail in case error has occured is deactivated)
   With this module the e-mail template ' Trusted Shops buyer protection SOAP error de'
   is created, which should be used by trustedshops.de in case of errors during the forward to SOAP-
   Interface.
   Within the template, Varien_Object 'tsSoapDataObject' is available
   which is used for filling in of the template. The following variables/
   indexes are available:

   returnValue: return_value
   tsId: ts_id
   tsProductId: ts_product_id
   amount: amount
   currency: currency
   paymentType: payment_type
   buyerEmail: buyer_email
   shopCustomerID: shop_customer_id
   shopOrderID: shop_order_id
   orderDate: order_date
   shopSystemVersion: shop_system_version
   wsUser: ws_user
   wsPassword: ws_password

   References:

   " SOAP transmission error  - Customer-Id: {{var tsSoapData.getTsId ()}}--
   Order-Id: {{var tsSoapData.getShopOrderId ()}} "

   The method sendTsEmailOnSoapFail () of the class
   Symmetrics_Buyerprotect_Model_Buyerprotection receives an associative
   array with o.g. indexes as a parameter. This data is stored in Varien_Object
    and is sent to the deposited in the backend e-mail address.

   Reasons for sending of the error e-mail are negative request return values
   or appearance of a SOAP exception. The meanings of the
   negative values are taken from Trusted Shops integration guide (v3.00).


G: Magento template (with multilanguage support) is connected to Info box in
   the Admin Panel Configuration by the frontend model renderer
   (adminhtml_system_config_info).

* TESTCASES

** BASIC
*** A: Check after the installation under ' Catalogue> Products management '
       (screenshot_006-ts_produkte_ab_v0.1.15.png) whether 6 TS products are created.

       Manual settings must be still carried out, activate
       product status. Select a product in the backend
       ' Admin Panel> Catalogue> Products management ', the status can be set in tab
       'General'. Set a tax class in tab 'Prices'
       Pay attention that Brutto price already contains 19%.
       If the taxation does not equal 19%, the price has to be
       respectively adjusted.
       Afterwards activate the product for the corresponding web site in tab 'Web sites'.
       ATTENTION: SKU changes are allowed only after Trusted
       Shops requests.

       These manual settings can not currently be avoided, as in
       the migration script the correct Store-ID cannot be determined.

*** B: TS as own product type.

       1. Put in the backend under ' Catalogue> Product management ' a new
          product of the  ' Trusted Shops - Buyerprotection ' type.
          [SCREENSHOT: screenshot_004-ts_produkt_anlegen-ts_produkt_typ.png]

       2. Pay attention to the fact, that setting possibilities for this
          product were substantially reduced.
          [SCREENSHOT: screenshot_007-ts_produkte_einstellungen.png]

*** C: TS product selection: map a TS buyer protection of the current store
       to which you will log in in the frontend as a buyer and
       activate the buyer protection in tab ' System> Buyer protected sales >
       Trusted Shops buyer protection'. Carry out a purchase
       in the frontend. As soon as you come to 'payment information' step,
       check whether the form is to be seen.
       [SCREENSHOT: screenshot_008-ts_auswahl.png]

       1. Deactivate the buyer protection in the backend, TS products selection
          does not appear any more in checkout by 'Payment information'.

       2. Activate buyer protection again and cancel all the TS products assignments
          for a certain store, log in in thefrontend
          of the store and carry out a purchase again. TS products selection still
          does not appear in checkout by 'Payment information'.

*** D: Discounts have no effect on TS products.
       To check this, by purchasing, select products without a decimal in
       the price (without a comma) and set the rules, for example, 10% as a discount.

       1. Create a new rule in a tab under ' Sales presentation (promotion) > Cart price
          rules'.
          In the frontend carry out a purchase of TS product and make
          sure that price reduction is not applied to the TS product.

       2. Create a new rule in a tab under ' Sales presentation (promotion) > Catalogue price
          rules'.
          In the frontend carry out a purchase of TS product and make
          sure that price reduction is not applied to the TS product.

*** E: Create a new web site (' System> Store management ').Assign
       different TS products to different stores of the web site.
       Carry out a purchase in the stores of the web site, where you assigned the products.
       In  'payment information' in checkout
       only those TS products stay for selection which you have assigned to the stores.
       In the backend, deactivate buyer protection for any store and carry out a purchase
       in the store once more. Below 'payment information'
       in checkout you will find no TS product selection.

*** F: You will see by the above settings, the TS seal forms right under the basket.
       Check whether the seal is displayed when ' Activate Trusted Shops
       seal box' setting was set to 'No'. The same applies if the setting is set to 'Yes''.
       [SCREENSHOT: screenshot_002-trusted_shops_konfiguration.png] (6)

*** G: 1. Open " Admin Panel / System / Configuration / Sales/
          Trusted Shops Seal / Info " and compare the contents of a banner
          with a screenshot
          [SCREENSHOT: screenshot_002-trusted_shops_konfiguration.png] (2).
          To test the buttons and links.
       2. Change the backend language from English into German, in this case,
          the banner should display German text
          [SCREENSHOT: screenshot_002-trusted_shops_konfiguration_info_de.png].
