* DOCUMENTATION

** INSTALLATION
   Extrahieren Sie den Inhalt dieses Archivs in Ihr Magento Verzeichnis.

** USAGE
   Dieses Modul wickelt den Trusted Shops Käuferschutz ab.

** FUNCTIONALITY
*** B: Mit der Version 0.1.1 wird das E-Mail-Template 'Trusted Shops
       Käuferschutz SOAP Fehler DE' per Migrationsskript erstellt.

*** C: Tritt ein Fehler beim SOAP Request auf, werden die Daten aus dem Objekt
       Symmetrics_Buyerprotect_Model_Service_Soap_Data verwendet, in das
       Template aus Punkt A. gerendert und an die im Backend hinterlegte E-Mail-
       Adresse versendet.

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
   shopSystemVersion: shop_system_version
   wsUser: ws_user
   wsPassword: ws_password

   Betreff:

   "SOAP Übermittlungsfehler -- Kunden-Id: {{var tsSoapData.getTsId()}} --
   Bestellungs-Id: {{var tsSoapData.getShopOrderId()}}"

   Die Methode sendTsEmailOnSoapFail() der Klasse
   Symmetrics_Buyerprotect_Model_Buyerprotection erhält als Parameter ein asso-
   ziatives Array mit den o.g. Indizes. Diese Daten werden werden in ein
   Varien_Object abgelegt und der im Backend hinterlegten E-Mail-Adresse
   zugeschickt.

   Ursachen zum Versand der Fehler-E-Mail sind Rückgabewerte des Request die
   negativ sind oder beim Auftreten einer SOAP-Exception. Die Bedeutungen der
   negativen Werte sind aus dem Integrationshandbuch (v3.00) von Trusted Shops
   zu entnehmen.

* TESTCASES

** BASIC
*** B: Gehen Sie ins Backend und öffnen im Reiter 'System > Transaktions-E-
       Mails' die Ansicht zum Bearbeiten von E-Mail-Templates
       (screenshot_001-e-mail-template_ansicht.png). Prüfen Sie, ob das Template
       'Trusted Shops Käuferschutz SOAP Fehler DE' vorhanden ist.

*** C: Fehler provozieren zum Versenden der Fehler-E-Mail.
       Gehen Sie ins Backend und aktivieren den Käuferschutz im Reiter 'System >
       Konfiguration', dort ist das Tab 'Verkäufe > Trusted Shops Käuferschutz'
       zu öffnen (screenshot_002-trusted_shops_konfiguration.png) und die 'SOAP
       Prüfung' zu aktivieren ('Ja').
       Setzen Sie hier Ihre Trusted Shops Benutzerdaten ein, tragen Ihre E-Mail-
       Adresse als Empfänger der Fehler-E-Mail ein und wählen das entsprechende
       E-Mail-Template ('Trusted Shops Käuferschutz SOAP Fehler DE') aus.

       1. Tragen Sie fehlerhafte TS Benutzerdaten ein (Shop ID, Benutzerkennung,
          oder Passwort) ein. Im Anschluß tätigen Sie eine Bestellung im Front-
          end und prüfen ob Sie eine E-Mail mit dem Betreff:

  'SOAP Übermittlungsfehler -- Kunden-Id: [TS-ID] -- Bestellungs-Id: [ORDER-ID]'

          erhalten.

       2. Erstellen Sie ein 'fehlerhaftes' Käuferschutz Produkt - die SKU ist
          keine gültige TS Produkt ID - unter 'Katalog > Produkte verwalten'
          (screenshot_003-ts_produkt_anlegen.png) und legen ein neues mit
          falschem TS Produkt ID an
          (screenshot_004-ts_produkt_anlegen-ts_produkt_typ.png und
          screenshot_005-ts_produkt_anlegen-fehlerhafte_ts_produkt_id.png).
          Tätigen Sie erneut eine Bestellung und fügen das erstellte TS Produkt
          in Ihren Warenkorb und schließen den Einkauf ab.
          Prüfen Sie ob Sie die E-Mail erhalten.
