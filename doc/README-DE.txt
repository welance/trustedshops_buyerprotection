* DOCUMENTATION

** INSTALLATION
   Extrahieren Sie den Inhalt dieses Archivs in Ihr Magento Verzeichnis.

** USAGE
   Dieses Modul wickelt den Trusted Shops Käuferschutz ab.

** FUNCTIONALITY
*** A: Mit der Version 0.1.1 wird das E-Mail-Template 'Trusted Shops
       Käuferschutz SOAP Fehler DE' per Migrationsskript erstellt.

** TECHNICAL
   Es wird mit diesem Modul das E-Mail-Template 'Trusted Shops Käuferschutz SOAP
   Fehler DE' erstellt, welches  bei Fehlern während der Übertragung zur SOAP-
   Schnittstelle von trustedshops.de verwendet werden soll.
   Innerhalb des Templates steht das Varien_Object 'tsSoapDataObject' zu Verfü-
   gung welches zum Befüllen des Templates verwendet wird. Folgende Variablen/
   Indizes stehen zur Verfügung:

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
   wsUser: ws_user
   wsPassword: ws_password

   Betreff:

   "SOAP Übermittlungsfehler -- Kunden-Id: {{var tsSoapData.getTsId()}} --
   Bestellungs-Id: {{var tsSoapData.getShopOrderId()}}"

   Die Methode sendTsEmailOnSoapFail() der Klasse
   Symmetrics_Buyerprotect_Model_Buyerprotection erhält als Parameter ein asso-
   ziatives Array mit den o.g. Indizes. Diese Daten werden werden in ein
   Varien_Object abgelegt und der im Backend hinterlegte E-Mail-Adresse
   zugeschickt.

* TESTCASES

** BASIC
*** A: Gehen Sie ins Backend und öffnen im Reiter 'System > Transaktions-E-
       Mails' die Ansicht zum Bearbeiten von E-Mail-Templates
       (screenshot_001-e-mail-template_ansicht.png). Prüfen Sie, ob das Template
       'Trusted Shops Käuferschutz SOAP Fehler DE' vorhanden ist.
