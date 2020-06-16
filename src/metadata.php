<?php

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
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'd3dev',
    'title'        =>
        '<svg style="height:1em;width:1em"><image xlink:href="https://logos.oxidmodule.com/d3logo.svg" style="height:1em;width:1em" /></svg> '.
        'TPL Development Tool',
    'description'  => array(
        'de'    => '<script type="text/javascript"><!--
                    function showNote() {
                        var _oElem = document.getElementById("secnote");
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
            '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=thankyou&d3dev=1&d3ordernr=" target="_new">Thankyou-Seite ist ohne Bestellung aufrufbar*</a></li>'.
            '<li>Mail-Templates k&ouml;nnen im Browser ausgegeben werden'.
                '<ul>'.
                '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showOrderMailContent&type=owner_html&d3ordernr=" target="_new">Order Owner HTML*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showOrderMailContent&type=owner_plain&d3ordernr=" target="_new">Order Owner Plain*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showOrderMailContent&type=user_html&d3ordernr=" target="_new">Order User HTML*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showOrderMailContent&type=user_plain&d3ordernr=" target="_new">Order User Plain*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showInquiryMailContent&type=owner_html&d3inquirynr=" target="_new">Inquiry Owner HTML*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showInquiryMailContent&type=owner_plain&d3inquirynr=" target="_new">Inquiry Owner Plain*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showInquiryMailContent&type=user_html&d3inquirynr=" target="_new">Inquiry User HTML*</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.Registry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showInquiryMailContent&type=user_plain&d3inquirynr=" target="_new">Inquiry User Plain*</a></li></ul>'.
            '</li>'.
            '<li>blockiert &uuml;bers Framework versendete Mails oder leitet diese um</li>'.
            '</ul><br>Jede dieser Optionen muss aus Sicherheitsgr&uuml;nden unter "Einstell." aktiviert werden. Weiterhin darf der Shop nicht im Produktivmodus betrieben werden.<br><br>'.
            '* Ordernummer an URL erg&auml;nzen, wenn bestimmte Bestellungen angezeigt werden sollen',
        'en'    => ''),
    'version'      => '2.0.1.0',
    'author'       => 'D&sup3; Data Development (Inh.: Thomas Dartsch)',
    'email'        => 'support@shopmodule.com',
    'url'          => 'http://www.oxidmodule.com/',
    'extend'      => array(
        OxidController\ThankYouController::class    => ModuleController\d3_dev_thankyou::class,
        OxidModel\Order::class                      => ModuleModel\d3_dev_oxorder::class,
        OxidModel\OrderArticle::class               => ModuleModel\d3_dev_oxorderarticle::class,
        OxidCore\Email::class                       => ModuleCore\d3_dev_oxemail::class,
        OxidModel\Basket::class                     => ModuleModel\d3_dev_oxbasket::class,
        OxidModel\BasketItem::class                 => ModuleModel\d3_dev_oxbasketitem::class,
    ),
    'controllers'       => array(
        'd3dev'     => \D3\Devhelper\Application\Controller\d3dev::class,
    ),
    'templates'   => array(
    ),
    'events'      => array(
    ),
    'blocks'      => array(
    ),
    'settings'    => array(
        array(
            'group' => 'd3dev_order',
            'name' => 'blD3DevAvoidDelBasket',
            'type' => 'bool',
            'value' => 'false'
        ),
        array(
            'group' => 'd3dev_order',
            'name' => 'blD3DevShowThankyou',
            'type' => 'bool',
            'value' => 'false'
        ),
        array(
            'group' => 'd3dev_mail',
            'name' => 'blD3DevShowOrderMailsInBrowser',
            'type' => 'bool',
            'value' => 'false'
        ),
        array(
            'group' => 'd3dev_mailblock',
            'name' => 'blD3DevBlockMails',
            'type' => 'bool',
            'value' => 'false'
        ),
        array(
            'group' => 'd3dev_mailblock',
            'name' => 'sD3DevRedirectMail',
            'type' => 'str',
            'value' => 'd3test1@shopmodule.com'
        ),
    ),
);


if (class_exists('d3inquiry')) {
    $aModule['extend']['d3inquiry']  = ModuleModel\d3_dev_d3inquiry::class;
    $aModule['extend']['d3inquiryarticle']  = ModuleModel\d3_dev_d3inquiryarticle::class;
}
