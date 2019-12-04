<?php
MLI18n::gi()->idealo_config_account_title = 'Zugangsdaten';
MLI18n::gi()->idealo_config_account_prepare = 'Artikelvorbereitung';
MLI18n::gi()->idealo_config_account_price = 'Preisberechnung';
MLI18n::gi()->idealo_config_account_sync = 'Synchronisation';
MLI18n::gi()->idealo_config_account_orderimport = 'Bestellimport';
MLI18n::gi()->idealo_config_account_emailtemplate = '{#i18n:configform_emailtemplate_legend#}';
MLI18n::gi()->idealo_config_message_no_csv_table_yet = 'Noch keine CSV-Tabelle erstellt: Bitte stellen Sie zuerst Artikel ein. Danach finden Sie hier den CSV-Pfad.';
MLI18n::gi()->idealo_methods_not_available = 'Bitte hinterlegen und speichern Sie erst den Direktkauf-Token unter „Bestellimport > Zugangsdaten für Idealo Direktkauf"';

MLI18n::gi()->idealo_configform_orderimport_payment_values = array(
    'textfield' => array(
        'title' => 'Aus Textfeld',
        'textoption' => true
    ),
    'matching' => array(
        'title' => 'Automatische Zuordnung',
    ),
);