<?php

/**
 * Copyright (c) D3 Data Development (Inh. Thomas Dartsch)
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * https://www.d3data.de
 *
 * @copyright (C) D3 Data Development (Inh. Thomas Dartsch)
 * @author    D3 Data Development - Daniel Seifert <info@shopmodule.com>
 * @link      https://www.oxidmodule.com
 */

$sLangName = "Deutsch";

// -------------------------------
// RESOURCE IDENTITFIER = STRING
// -------------------------------
$aLang = [
    //Navigation
    'charset'                                                   => 'UTF-8',

    'SHOP_MODULE_GROUP_d3dev_order'                             => 'Bestellungsablauf manipulieren',
    'SHOP_MODULE_blD3DevAvoidDelBasket'                         => 'Warenkorb wird nach Bestellabschluss nicht geleert',
    'HELP_SHOP_MODULE_blD3DevAvoidDelBasket'                    => 'Damit kann auf der Bestellbestätigungsseite '.
        '(Thankyou) durch den "zurück"-Button in den Warenkorb Schritt 4 gewechselt werden und die Bestellung erneut '.
        'abgeschickt werden. Eine erneute Bestückung des Warenkorbs ist nicht nötig. Beim erneuten Absenden wird '.
        'jeweils eine weitere Bestellung angelegt werden. Eventuell verwendete Gutscheine müssen so eingestellt '.
        'werden, dass diese mehrfach verwendet werden können.',
    'SHOP_MODULE_blD3DevShowThankyou'                           => 'Thankyou-Seite kann auch ohne Bestellung '.
        'aufgerufen werden',
    'HELP_SHOP_MODULE_blD3DevShowThankyou'                      => 'Ohne abgesendete Bestellung läßt sich die '.
        'Thankyou-Seite im Standardshop nicht aufrufen. Diese Option stellt dies für Entwicklungszwecke zur '.
        'Verfügung. <br>Den Link zum Seitenaufruf haben wir hier im Stamm-Tab hinterlegt. Die Seite öffnet sich in '.
        'einem neuen Browserfenster. <br>Für die Anzeige wird die letzte vorliegende Bestellung geladen. Über den '.
        'Parameter "d3ordernr=X" kann eine bestimmten Bestellung vorgegeben werden.',

    'SHOP_MODULE_GROUP_d3dev_mail'                              => 'Mailanzeige',
    'SHOP_MODULE_blD3DevShowOrderMailsInBrowser'                => 'Bestellbestätigungsmails können im Browser '.
        'angezeigt werden',
    'HELP_SHOP_MODULE_blD3DevShowOrderMailsInBrowser'           => 'Die Links zu den '.
        'jeweiligen Mails sind im Stamm-Tab aufgelistet.<br>Für die Anzeige wird ohne Angabe der Bestellnummer die '.
        'letzte vorliegende Bestellung geladen. Über den Parameter "d3ordernr=X" kann eine bestimmten Bestellung '.
        'vorgegeben werden.',

    'SHOP_MODULE_GROUP_d3dev_mailblock'                         => 'Mailversand',
    'SHOP_MODULE_blD3DevBlockMails'                             => 'Mails an beliebige Mailadressen werden nicht '.
        'versandt',
    'HELP_SHOP_MODULE_blD3DevBlockMails'                        => 'Der Mailversand wird komplett geblockt.',
    'SHOP_MODULE_sD3DevRedirectMail'                            => 'versendete Mails an diese Adresse umleiten',
    'HELP_SHOP_MODULE_sD3DevRedirectMail'                       => 'Wenn leer, erfolgt keine Umleitung. Ohne '.
        'zusätzliche Blockieroption werden die Mails dann an den original Empfänger gesendet.',
];
