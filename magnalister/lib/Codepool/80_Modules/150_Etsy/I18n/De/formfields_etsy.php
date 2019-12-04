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
* (c) 2010 - 2018 RedGecko GmbH -- http://www.redgecko.de
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

MLI18n::gi()->add('formfields_etsy', array(
    'shippingtemplatetitle' => array(
        'label' => 'Versandgruppen Titel',
    ),
    'shippingtemplatecountry' => array(
        'label' => 'Herkunftsland',
        'help' => 'Land, aus dem das Produkt versendet wird',
    ),
    'shippingtemplateprimarycost' => array(
        'label' => 'Primärkosten',
        'help' => 'Die Versandkosten für diesen Artikel, wenn er allein versandt wird.',
    ),
    'shippingtemplatesecondarycost' => array(
        'label' => 'Sekundäre Kosten',
        'help' => 'Die Versandkosten für diesen Artikel, wenn er mit einem anderen Artikel verschickt wird.',
    ),
    'shippingtemplatesend' => array(
        'label' => 'Versandgruppe erstellen',
    ),
    'whomade' => array(
        'values' => array(
            'i_did' => 'Ich war\'s',
            'collective' => 'Ein Mitglied meines Shops',
            'someone_else' => 'Eine andere Firma oder Person',
        ),
    ),
    'whenmade' => array(
        'values' => array(
            'made_to_order' => 'Produktion auf Bestellung',
            '2010_2018' => '2010-2018',
            '2000_2009' => '2000-2009',
            '1999_1999' => '1999-1999',
            'before_1999' => 'Vor 1999',
            '1990_1998' => '1990-1998',
            '1980s' => '1980ern',
            '1970s' => '1970ern',
            '1960s' => '1960ern',
            '1950s' => '1950ern',
            '1940s' => '1940ern',
            '1930s' => '1930ern',
            '1920s' => '1920ern',
            '1910s' => '1910ern',
            '1900s' => '1900er',
            '1800s' => '1800er',
            '1700s' => '1700er',
            'before_1700' => 'Vor 1700'
        ),
    ),
    'issupply' => array(
        'values' => array(
            'false' => 'Ein fertiges Produkt',
            'true' => 'Zubehör oder ein Werkzeug, um etwas herzustellen',
        ),
    ),
    'access.username' => array(
        'label' => 'Etsy Username',
    ),
    'access.password' => array(
        'label' => 'Etsy Passwort',
    ),
    'access.token' => array(
        'label' => 'Etsy Token',
    ),
    'shop.language' => array(
        'label' => 'Etsy Sprache',
        'values' => array(
            'de' => 'Deutsch',
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'it' => 'Italiano',
            'ja' => '日本語',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'pt' => 'Português',
            'ru' => 'Русский',
        ),
    ),
    'shop.currency' => array(
        'label' => 'Etsy Währung',
        'values' => array(
            'EUR' => '€ Euro',
            'USD' => '$ US-Dollar',
            'CAD' => '$ Kanadischer Dollar',
            'GBP' => '£ Britische Pfund',
            'AUD' => '$ Australischer Dollar',
            'DDK' => 'kr Dänische Krone',
            'HKD' => '$ Honkong-Dollar',
            'NZD' => '$ Neuseeländischer Dollar',
            'NOK' => 'kr Norwegische Krone',
            'SGD' => '$ Singapur-Dollar',
            'SEK' => 'kr Schwedische Krone',
            'CHF' => 'Schweizer Franken',
            'TWD' => 'NT$ Neuer Taiwan-Dollar',
        ),
    ),
    'prepare.imagesize' => array(
        'label' => 'Bildgr&ouml;&szlig;e',
        'help' => '<p>Geben Sie hier die Pixel-Breite an, die Ihr Bild auf dem Marktplatz haben soll.
            Die H&ouml;he wird automatisch dem urspr&uuml;nglichen Seitenverh&auml;ltnis nach angepasst.</p>
            <p>Die Quelldateien werden aus dem Bildordner {#setting:sSourceImagePath#} verarbeitet und mit der hier gew&auml;hlten Pixelbreite im Ordner {#setting:sImagePath#}  f&uuml;r die &Uuml;bermittlung zum Marktplatz abgelegt.</p>',
        'hint' => 'Gespeichert unter: {#setting:sImagePath#}'
    ),
    'prepare.whomade' => array(
        'label' => 'Wer hat es gemacht?',
    ),
    'prepare.whenmade' => array(
        'label' => 'Wann hast du es gemacht?',
    ),
    'prepare.issupply' => array(
        'label' => 'Was ist es?',
    ),
    'fixed.price' => array(
        'label' => 'Preis',
        'help' => 'Geben Sie einen prozentualen oder fest definierten Preis Auf- oder Abschlag an. Abschlag mit vorgesetztem Minus-Zeichen.'
    ),
    'fixed.price.addkind' => array(
        'label' => '',
    ),
    'fixed.price.factor' => array(
        'label' => '',
    ),
    'fixed.price.signal' => array(
        'label' => 'Nachkommastelle',
        'hint' => 'Nachkommastelle',
        'help' => '
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu ebay als Nachkommastelle an Ihrem Preis &uuml;bernommen.<br/><br/>
                <strong>Beispiel:</strong> <br />
                Wert im Textfeld: 99 <br />
                Preis-Ursprung: 5.58 <br />
                Finales Ergebnis: 5.99 <br /><br />
                Die Funktion hilft insbesondere bei prozentualen Preis-Auf-/Abschl&auml;gen.<br/>
                Lassen Sie das Feld leer, wenn Sie keine Nachkommastelle &uuml;bermitteln wollen.<br/>
                Das Eingabe-Format ist eine ganzstellige Zahl mit max. 2 Ziffern.
            '
    ),
    'prepare.language' => array(
        'label' => 'Sprache',
    ),
    'shippingtemplate' => array(
        'label' => 'Standard Versandgruppe',
        'hint' => '<button id="shippingtemplateajax" class="mlbtn action add-matching" value="Secondary_color" style="display: inline-block;">+</button>',
    ),
    'prepare_title' => array(
        'label' => 'Titel',
        'optional' => array(
            'checkbox' => array(
                'labelNegativ' => 'Titel immer aktuell aus Web-Shop verwenden',
            )
        ),
    ),
    'prepare_description' => array(
        'label' => 'Beschreibung',
        'help' => 'Die maximale Anzahl der Zeichen beträgt 63000.',
        'optional' => array(
            'checkbox' => array(
                'labelNegativ' => 'Artikelbeschreibung immer aktuell aus Web-Shop verwenden',
            )
        ),
    ),
    'prepare_image' => array(
        'label' => 'Produktbilder',
        'help' => 'Maximal können 10 Bilder eingestellt werden.<br/>Maximal zulässige Bildgröße ist 3000 x 3000 px.',
        'hint' => 'Maximal 10 Bilder'
    ),
    'category' => array(
        'label' => 'Kategorie',
    ),
    'prepare_price' => array(
        'label' => 'Preis',
        'help' => 'Minimaler Artikelpreis auf Etsy ist 0.17£.',
    ),
    'prepare_quantity' => array(
        'label' => 'Bestand',
        'help' => 'Der Bestand für ein Produkt darf nicht größe als 999 sein.',
    ),
));