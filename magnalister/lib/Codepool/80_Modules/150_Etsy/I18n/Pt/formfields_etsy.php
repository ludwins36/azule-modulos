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
MLI18n::gi()->add('formfields__quantity', array('help' => 'As stock {#setting:currentMarketplaceName#} supports only "availible" or "not availible".<br />Here you can define how the threshold for availible items.'));
MLI18n::gi()->add('formfields__stocksync.frommarketplace', array('help' => '
     <b>Note</b>: {#setting:currentMarketplaceName#} knows only "available" or "not available". Therefore:<br>
    <br>
    <ul>
        <li>Shop&apos;s stock quantity  &gt; 0 = available on {#setting:currentMarketplaceName#}</li>
        <li>Shop&apos;s stock quantity  &lt; 0 = not available on {#setting:currentMarketplaceName#}</li>
    </ul>
    <br>
    Function:<br>
    Automatic Synchronization by CronJob (recommended)<br>
    <br>
    <br>
    The function "Automatic Synchronisation by CronJob" equalizes the current {#setting:currentMarketplaceName#}-stock with the shop-stock every 4 hours.*<br>
    <br>
    <br>
    By this procedure, the database values are checked for changes. The new data will be submitted, also when the changes have been set by an inventory management system.<br>
    <br>
    You can manually synchronize stock changes, by clicking the assigned button in the magnalister-header, next left to the ant-logo.<br>
    <br>
    Additionally, you can synchronize stock changes, by setting a own cronjob to your following shop-link:<br>    <i>{#setting:sSyncInventoryUrl#}</i><br>
    <br>
    Setting an own cronjob is permitted for customers within the service plan "Flat", only.<br>
    Own cronjob-calls, exceeding a quarter of an hour, or calls from customers, who are not within the service plan "Flat", will be blocked.<br>
    <br>
    <br>
    <br>
    <b>Note:</b>Settings in "Configuration" + "Listing Process" ...<br>
    <br>
    + "order limit per day"
    + "quantity"  for the first two options.<br><br>... will be considered.
'));

MLI18n::gi()->add('formfields_etsy', array(
    'shippingtemplatetitle' => array(
        'label' => 'Shipping template title',
    ),
    'shippingtemplatecountry' => array(
        'label' => 'Origin country',
        'help' => 'Country from which the listing ships',
    ),
    'shippingtemplateprimarycost' => array(
        'label' => 'Primary cost',
        'help' => 'The shipping fee for this item, if shipped alone',
    ),
    'shippingtemplatesecondarycost' => array(
        'label' => 'Secondary cost',
        'help' => 'The shipping fee for this item, if shipped with another item',
    ),
    'shippingtemplatesend' => array(
        'label' => 'Save shipping template',
    ),
    'paymentmethod' => array(
        'label' => 'Payment Methods',
        'help' => '
            Select here the default payment methods for comparison shopping portal and direct-buy (multi selection is possible).<br />
            You can change these payment methods during item preparation.<br />
            <br />
            <strong>Caution:</strong> {#setting:currentMarketplaceName#} exclusively accepts PayPal, Sofortüberweisung and credit card as payment methods for direct-buy.',
        'values' => array(
            'Direktkauf & Suchmaschine:' => array(
                'PAYPAL' => 'PayPal',
                'CREDITCARD' => 'Credit Card',
                'SOFORT' => 'Sofort&uuml;berweisung'
            ),
            'Nur Suchmaschine:' => array(
                'PRE' => 'payment in advance',
                'COD' => 'cash on delivery',
                'BANKENTER' => 'bank enter',
                'BILL' => 'bill',
                'GIROPAY' => 'Giropay',
                'CLICKBUY' => 'Click&Buy',
                'SKRILL' => 'Skrill'
            ),
        ),
    ),
    'whomade' => array(
        'values' => array(
            'i_did' => 'I did',
            'collective' => 'A member of my shop',
            'someone_else' => 'Another company or person',
        ),
    ),
    'whenmade' => array(
        'values' => array(
            'made_to_order' => 'Made to order',
            '2010_2018' => '2010-2018',
            '2000_2009' => '2000-2009',
            '1999_1999' => '1999-1999',
            'before_1999' => 'Before 1999',
            '1990_1998' => '1990-1998',
            '1980s' => '1980s',
            '1970s' => '1970s',
            '1960s' => '1960s',
            '1950s' => '1950s',
            '1940s' => '1940s',
            '1930s' => '1930s',
            '1920s' => '1920s',
            '1910s' => '1910s',
            '1900s' => '1900s',
            '1800s' => '1800s',
            '1700s' => '1700s',
            'before_1700' => 'Before 1700'
        ),
    ),
    'issupply' => array(
        'values' => array(
            'false' => 'A finished product',
            'true' => 'A supply or tool to make things',
        ),
    ),
    'access.username' => array(
        'label' => 'Etsy Username',
    ),
    'access.password' => array(
        'label' => 'Etsy Password',
    ),
    'access.token' => array(
        'label' => 'Etsy Token',
    ),
    'shop.language' => array(
        'label' => 'Etsy Language',
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
        'label' => 'Etsy Currency',
        'values' => array(
            'EUR' => '€ Euro',
            'USD' => '$ United States Dollar',
            'CAD' => '$ Canadian Dollar',
            'GBP' => '£ British Pound',
            'AUD' => '$ Australian Dollar',
            'DDK' => 'kr Danish Krone',
            'HKD' => '$ Hong Kong Dollar',
            'NZD' => '$ New Zealand Dollar',
            'NOK' => 'kr Norwegian Krone',
            'SGD' => '$ Singapore Dollar',
            'SEK' => 'kr Swedish Krona',
            'CHF' => 'Swiss Franc',
            'TWD' => 'NT$ Taiwan New Dollar',
        ),
    ),
    'prepare.imagesize' => array(
        'label' => 'Image size',
    ),
    'prepare.whomade' => array(
        'label' => 'Who made it?',
    ),
    'prepare.whenmade' => array(
        'label' => 'When did you make it?',
    ),
    'prepare.issupply' => array(
        'label' => 'What is it?',
    ),
    'fixed.price' => array(
        'label' => 'Price',
        'help' => 'Please enter a price markup or markdown, either in percentage or fixed amount. Use a minus sign (-) before the amount to denote markdown.'
    ),
    'fixed.price.addkind' => array(
        'label' => '',
    ),
    'fixed.price.factor' => array(
        'label' => '',
    ),
    'fixed.price.signal' => array(
        'label' => 'Decimal Amount',
        'hint' => 'Decimal Amount',
        'help' => 'This textfield shows the decimal value that will appear in the item price on Etsy.'
    ),
    'prepare.language' => array(
        'label' => 'Language',
    ),
    'shippingtemplate' => array(
        'label' => 'Default shipping template',
        'hint' => '<button id="shippingtemplateajax" class="mlbtn action add-matching" value="Secondary_color" style="display: inline-block;">+</button>',
    ),
    'prepare_title' => array(
        'label' => 'Title',
        'optional' => array(
            'checkbox' => array(
                'labelNegativ' => 'Always use product title from web-shop',
            )
        ),
    ),
    'prepare_description' => array(
        'label' => 'Description',
        'help' => 'Maximum number of characters is 63000.',
        'optional' => array(
            'checkbox' => array(
                'labelNegativ' => 'Always use product description from web-shop',
            )
        ),
    ),
    'prepare_image' => array(
        'label' => 'Product Images',
        'hint' => 'Maximum 10 images',
        'help' => 'A maximum 10 images can be set.<br/>The maximum allowed image size is 3000 x 3000 px.',
    ),
    'category' => array(
        'label' => 'Category',
    ),
    'prepare_price' => array(
        'label' => 'Price',
        'help' => 'Minimum item price on Etsy is 0.17£',
    ),
    'prepare_quantity' => array(
        'label' => 'Quantity',
        'help' => 'Maximum item on Etsy is 999',
    ),
));