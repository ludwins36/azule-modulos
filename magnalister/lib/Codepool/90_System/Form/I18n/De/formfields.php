<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLI18n::gi()->add('formfields', array(
    'checkin.status' => array(
        'label' => 'Statusfilter',
        'valuehint' => 'nur aktive Artikel &uuml;bernehmen',
        'help' => 'Im Web-Shop können Sie Artikel aktiv oder inaktiv setzen. Je nach Einstellung hier werden nur aktive Artikel beim Produkte hochladen angezeigt.',
    ),
    'lang' => array (
        'label' => 'Artikelbeschreibung',
    ),
    'prepare.status' => array(
        'label' => '{#i18n:formfields__checkin.status__label#}',
        'valuehint' => '{#i18n:formfields__checkin.status__valuehint#}',
        'help' => 'Im Web-Shop können Sie Artikel aktiv oder inaktiv setzen. Je nach Einstellung hier werden nur aktive Artikel beim Produkte vorbereiten angezeigt.',
    ),
    'tabident' => array(
        'label' => '{#i18n:ML_LABEL_TAB_IDENT#}',
        'help' => '{#i18n:ML_TEXT_TAB_IDENT#}',
    ),
    'stocksync.tomarketplace' => array(
        'label' => 'Lagerveränderung vom Shop',
        'help' => '
            Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts) den aktuellen {#setting:currentMarketplaceName#}-Lagerbestand an der Shop-Lagerbestand an (je nach Konfiguration ggf. mit Abzug).<br />
            <br />
            Dabei werden die Werte aus der Datenbank geprüft und übernommen, auch wenn die Änderungen durch z.B. eine Warenwirtschaft nur in der Datenbank erfolgten.<br />
            <br />
            Einen manuellen Abgleich können Sie anstoßen, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br />
            Zusätzlich können Sie den Lagerabgleich (ab Tarif Flat - maximal viertelstündlich) auch durch einen eigenen CronJob anstoßen, indem Sie folgenden Link zu Ihrem Shop aufrufen:<br />
            <i>{#setting:sSyncInventoryUrl#}</i><br />
            Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die häufiger als viertelstündlich laufen, werden geblockt.<br />
            <br />
            <strong>Hinweis:</strong> Die Einstellungen unter "Konfiguration" → "Einstellvorgang" → "Stückzahl Lagerbestand" werden berücksichtigt. 
        ',
    ),
    'stocksync.frommarketplace' => array(
        'label' => 'Bestellimport von {#setting:currentMarketplaceName#}',
        'help' => '
            Wenn z. B. bei {#setting:currentMarketplaceName#} ein Artikel 3 mal gekauft wurde, wird der Lagerbestand im Shop um 3 reduziert.<br />
            <br />
            <strong>Wichtig:</strong> Diese Funktion läuft nur, wenn Sie den Bestellimport aktiviert haben!
        ',
    ),
    'inventorysync.price' => array(
        'label' => 'Artikelpreis',
        'help' => '
            <dl>
                <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                <dd>
                    Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts) den aktuellen Shop-Preis auf Ihren {#setting:currentMarketplaceName#}-Preis an.<br />
                    Dabei werden die Werte aus der Datenbank geprüft und übernommen, auch wenn die Änderungen durch z.B. eine Warenwirtschaft nur in der Datenbank erfolgten.<br />
                    <br />
                    Einen manuellen Abgleich können Sie anstoßen, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br />
                    <br />
                    Zusätzlich können Sie den Preisabgleich auch durch einen eigenen CronJob anstoßen, indem Sie folgenden Link zu Ihrem Shop aufrufen:<br />
                    <i>{#setting:sSyncInventoryUrl#}</i><br />
                    Eigene CronJob-Aufrufe die häufiger als viertelstündlich laufen, werden geblockt.
                </dd>
            </dl>
            <br />
            <strong>Hinweis:</strong> Die Einstellungen unter "Konfiguration" → "Preisberechnung" werden berücksichtigt.
        ',
    ),
    'mail.send' => array(
        'label' => 'E-Mail versenden?',
        'help' => 'Soll von Ihrem Shop eine E-Mail an den K&auml;ufer gesendet werden um Ihren Shop zu promoten?',
    ),
    'mail.originator.name' => array(
        'label' => 'Absender Name',
        'default' => 'Beispiel-Shop',
    ),
    'mail.originator.adress' => array(
        'label' => 'Absender E-Mail Adresse',
        'default' => 'beispiel@onlineshop.de',
    ),
    'mail.subject' => array(
        'label' => 'Betreff',
        'default' => 'Ihre Bestellung bei #SHOPURL#',
    ),
    'mail.content' => array(
        'label' => 'E-Mail Inhalt',
        'hint' => '
            Liste verf&uuml;gbarer Platzhalter f&uuml;r Betreff und Inhalt:
            <dl>
                <dt>#FIRSTNAME#</dt>
                <dd>Vorname des K&auml;ufers</dd>
                <dt>#LASTNAME#</dt>
                <dd>Nachname des K&auml;ufers</dd>
                <dt>#EMAIL#</dt>
                <dd>e-mail Adresse des K&auml;ufers</dd>
                <dt>#PASSWORD#</dt>
                <dd>Password des K&auml;ufers zum Einloggen in Ihren Shop. Nur bei Kunden, die dabei automatisch angelegt werden, sonst wird der Platzhalter durch \'(wie bekannt)\' ersetzt.</dd>
                <dt>#ORDERSUMMARY#</dt>
                <dd>
                    Zusammenfassung der gekauften Artikel. Sollte extra in einer Zeile stehen.<br>
                    <i>Kann nicht im Betreff verwendet werden!</i>
                </dd>
                <dt>#MARKETPLACE#</dt>
                <dd>Name dieses Marketplaces</dd>
                <dt>#SHOPURL#</dt>
                <dd>URL zu Ihrem Shop</dd>
                <dt>#ORIGINATOR#</dt>
                <dd>Absender Name</dd>
            </dl>
        ',
        'default' => 
'<style><!--
    body {
        font: 12px sans-serif;
    }
    table.ordersummary {
            width: 100%;
            border: 1px solid #e8e8e8;
    }
    table.ordersummary td {
            padding: 3px 5px;
    }
    table.ordersummary thead td {
            background: #cfcfcf;
            color: #000;
            font-weight: bold;
            text-align: center;
    }
    table.ordersummary thead td.name {
            text-align: left;
    }
    table.ordersummary tbody tr.even td {
            background: #e8e8e8;
            color: #000;
    }
    table.ordersummary tbody tr.odd td {
            background: #f8f8f8;
            color: #000;
    }
    table.ordersummary td.price,
    table.ordersummary td.fprice {
            text-align: right;
            white-space: nowrap;
    }
    table.ordersummary tbody td.qty {
            text-align: center;
}
--></style>
<p>Hallo #FIRSTNAME# #LASTNAME#,</p>
<p>vielen Dank f&uuml;r Ihre Bestellung! Sie haben &uuml;ber #MARKETPLACE# in unserem Shop folgendes bestellt:</p>
#ORDERSUMMARY#
<p>Zuz&uuml;glich etwaiger Versandkosten.</p>
<p>&nbsp;</p>
<p>Mit freundlichen Gr&uuml;&szlig;en,</p>
<p>Ihr Online-Shop-Teamff</p>'
    ),
    'mail.copy' => array(
        'label' => 'Kopie an Absender',
        'help' => 'Die Kopie wird an die Absender E-Mail Adresse gesendet.',
    ),
    'quantity' => array(
        'label' => 'Lagerbestand',
        'help' => '
            Geben Sie hier an, wie viel Lagermenge eines Artikels auf {#setting:currentMarketplaceName#} verfügbar sein soll.<br />
            <br />
            Um Überverkäufe zu vermeiden, können Sie den Wert<br />
            "Shop-Lagerbestand übernehmen abzgl. Wert aus rechtem Feld" aktivieren.<br />
            <br />
            <strong>Beispiel:</strong> Wert auf "2" setzen. Ergibt → Shoplager: 10 → MeinPaket-Lager: 8<br />
            <br />
            Hinweis:Wenn Sie Artikel, die im Shop inaktiv gesetzt werden, unabhängig der verwendeten Lagermengen auch auf dem Marktplatz als Lager "0" behandeln wollen, gehen Sie bitte wie folgt vor:<br />
            <br />
            <ul>
                <li>"Synchronisation des Inventars" > "Lagerveränderung Shop" auf "automatische Synchronisation per CronJob" einstellen</li>
                <li>"Globale Konfiguration" > "Produktstatus" > "Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt" aktivieren</li>
            </ul>
        ',
    ),
    'maxquantity' => array(
        'label' => 'Stückzahl-Begrenzung',
        'help' => '
            Hier k&ouml;nnen Sie die St&uuml;ckzahlen der auf {#setting:currentMarketplaceName#} eingestellten Artikel begrenzen.<br /><br />
            <strong>Beispiel:</strong> Sie stellen bei "St&uuml;ckzahl" ein "Shop-Lagerbestand &uuml;bernehmen", und tragen hier 20 ein. Dann werden beim Hochladen so viel St&uuml;ck eingestellt wie im Shop vorhanden, aber nicht mehr als 20. Die Lagersynchronisierung (wenn aktiviert) gleicht die {#setting:currentMarketplaceName#}-St&uuml;ckzahl an den Shopbestand an, solange der Shopbestand unter 20 St&uuml;ck ist. Wenn im Shop mehr als 20 St&uuml;ck auf Lager sind, wird die {#setting:currentMarketplaceName#}-St&uuml;ckzahl auf 20 gesetzt.<br /><br />
            Lassen Sie dieses Feld leer oder tragen Sie 0 ein, wenn Sie keine Begrenzung w&uuml;nschen.<br /><br />
            <strong>Hinweis:</strong> Wenn die "St&uuml;ckzahl"-Einstellung "Pauschal (aus rechtem Feld)" ist, hat die Begrenzung keine Wirkung.
        ',
    ),
    'priceoptions' => array(
        'label' => 'Preisoptionen',
        'help' => '{#i18n:configform_price_field_priceoptions_help#}',
    ),
    'price.usespecialoffer' => array(
        'label' => 'auch Sonderpreise verwenden',
    ),
    'exchangerate_update' => array(
        'label' => 'Wechselkurs',
        'valuehint' => 'Wechselkurs automatisch aktualisieren',
        'help' => '{#i18n:form_config_orderimport_exchangerate_update_help#}',
        'alert' => '{#i18n:form_config_orderimport_exchangerate_update_alert#}',
    ),
    
    'importactive' => array(
        'label' => 'Import aktivieren',
        'help' => '
            Sollen Bestellungen aus {#setting:currentMarketplaceName#} importiert werden?<br />
            <br />
            Wenn die Funktion aktiviert ist, werden Bestellungen voreingestellt stündlich importiert.<br />
            <br />
            Sie können die Zeiten der automatischen Bestellimporte selbst unter<br />
            "magnalister Admin" → "Globale Konfiguration" → "Bestellabrufe" bestimmen.<br />
            <br />
            Einen manuellen Import können Sie anstoßen, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br />
            <br />
            Zusätzlich können Sie den Bestellimport (ab Tarif Flat - maximal viertelstündlich) auch durch einen eigenen CronJob anstoßen, indem Sie folgenden Link zu Ihrem Shop aufrufen:
            <i>{#setting:sImportOrdersUrl#}</i><br />
            <br />
            Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die häufiger als viertelstündlich laufen, werden geblockt.
        ',
    ),
    'preimport.start' => array(
        'label' => 'erstmalig ab Zeitpunkt',
        'hint' => 'Startzeitpunkt',
        'help' => 'Startzeitpunkt, ab dem die Bestellungen erstmalig importiert werden sollen. Bitte beachten Sie, dass dies nicht beliebig weit in die Vergangenheit möglich ist, da die Daten bei {#setting:currentMarketplaceName#} höchstens einige Wochen lang vorliegen.',
    ),
    'customergroup' => array(
        'label' => 'Kundengruppe',
        'help' => 'Kundengruppe, zu der Kunden bei neuen Bestellungen zugeordnet werden sollen.',
    ),
    'orderimport.shop' => array(
        'label' => '{#i18n:form_config_orderimport_shop_lable#}',
        'help' => '{#i18n:form_config_orderimport_shop_help#}',
    ),
    'orderstatus.open' => array(
        'label' => 'Bestellstatus im Shop',
        'help' => '
            Der Status, den eine von {#setting:currentMarketplaceName#} neu eingegangene Bestellung im Shop automatisch bekommen soll.<br />
            Sollten Sie ein angeschlossenes Mahnwesen verwenden, ist es empfehlenswert, den Bestellstatus auf "Bezahlt" zu setzen (Konfiguration → Bestellstatus).
        ',
    ),
    'orderimport.shippingmethod' => array(
        'label' => 'Versandart der Bestellungen',
        'help' => '
            Versandart, die allen {#setting:currentMarketplaceName#}-Bestellungen zugeordnet wird. Standard: "{#setting:currentMarketplaceName#}".<br>
            <br>
            Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck und f&uuml;r die nachtr&auml;gliche Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.
        ',
    ),
    'orderimport.paymentmethod' => array(
        'label' => 'Zahlart der Bestellungen',
        'help' => '
            Zahlart, die allen {#setting:currentMarketplaceName#}-Bestellungen zugeordnet wird. Standard: "{#setting:currentMarketplaceName#}".<br /><br />
            Diese Einstellung ist wichtig für den Rechnungs- und Lieferscheindruck und für die nachträgliche Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.
        ',
    ),
    'mwst.fallback' => array(
        'label' => 'MwSt. Shop-fremder Artikel',
        'hint' => 'Steuersatz, der f&uuml;r Shop-fremde Artikel bei Bestellimport verwendet wird in %.',
        'help' => '
            Wenn beim Bestellimport die Artikelnummer eines Kaufs im Web-Shop nicht erkannt wird, kann die Mehrwertsteuer nicht berechnet werden.<br />
            Als Lösung wird der hier angegebene Wert in Prozent bei allen Produkten hinterlegt, deren Mehrwertsteuersatz beim Bestellimport aus {#setting:currentMarketplaceName#} nicht bekannt ist.
        ',
    ),
    'orderstatus.sync' => array(
        'label' => 'Status Synchronisierung',
        'help' => '
            <strong>Automatische Synchronisierung per CronJob (empfohlen)</strong><br />
            Die Funktion "Automatische Synchronisierung per CronJob" übermittelt alle 2 Stunden den aktuellen Versendet-Status zu {#setting:currentMarketplaceName#}.<br />
            Dabei werden die Status-Werte aus der Datenbank geprüft und übernommen, auch wenn die Änderungen durch z.B. eine Warenwirtschaft nur in der Datenbank erfolgten.<br />
            <br />
            Einen manuellen Abgleich können Sie anstoßen, indem Sie die Bestellung direkt im Web-Shop bearbeiten, dort den gewünschten Status setzen, und dann auf "Aktualisieren" klicken.<br />
            Sie können auch den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise), um den Status sofort zu übergeben.<br />
            <br />
            Zusätzlich können Sie den Bestellstatus-Abgleich (ab Tarif Premium - maximal viertelstündlich) auch durch einen eigenen CronJob anstoßen, indem Sie folgenden Link zu Ihrem Shop aufrufen:<br />
            <br />
            <i>{#setting:sSyncOrderStatusUrl#}</i><br/><br/>
            Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Premium sind, oder die häufiger als viertelstündlich laufen, werden geblockt.
        ',
    ),
    'orderstatus.shipped' => array(
        'label' => 'Versand bestätigen mit',
        'help' => 'Setzen Sie hier den Shop-Status, der auf {#setting:currentMarketplaceName#} automatisch den Status "Versendet" setzen soll.',
    ),
    'orderstatus.carrier.default' => array (
        'label' => 'Spediteur',
        'help' => 'Vorausgew&auml;hlter Spediteur beim Best&auml;tigen des Versandes nach {#setting:currentMarketplaceName#}.',
    ),
    'orderstatus.canceled' => array(
        'label' => 'Bestellung stornieren mit',
        'help' => '
            Wählen Sie hier den Shop-Status, der zu {#setting:currentMarketplaceName#} automatisch den Status "Bestellung storniert" übermitteln soll.<br />
            <br />
            <strong>Hinweis:</strong> Teilstorno ist hierüber nicht möglich. Die gesamte Bestellung wird über diese Funktion storniert.
        ',
    ),
));