* DOCUMENTATION

** INSTALLATION
   Extrahieren Sie den Inhalt dieses Archivs in Ihr Magento Verzeichnis.

** USAGE
   Dieses Modul wickelt den Trusted Shops Käuferschutz ab.

** FUNCTIONALITY
*** A: Mit der Version 0.1.7 werden die in
       Symmetrics_Buyerprotect_Model_Type_Buyerprotect::_tsProductIds defi-
       nierten Produkte im Shop angelegt. Die Preise dieser Produkte sind be-
       reits mit 19% Mwst. vorbelegt.

*** B: Dieses Modul stellt einen eigenen Produkttypen zur Verfügung. Anhand des
       Typs findet die Prüfung statt, ob von diesem Typ Produkte im Warenkorb
       vorhanden sind. Es stehen nicht alle Einstellungsmöglichkeiten für diesen
       Produkttyp zur Verfügung ('Trusted Shops - Buyerprotection').

*** C: Das Formular zum Auswählen des TS Produkts erscheint nur unter bestimmten
       Bedingungen unterhalb der Auswahl der Zahlungsmethode beim Checkout.

*** D: Warenkorb- und Katalog-Regeln greifen nicht auf Produkte des Typs
       'Trusted Shops - Buyerprotect'.

*** E: Multi-Website und -Store fähig.

*** F: Anzeige des Trusted Shops Sigels auf der Startseite.

*** Y: (ACHTUNG: auf Wunsch von Marco Verch, wurde die Funktionalität für den
       Versand von Fehler-E-Mail bei Fehlerfall deaktiviert)
       Mit der Version 0.1.1 wird das E-Mail-Template 'Trusted Shops
       Käuferschutz SOAP Fehler DE' per Migrationsskript erstellt.

*** Z: (ACHTUNG: auf Wunsch von Marco Verch, wurde die Funktionalität für den
       Versand von Fehler-E-Mail bei Fehlerfall deaktiviert)
       Tritt ein Fehler beim SOAP Request auf, werden die Daten aus dem Objekt
       Symmetrics_Buyerprotect_Model_Service_Soap_Data verwendet, in das
       Template aus Punkt A. gerendert und an die im Backend hinterlegte E-Mail-
       Adresse versendet.

** TECHNICAL
   Die Klasse Symmetrics_Buyerprotect_Model_Type_Buyerprotect hat in
   $_tsProductIds alle TS Produkt ID's und Netto Preise aus dem Integrations-
   handbuch implementiert. Zum Zeitpunkt der Umsetzung des Moduls lag das Hand-
   buch in der Version 3.00 vor.
   Die daraus entnommenen TS Produkte welche auch im Shop angelegt werden:

             TS Produkt ID         Netto (€)       inkl. 19% (€)
        'TS080501_500_30_EUR'   =>  0.82      =>     0,97
        'TS080501_1500_30_EUR'  =>  2.47      =>     2,94
        'TS080501_2500_30_EUR'  =>  4.12      =>     4,90
        'TS080501_5000_30_EUR'  =>  8.24      =>     9,81
        'TS080501_10000_30_EUR' =>  16.47     =>     16,60
        'TS080501_20000_30_EUR' =>  32.94     =>     39,20


   Mit Symmetrics_Buyerprotect_Model_Type_Buyerprotect wird im Shop ein neuer
   Produkttyp zur Verfügung gestellt.
   In 'app/design/adminhtml/default/default/layout/buyerprotect.xml' werden die
   meisten Tabs für diesen Produkttyp entfernt. Zusätzlich wird per JS im Tem-
   plate 'app/design/adminhtml/default/default/template/buyerprotect/adminhtml/
   catalog/product/buyerprotect.js.phtml' weitere Eingabefelder gelöscht.

   Zur Prüfung im Checkout, ob Produkt vom Produkttyp 'Trusted Shops - Buyer-
   protection' im Warenkorb vorhanden ist, wurd im Helper die Methode
   getTsProductsInCart() implementiert. In dieser wird das Singleton-Objekt
   Mage_Checkout_Model_Cart geholt und geprüft ob Produkte des Typs
   Symmetrics_Buyerprotect_Model_Type_Buyerprotect::TYPE_BUYERPROTECT vorhanden
   ist. Der Aufruf der Helper-Methode erfolgt in der Event-Methode
   registerTsSoapModel() welches auf das Event
   'checkout_type_onepage_save_order_after' registriert ist.
   In dieser Event-Methode wird das Order-Objekt 'Mage_Sales_Model_Order' in die
   Session des Kunden abgelegt und erst bei erfolgreichem Checkout die SOAP-Me-
   thode Symmetrics_Buyerprotect_Model_Service_Soap::_requestV2() aufgerufen.
   Die Kommunikation mit der TS SOAP API erfolgt in requestTsProtection() beim
   'checkout_onepage_controller_success_action'-Event.

   In Symmetrics_Buyerprotect_Block_Checkout_Form::showForm() wird mit Hilfe der
   isBuyerprotectActive() und getAllTsProductTypes() Methoden des Helpers
   Symmetrics_Buyerprotect_Helper_Data geprüft, ob der aktuelle Store TS Pro-
   dukte hat und ob Käuferschutz aktiviert ist bevor das Formular zur Auswahl
   von TS Produkten erscheint (screenshot_008-ts_auswahl.png).

   Im Schritt nach der Auswahl der Zahlungsinformationen wird im Observer auf
   den Parameter 'trusted_shops' in der Methode addProductToCart() geprüft und
   sicher gestellt, dass immer nur max. ein TS Produkt im Warenkorb vorhanden
   ist.

   Um zu verhindern, dass Warenkorb-Regeln und Katalog-Regeln (Preisnachlässe)
   auf Produkte von Trusted Shops greifen, fängt die Methode
   quoteCalculateDiscountItem() das Event
   'buyerprotect_catalogrule_before_apply' ab und setzt die Nachlässe für Kata-
   log-Regeln auf 0 (Null) - für den Warenkorb ist
   Symmetrics_Buyerprotect_Model_Type_Buyerprotect_Price::getFinalPrice() zu-
   ständig.

   (ACHTUNG: auf Wunsch von Marco Verch, wurde die Funktionalität für den
   Versand von Fehler-E-Mail bei Fehlerfall deaktiviert)
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
*** A: Prüfen Sie nach der Installation unter 'Katalog > Produkte verwalten'
       (screenshot_006-ts_produkte_ab_v0.1.7.png) ob die 6 TS Produkte angelegt
       sind.

