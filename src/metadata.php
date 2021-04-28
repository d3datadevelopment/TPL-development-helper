<?php

/**
 * This Software is the property of Data Development and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * http://www.shopmodule.com
 *
 * @copyright © D³ Data Development, Thomas Dartsch
 * @author    D³ Data Development - Daniel Seifert <info@shopmodule.com>
 * @link      http://www.oxidmodule.com
 */

use D3\Devhelper\Application\Controller\d3dev;
use D3\Devhelper\Modules\Core as ModuleCore;
use D3\Devhelper\Modules\Application\Controller as ModuleController;
use D3\Devhelper\Modules\Application\Model as ModuleModel;
use OxidEsales\Eshop\Application\Controller as OxidController;
use OxidEsales\Eshop\Application\Model as OxidModel;
use OxidEsales\Eshop\Core as OxidCore;
use OxidEsales\Eshop\Core\Registry;

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';
$sLogo = '<img src="https://logos.oxidmodule.com/d3logo.svg" alt="(D3)" style="height:1em;width:1em"> ';

$shopUrl = function_exists('oxNew') ? Registry::getConfig()->getCurrentShopUrl(false) : '../';
    

/**
 * Module information
 */
$aModule = array(
    'id'           => 'd3dev',
    'title'        => [
        'de'    => $sLogo . 'TPL Entwicklerwerkzeug',
        'en'    => $sLogo . 'TPL Development Tool'
    ],
    'description'  => [
        'de'    => '<script type="text/javascript"><!--
                    function showNote() {
                        let _oElem = document.getElementById("secnote");
                        if (_oElem.style.display === "block") {
                            _oElem.style.display = "none";
                        } else {
                            _oElem.style.display = "block";
                        }
                    }
                    --></script>
                    <p style="background-color: darkred; padding: 5px;"><a href="#" style="text-decoration: underline; color: white;" onclick="showNote(); return false;"><b>Sicherheitshinweis</b></a></p>
                    <p style="display: none; background-color: darkred; color: white; padding: 5px;" id="secnote">Diese Shoperweiterung stellt Entwicklungshilfen zur Verf&uuml;gung, die im Livebetrieb sicherheitskritisch sein k&ouml;nnen. Es k&ouml;nnen Kunden- und Bestelldaten ausgelesen und auch Shopfunktionen manipuliert werden. Aktivieren Sie diese Erweiterung daher nur in einem Umfeld, in dem Sie Missbrauch ausschlie&szligen k&ouml;nnen. F&uuml;r entstandene Sch&auml;den lehnen wir jede Haftung ab.</p>
                    <ul><li>unterbindet L&ouml;schen des Warenkorbs nach Bestellabschluss</li>'.
            '<li><a style="text-decoration: underline;" href="'.$shopUrl.'index.php?cl=thankyou&d3dev=1&d3ordernr=" target="_new">Thankyou-Seite ist ohne Bestellung aufrufbar*</a></li>'.
            '<li>Mail-Templates k&ouml;nnen im Browser ausgegeben werden'.
                '<ul>'.
                '<li><a style="text-decoration: underline;" href="'.$shopUrl.'index.php?cl=d3dev&fnc=showOrderMailContent&type=owner_html&d3ordernr=" target="_new">Order Owner HTML*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.$shopUrl.'index.php?cl=d3dev&fnc=showOrderMailContent&type=owner_plain&d3ordernr=" target="_new">Order Owner Plain*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.$shopUrl.'index.php?cl=d3dev&fnc=showOrderMailContent&type=user_html&d3ordernr=" target="_new">Order User HTML*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.$shopUrl.'index.php?cl=d3dev&fnc=showOrderMailContent&type=user_plain&d3ordernr=" target="_new">Order User Plain*</a></li>'.
            '</li>'.
            '<li>blockiert &uuml;bers Framework versendete Mails oder leitet diese um</li>'.
            '</ul><br>Jede dieser Optionen muss aus Sicherheitsgr&uuml;nden unter "Einstell." aktiviert werden. Weiterhin darf der Shop nicht im Produktivmodus betrieben werden.<br><br>'.
            '* Ordernummer an URL erg&auml;nzen, wenn bestimmte Bestellungen angezeigt werden sollen',
        'en'    => ''],
    'version'      => '3.0.0.1',
    'author'       => 'D&sup3; Data Development (Inh.: Thomas Dartsch)',
    'email'        => 'support@shopmodule.com',
    'url'          => 'http://www.oxidmodule.com/',
    'extend'      => [
        OxidController\ThankYouController::class    => ModuleController\d3_dev_thankyou::class,
        OxidModel\Order::class                      => ModuleModel\d3_dev_oxorder::class,
        OxidModel\OrderArticle::class               => ModuleModel\d3_dev_oxorderarticle::class,
        OxidCore\Email::class                       => ModuleCore\d3_dev_oxemail::class,
        OxidModel\Basket::class                     => ModuleModel\d3_dev_oxbasket::class,
        OxidModel\BasketItem::class                 => ModuleModel\d3_dev_oxbasketitem::class,
    ],
    'controllers'       => [
        'd3dev'     => d3dev::class,
    ],
    'templates'   => [],
    'events'      => [],
    'blocks'      => [],
    'settings'    => [
        [
            'group' => 'd3dev_order',
            'name' => ModuleCore\d3_dev_conf::OPTION_PREVENTDELBASKET,
            'type' => 'bool',
            'value' => 'false'
        ],
        [
            'group' => 'd3dev_order',
            'name' => ModuleCore\d3_dev_conf::OPTION_SHOWTHANKYOU,
            'type' => 'bool',
            'value' => 'false'
        ],
        [
            'group' => 'd3dev_mail',
            'name' => ModuleCore\d3_dev_conf::OPTION_SHOWMAILSINBROWSER,
            'type' => 'bool',
            'value' => 'false'
        ],
        [
            'group' => 'd3dev_mailblock',
            'name' => ModuleCore\d3_dev_conf::OPTION_BLOCKMAIL,
            'type' => 'bool',
            'value' => 'false'
        ],
        [
            'group' => 'd3dev_mailblock',
            'name' => ModuleCore\d3_dev_conf::OPTION_REDIRECTMAIL,
            'type' => 'str',
            'value' => 'd3test1@shopmodule.com'
        ],
    ],
);
