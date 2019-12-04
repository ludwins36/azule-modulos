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
        'label' => 'Status Filter',
        'valuehint' => 'Only transfer active items',
        'help' => 'Items can be active or inactive in your store.',
    ),
    'lang' => array (
        'label' => 'Item Description',
    ),
    'prepare.status' => array(
        'label' => '{#i18n:formfields__checkin.status__label#}',
        'valuehint' => '{#i18n:formfields__checkin.status__valuehint#}',
        'help' => 'Items can be active or inactive in your store.',
    ),
    'tabident' => array(
        'label' => '{#i18n:ML_LABEL_TAB_IDENT#}',
        'help' => '{#i18n:ML_TEXT_TAB_IDENT#}',
    ),
    'stocksync.tomarketplace' => array(
        'label' => 'Stock Sync to Marketplace',
        'help' => '
            Hint: idealo supports only "available" and "not available" for your offers.<br />
            <br />
            Stock shop > 0 = availible on idealo<br />
            Stock shop < 1 = not avilible on idealo<br />
            <br />
            <br />
            Function:<br />
            Automatic synchronisation by CronJob (recommended)<br />
            <br />
            <br />
            The function "Automatic Synchronisation by CronJob" checks the shop stock every 4 hours*<br />
            <br />
            <br />
            By this procedure, the database values are checked for changes. The new data will be submitted, also when the changes had been set by an inventory management system.<br />
            <br />
            You can manually synchronize stock changes, by clicking the assigned button in the magnalister-header, next left to the ant-logo.<br />
            <br />
            Additionally, you can synchronize stock changes, by setting a own cronjob to your following shop-link:<br />
            <i>{#setting:sSyncInventoryUrl#}</i><br />
            <br />
            Own cronjob-calls, exceeding a quarter of an hour will be blocked.<br />
            <br />
            <br />
            Hint: The config value "Configuration" → "Presets" ...<br />
            <br />
            → "Orderlimit for one day" and<br />
            → "shop stock"<br />
            will be consided.
        ',
    ),
    'stocksync.frommarketplace' => array(
        'label' => 'Orderimport from {#setting:currentMarketplaceName#}',
        'help' => 'For example: If 3 items are sold on idealo.de, the shop-stock will be reduced by 3 items, too.',
    ),
    'inventorysync.price' => array(
        'label' => 'Price Options',
        'help' => '
            This function allows you to define other prices for idealo. These prices will be used in item upload as well as in the price synchronization.<br />
            <br />
            <ul>
                <li>Use a customer group, or define an own customer group where you place the prices for the marketplace.</li>
                <li>If an item has no price defined for the price group chosen, the default price will be used.</li>
            </ul>
            <br />
            This way, you can change the prices for only a few items without changing the calculation rules for everything.<br />
            The other configuration settings (Markup/Markdown and Decimal amount) apply here as well.
        ',
    ),
    'mail.send' => array(
        'label' => 'Send E-Mail?',
        'help' => 'Should an email be sent from your Shop to customers, to promote your Shop?',
    ),
    'mail.originator.name' => array(
        'label' => 'Sender Name',
        'default' => 'Example-Shop',
    ),
    'mail.originator.adress' => array(
        'label' => 'Sender E-Mail Address',
        'default' => 'example@onlineshop.de',
    ),
    'mail.subject' => array(
        'label' => 'Subject',
        'default' => 'Your order at #SHOPURL#',
    ),
    'mail.content' => array(
        'label' => 'E-Mail Content',
        'hint' => '
            List of available placeholders for Subject and Content:
            <dl>
                <dt>#FIRSTNAME#</dt>
                        <dd>Buyer\'s first name</dd>
                <dt>#LASTNAME#</dt>
                        <dd>Buyer\'s last name</dd>
                <dt>#EMAIL#</dt>
                        <dd>Buyer\'s email address</dd>
                <dt>#PASSWORD#</dt>
                        <dd>Buyer\'s password for logging in to your Shop. Only for customers that are automatically assigned passwords – otherwise the placeholder will be replaced with \'(as known)\'***.</dd>
                <dt>#ORDERSUMMARY#</dt>
                        <dd>Summary of the purchased items. Should be written on a separate line. <br/><i>Cannot be used in the Subject!</i></dd>
                <dt>#ORIGINATOR#</dt>
                        <dd>Sender name</dd>
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
<p>Hello #FIRSTNAME# #LASTNAME#,</p>
<p>Thank you for your order! The following items were purchased via #MARKETPLACE#:</p>
#ORDERSUMMARY#
<p>Shipping costs are included.</p>
<p>&nbsp;</p>
<p>Sincerely,</p>
<p>Your Online Shop Team</p>
    '),
    'mail.copy' => array(
        'label' => 'Copy to Sender',
        'help' => 'A copy will be sent to the sender email address.',
    ),
    'quantity' => array(
        'label' => 'Inventory Item Count',
        'help' => '
            Please enter how much of the inventory should be available on the marketplace.<br/>
            <br/>
            You can change the individual item count directly under \'Upload\'. In this case it is recommended that you turn off automatic<br/>
            synchronization under \'Synchronization of Inventory\' > \'Stock Sync to Marketplace\'.<br/>
            <br/>
            To avoid overselling, you can activate \'Transfer shop inventory minus value from the right field\'.
            <br/>
            <strong>Example:</strong> Setting the value at 2 gives &#8594; Shop inventory: 10 &#8594; {#setting:currentMarketplaceName#} inventory: 8<br/>
            <br/>
            <strong>Please note:</strong>If you want to set an inventory count for an item in the Marketplace to \'0\', which is already set as Inactive in the Shop, independent of the actual inventory count, please proceed as follows:<br/>
            <ul>
                <li>\'Synchronize Inventory"> Set "Edit Shop Inventory" to "Automatic Synchronization with CronJob"</li>
                <li>"Global Configuration" > "Product Status" > Activate setting "If product status is inactive, treat inventory count as 0"</li>
            </ul>
        ',
    ),
    'maxquantity' => array(
        'label' => 'Limitation for number of items',
        'help' => '
            Here you can limitate the number of items published on {#setting:currentMarketplaceName#}.<br /><br />
            <strong>Example:</strong>
            For “number of items” you select “take inventory from shop” and enter “20” in this field. While upload number of items will be taken from available inventory but not more then 20. The inventory synchronisation (if activated) will adapt the {#setting:currentMarketplaceName#}-number of items to the shop-inventory as long as the shop-inventory is less then 20. If there are more then 20 items in the inventory, the {#setting:currentMarketplaceName#} number of items is set to 20.<br /><br />
            Please insert “0” or let this field blank if you do not want a limitation.<br /><br />
            <strong>Hint:</strong>
            If the “number of items” option is “global (from the right field)”, limitation has no effect.
        ',
    ),
    'priceoptions' => array(
        'label' => 'Price Options',
        'help' => '{#i18n:configform_price_field_priceoptions_help#}',
    ),
    'price.usespecialoffer' => array(
        'label' => 'Use special offer prices',
    ),
    'exchangerate_update' => array(
        'label' => 'Exchange Rate',
        'valuehint' => 'Update exchange rate automatically',
        'help' => '{#i18n:form_config_orderimport_exchangerate_update_help#}',
        'alert' => '{#i18n:form_config_orderimport_exchangerate_update_alert#}',
    ),
    'importactive' => array(
        'label' => 'Activate Import',
        'help' => '
            Should orders from {#setting:currentMarketplaceName#} be imported?<br />
            <br />
            Orders are imported automatically every hour as the default setting.<br />
            <br />
            You can adjust the import-time individually, by configuring<br />
            "magnalister admin" → "Global Configuration" → "Orders Import".<br />
            <br />
            You can manually import orders, by clicking the assigned button in the magnalister-header, next left to the ant-logo.<br />
            <br />
            Additionally, you can call order imports by setting a own cronjob to your following shop-link:<br />
            <i>{#setting:sImportOrdersUrl#}</i><br />
            <br />
            Own cronjob-calls, exceeding a quarter of an hour will be blocked.
        ',
    ),
    'preimport.start' => array(
        'label' => 'First-time order-import',
        'hint' => 'Start date',
        'help' => 'The date, an order import will be processed the first time.',
    ),
    'customergroup' => array(
        'label' => 'Customer Group',
        'help' => 'Allocate customers from {#setting:currentMarketplaceName#} to a customer group of the shop.',
    ),
    'orderimport.shop' => array(
        'label' => '{#i18n:form_config_orderimport_shop_lable#}',
        'help' => '{#i18n:form_config_orderimport_shop_help#}',
    ),
    'orderstatus.open' => array(
        'label' => 'Order Status',
        'help' => '
            The status a new order from {#setting:currentMarketplaceName#} gets automatically in the shop.<br />
            If you use an attached dunning process, it is recommended to set the status to "Paid" (configuration → purchase order status).
        ',
    ),
    'orderimport.shippingmethod' => array(
        'label' => 'Shipping Service of the Orders',
        'help' => '
            Shipping method that will apply to all orders imported from {#setting:currentMarketplaceName#}. Standard: “{#setting:currentMarketplaceName#}”<br><br>
            This setting is necessary for the invoice and shipping notice, and for editing orders later in the Shop or via ERP.
        ',
    ),
    'orderimport.paymentmethod' => array(
        'label' => 'Payment Methods',
        'help' => '
            <p>Payment method that will apply to all orders imported from {#setting:currentMarketplaceName#}. Standard: “Automatic Allocation”</p>
            <p>If you choose “Automatic Allocation”, magnalister will accept the payment method chosen by the buyer on {#setting:currentMarketplaceName#}.</p>
            <p>Additional payment methods can be added to the list via Shopware > Settings > Payment Methods, then activated here.</p>
            <p>This setting is necessary for the invoice and shipping notice, and for editing orders later in the Shop or via ERP.</p>
        ',
    ),
    'mwst.fallback' => array(
        'label' => 'VAT on Non-Shop*** Items',
        'hint' => 'The tax rate to apply to non-Shop items on order imports, in %.',
        'help' => '
            If an item is not entered in the web-shop, magnalister uses the VAT from here since marketplaces give no details to VAT within the order import.<br />
            <br />
            Further explanation:<br />
            Basically, magnalister calculates the VAT the same way the shop-system does itself.<br />
            VAT per country can only be considered if the article can be found in the web-shop with his number range (SKU).<br />
            magnalister uses the configured web-shop-VAT-classes.
        ',
    ),
    'orderstatus.sync' => array(
        'label' => 'Status Synchronization',
        'help' => '
            <dl>
                <dt>Automatic Synchronization via CronJob (recommended)</dt>
                <dd>
                    The function \'Automatic Synchronization with CronJob\' transfers the current Sent Status to {#setting:currentMarketplaceName#} every 2 hours.<br/>
                    The status values from the database will be checked and transferred, including when the changes are only made to the database, for example, with an ERP. <br/><br/>
                    To do a manual comparison, which allows you to edit the order directly in the web shop, set the desired status there and then click \'refresh\'.<br/>
                    Click the button in the magnalister header (left of the shopping cart) to transfer the status immediately.<br/><br/>
                    Additionally you can activate the Order Status Comparison through CronJob (flat tariff*** - maximum every 4 hours) with the link: <br/><br/>
                    <i>{#setting:sSyncOrderStatusUrl#}</i><br/><br/>
                    Some CronJob requests may be blocked, if they are made through customers not on the flat tariff*** or if the request is made more than once every 4 hours. 
                </dd>
            </dl>
        ',
    ),
    'orderstatus.shipped' => array(
        'label' => 'Confirm Shipping With',
        'help' => 'Please set the Shop Status that should trigger the \'Shipping Confirmed\' status on {#setting:currentMarketplaceName#}.',
    ),
    'orderstatus.carrier.default' => array (
        'label' => 'Carrier',
        'help' => 'Pre-selected carrier with confirmation of distribution to {#setting:currentMarketplaceName#}.',
    ),
    'orderstatus.canceled' => array(
        'label' => 'Cancel Order With',
        'help' => '
            Here you set the shop status which will set the {#setting:currentMarketplaceName#} order status to „cancel order“. <br/><br/>
            Note: partial cancellation is not possible in this setting. The whole order will be cancelled with this function und credited tot he customer
        ',
    ),
));