*** B: TS als eigener Produkttyp

       1. Legen Sie im Backend unter 'Katalog > Produkte verwalten' ein neues
          Produkt des Typs 'Trusted Shops - Buyerprotection' an
          (screenshot_004-ts_produkt_anlegen-ts_produkt_typ.png).

       2. Achten Sie dabei darauf, dass Möglichkeiten der Einstellungen für das
          Produkt wesentlich reduziert wurden
          (screenshot_007-ts_produkte_einstellungen.png).

*** C: TS Produkt Auswahl: Ordnen Sie ein TS Käuferschutz der aktuellen Store
       zu, auf die Sie sich im Frontend als Käufer einloggen werden und
       aktivieren den Käuferschutz im Reiter 'System > Käuferschutz' 'Verkäufe >
       Trusted Shops Käuferschutz'. Tätigen Sie im Anschluß einen Einkauf über
       das Frontend. Sobald Sie zum Schritt der 'Zahlungsinformation' kommen,
       prüfen Sie ob das Formular zu sehen ist (screenshot_008-ts_auswahl.png).

       1. Deaktivieren Sie im Backend den Käuferschutz, im Checkout bei
          'Zahlungsinformation' taucht die Auswahl der TS Produkte nicht mehr
          auf.

       2. Aktivieren Sie wieder den Käuferschutz und heben alle Zuordnungen der
          TS Produkte für einen bestimmten Store auf, loggen Sie sich im Front-
          end des Stores ein und tätigen wieder einen Einkauf. Im Checkout bei
          'Zahlungsinformation' taucht die Auswahl der TS Produkte ebenfalls
          nicht mehr auf.

*** D: Preisnachlässe haben keine Auswirkungen auf TS Produkte. Zur einfacheren
       Überprüfung, sollten Sie beim Einkaufen Produkte ohne Nachkommastelle im
       Preis auswählen und für die Regeln bspw. 10% als Nachlass einstellen.

       1. Erstellen Sie im Reiter unter 'Verkaufsförderung > Warenkorb Preisre-
          geln' eine neue Regel an.
          Im Frontend tätigen Sie einen Einkauf mit TS Produkt und gehen Sie
          sicher, dass der Nachlass nicht auf das TS Produkt angewendet wird.

       2. Erstellen Sie im Reiter unter 'Verkaufsförderung > Katalog Preisre-
          geln' eine neue Regel an.
          Im Frontend tätigen Sie einen Einkauf mit TS Produkt und gehen Sie
          sicher, dass der Nachlass nicht auf das TS Produkt angewendet wird.

*** E: Legen Sie eine neu Website an ('System > Stores verwalten'). Ordnen Sie
       verschiedene TS Produkte den verschiedenen Stores der Websites zu.
       Tätigen Sie in den Stores der Websites Einkäufe, wo Sie die Produkte zu-
       geordnet haben. Beim Punkt 'Zahlungsinformationen' im Checkout, haben Sie
       nur die TS Produkte zur Aswahl, die Sie den Stores zugeordnet haben.
       Deaktivieren Sie für einen Store den Käuferschutz im Backend und tätigen
       in dem Store erneut einen Einkauf. Unterhalb der 'Zahlungsinformationen'
       im Checkout werden Sie keine TS Produktauswahl vorfinden.

*** F: Sie sehen nun durch die oben vorgenommenen Einstellungen das TS-Sigel
       rechts unter dem Warehnkorb.

*** Y: (ACHTUNG: auf Wunsch von Marco Verch, wurde die Funktionalität für den
       Versand von Fehler-E-Mail bei Fehlerfall deaktiviert)
       Gehen Sie ins Backend und öffnen im Reiter 'System > Transaktions-E-
       Mails' die Ansicht zum Bearbeiten von E-Mail-Templates
       (screenshot_001-e-mail-template_ansicht.png). Prüfen Sie, ob das Template
       'Trusted Shops Käuferschutz SOAP Fehler DE' vorhanden ist.

*** Z: (ACHTUNG: auf Wunsch von Marco Verch, wurde die Funktionalität für den
       Versand von Fehler-E-Mail bei Fehlerfall deaktiviert)
       Fehler provozieren zum Versenden der Fehler-E-Mail.
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

       2. Erstellen Sie ein 'fehlerhaftes' Käuferschutz Produkt - die SKU ent-
          spricht keinem gültigem TS Produkt ID - unter 'Katalog > Produkte
          verwalten' (screenshot_003-ts_produkt_anlegen.png) und legen ein neues
          mit falschem TS Produkt ID an
          (screenshot_004-ts_produkt_anlegen-ts_produkt_typ.png und
          screenshot_005-ts_produkt_anlegen-fehlerhafte_ts_produkt_id.png).
          Tätigen Sie erneut eine Bestellung und fügen das erstellte TS Produkt
          in Ihren Warenkorb und schließen den Einkauf ab.
          Prüfen Sie, ob Sie die Fehler-E-Mail erhalten.
