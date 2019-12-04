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
// example for overwriting global element
MLI18n::gi()->add('formfields__quantity', array('help' => '{#setting:currentMarketplaceName#} kennt nur Lagermenge "Verfügbar" oder "Nicht verfügbar". Geben Sie hierüber an, ob Lagermenge entsprechend Ihres Shop-Lagerbestandes auf {#setting:currentMarketplaceName#} verfügbar sein soll.<br><br>Um Überverkäufe zu vermeiden, können Sie den Wert "Shop-Lagerbestand übernehmen und abzgl. "Wert aus rechtem Feld" aktivieren.<br><br><b>Beispiel:</b> Wert auf "2" setzen. Ergibt → Shoplager: 2 → {#setting:currentMarketplaceName#}-Lager: Artikel nicht verfügbar (0).<br><br> <b>Hinweis:</b> Wenn Sie Artikel, die im Shop inaktiv gesetzt werden, unabhängig der verwendeten Lagermengen auch auf {#setting:currentMarketplaceName#} als Lager "0" behandeln wollen, gehen Sie bitte wie folgt vor:<br><ul><li>"Synchronisation des Inventars" &gt; "Lagerveränderung Shop" auf "automatische Synchronisation per CronJob" einstellen</li><li>"Globale Konfiguration" &gt; "Produktstatus" &gt; "Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt" aktivieren</li></ul>'));
MLI18n::gi()->add('formfields__stocksync.tomarketplace', array('help' => '
    <b>Hinweis:</b> Da {#setting:currentMarketplaceName#} nur "verfügbar" oder "nicht verfügbar" für Ihre Angebote kennt, wird hierbei berücksichtigt:<br>
    <br>
    <ul>
        <li>Lagermenge Shop &gt; 0 = verfügbar auf {#setting:currentMarketplaceName#}</li>
        <li>Lagermenge Shop &lt; 1 = nicht auf {#setting:currentMarketplaceName#} verfügbar</li>
    </ul>
    <br>
    Funktion:<br>
    Automatische Synchronisierung per CronJob (empfohlen)<br>
    <br>
    <br>
    Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden den aktuellen {#setting:currentMarketplaceName#}-Lagerbestand an den Shop-Lagerbestand an*<br>
    <br>
    <br>
    Dabei werden die Werte aus der Datenbank geprüft und übernommen, auch wenn die Änderungen durch z.B. eine Warenwirtschaft nur in der Datenbank erfolgten.<br>
    <br>
    Einen manuellen Abgleich können Sie anstoßen, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br>
    <br>
    Zusätzlich können Sie den Lagerabgleich (ab Tarif Flat - maximal viertelstündlich) auch durch einen eigenen CronJob anstoßen, indem Sie folgenden Link zu Ihrem Shop aufrufen:<br>
    <i>{#setting:sSyncInventoryUrl#}</i><br>
    <br>
    Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die häufiger als viertelstündlich laufen, werden geblockt.<br>
    <br>
    <br>
    <br>
    <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" → "Einstellvorgang" ...<br>
    <br>
    → "Bestelllimit pro Kalendertag" und<br>
    → "Stückzahl Lagerbestand" für die ersten beiden Optionen.<br><br>… werden berücksichtigt.
'));
MLI18n::gi()->add('formfields__stocksync.frommarketplace', array('label' => 'Bestellimport Direktkauf'));
MLI18n::gi()->add('formfields__maxquantity', array(
    'label' => 'Bestelllimit pro Kalendertag',
    'help' => '
        Bestelllimit pro Kalendertag für den Direktkauf:<br />
        <br />
        Sie können hier die Anzahl angeben, die je Artikel am Kalendertag über den Direktkauf verkauft werden dürfen. Ohne diese Angabe bleibt der Artikel im Direktkauf verfügbar bis das Angebot entweder gelöscht, oder Einstellungen geändert werden.<br />
        <br />
        Bitte beachten Sie: Dieses Feld sollte nicht mit Ihrem Lagerbestand verwechselt werden. Hier geht es um ein Tageslimit, welches für den {#setting:currentMarketplaceName#} Direktkauf bestimmt wird.<br />
        <br />
        <br />
        <strong>Hinweis:</strong><br />
        Einstellungen unter "Stückzahl Lagerbestand" werden bis zum hier eingestellten Bestelllimit berücksichtigt.<br />
        <br />
        Wenn die "Stückzahl"-Einstellung "Pauschal (aus rechtem Feld)" aktiviert ist, hat das Limit keine Wirkung.
    ',
));


MLI18n::gi()->add('formfields_idealo', array(
    'idealotoken' => array(
        'label' => '{#setting:currentMarketplaceName#} Direktkauf Token',
        'help' => '
            Sollten Sie {#setting:currentMarketplaceName#} Direktkauf nutzen, tragen Sie hier bitte den Token ein, den {#setting:currentMarketplaceName#} Ihnen dafür zur Verfügung stellt. Ohne gültigen Token können die Direktkauf Funktionen nicht genutzt werden.<br />
            <br />
            Um den Direktkauf zu deaktivieren, löschen Sie den Token und klicken Sie auf "Daten speichern".
        '
    ),
    'shippingcountry' => array (
        'label' => 'Versand nach',
    ),
    'shippingmethodandcost' => array(
        'label' => 'Versandkosten',
        'help' => 'Tragen Sie hier die pauschalen Versandkosten für Ihre Artikel in Euro ein. In der Produktvorbereitung können Sie die Werte für die ausgewählten Artikel individuell speichern.',
    ),
    'shippingcostmethod' => array(
        'values' => array(
            '__ml_lump' => MLI18n::gi()->ML_COMPARISON_SHOPPING_LABEL_LUMP,
            '__ml_weight' => 'Versandkosten = Artikel-Gewicht',
        ),
    ),
    'subheader.d' => array(
        'label' => 'Direktkauf'
    ),
    'subheader.pd' => array(
        'label' => 'Preissuchmaschine und Direktkauf'
    ),
    'paymentmethod' => array(
        'label' => 'Zahlungsart',
        'help' => '
            Geben Sie hier die gewünschten Standard-Zahlungsarten für das Preisvergleichs-Portal und Direktkauf an (Mehrfachauswahl möglich).<br />
            Sie können die Zahlungsarten unter "Produkte vorbereiten" jederzeit individuell für die vorzubereitenden Produkte anpassen.<br />
            <br />
            <strong>Hinweis:</strong> {#setting:currentMarketplaceName#} bietet für Direktkauf nur die Zahlarten PayPal, Sofortüberweisung und Kreditkarte an.<br />
            Die für Direktkauf gewählten Zahlarten werden automatisch auch im Preisvergleichsportal angegeben.
        ',
        'values' => array(
            'Direktkauf & Suchmaschine:' => array(
                'PAYPAL' => 'PayPal',
                'CREDITCARD' => 'Kreditkarte',
                'SOFORT' => 'Sofort&uuml;berweisung'
            ),
            'Nur Suchmaschine:' => array(
                'PRE' => 'Vorkasse',
                'COD' => 'Nachnahme',
                'BANKENTER' => 'Bankeinzug',
                'BILL' => 'Rechnung',
                'GIROPAY' => 'Giropay',
                'CLICKBUY' => 'Click&Buy',
                'SKRILL' => 'Skrill'
            ),
        ),
    ),
    'checkout' => array(
        'label' => 'Direktkauf',
        'valuehint' => 'Artikel für Direktkauf freigeben'
    ),
    'checkoutenabled' => array(
        'label' => 'idealo Direktkauf',
        'help' => 'Zur Nutzung dieser Funktion hinterlegen Sie bitte unter dem Reiter "<a href="{#setting:idealo.activatedirectbuyconfigurl#}">Zugangsdaten</a>" den idealo Direktkauf Token.',
    ),
    'access.inventorypath' => array(
        'label' => 'Pfad zu Ihrer CSV-Tabelle',
    ),
    'shippingmethod' => array(
        'label' => 'Versandart',
        'help' => 'Geben Sie hier an, welche Versandart für Ihre Direktkauf-Angebote gelten sollen.',
        'values' => array(
            'Paketdienst' => 'Paketdienst',
            'Spedition' => 'Spedition',
        ),
    ),
    'shippingtime' => array(
        'label' => 'Versandzeit',
        'optional' => array(
            'checkbox' => array(
                'labelNegativ' => 'immer aus Konfiguration übernehmen',
            ),
        )
    ),
    'shippingtimetype' => array(
        'values' => array(
            '__ml_lump' => array('title' => 'Pauschal (aus rechtem Feld)',),
            'immediately' => array('title' => 'sofort lieferbar',),
            '4-6days' => array('title' => 'ca. 4-6 Werktage',),
            '1-2days' => array('title' => 'ab Zahlungseingang innerhalb 1-2 Werktagen beim Kunden',),
            '2-3days' => array('title' => '2-3 Tage',),
            '4weeks' => array('title' => 'Lieferzeit: 4 Wochen',),
            '24h' => array('title' => 'versandfertig in 24 Stunden',),
            '1-3days' => array('title' => 'sofort lieferbar, 1 - 3 Werktage',),
            '3days' => array('title' => 'versandfertig in 3 Tagen',),
            '3-5days' => array('title' => '3-5 Werktage',),
        ),
    ),
    'shippingtimeproductfield' => array(
        'label' => 'Versandzeit (Matching)',
        'help' => '
            Über das Versandzeit-Matching können Sie am Artikel hinterlegte Attribute als Versandzeit automatisiert zu {#setting:currentMarketplaceName#} hochladen.<br />
            In der DropDown-Auswahl sehen Sie alle Attribute, die aktuell für Artikeln definiert sind. Sie können jederzeit neue Attribute über die Shop-Verwaltung hinzufügen und verwenden.
        ',
    ),
    'orderstatus.cancelreason' => array (
        'label' => 'Bestell-Storno Grund',
        'help' => '
            Wählen Sie hier den Standard-Storno Grund:
            <ul>
                <li>"Kunden-Widerruf"</li>
                <li>"Händler kann nicht liefern"</li>
                <li>"Retoure"</li>
            </ul>
        '
    ),
    'orderstatus.cancelcomment' => array (
        'label' => 'Bestell-Storno Kommentar',
    ),
    'prepare_title' => array(
        'label' => 'Titel',
    ),
    'prepare_description' => array(
        'label' => 'Beschreibung',
    ),
    'prepare_image' => array(
        'label' => 'Produktbilder',
        'hint' => 'Maximal 3 Produktbilder ',
    ),
));