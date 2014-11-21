<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

$sStyle = class_exists('d3dev') ? "background-color: darkred; color: white; padding: 0 10px;" : "";

/**
 * Module information
 */
$aModule = array(
    'id'           => 'd3dev',
    'title'        =>
        (class_exists('d3utils') ? d3utils::getInstance()->getD3Logo() : 'D&sup3;') .
        ' <span style="'.$sStyle.'">TPL Development Tool</span>',
    'description'  => array(
        'de'    => '<script type="text/javascript"><!--
                    function showNote() {
                        var _oElem = document.getElementById("secnote");
                        if (_oElem.style.display == "block") {
                            _oElem.style.display = "none";
                        } else {
                            _oElem.style.display = "block";
                        }
                    }
                    --></script>
                    <p style="background-color: darkred; padding: 5px;"><a href="#" style="text-decoration: underline; color: white;" onclick="showNote(); return false;"><b>Sicherheitshinweis</b></a></p>
                    <p style="display: none; background-color: darkred; color: white; padding: 5px;" id="secnote">Diese Shoperweiterung stellt Entwicklungshilfen zur Verf&uuml;gung, die im Livebetrieb sicherheitskritisch sein k&ouml;nnen. Es k&ouml;nnen Kunden- und Bestelldaten ausgelesen und auch Shopfunktionen manipuliert werden. Aktivieren Sie diese Erweiterung daher nur in einem Umfeld, in dem Sie Missbrauch ausschlie&szligen k&ouml;nnen. F&uuml;r entstandene Sch&auml;den lehnen wir jede Haftung ab.</p>
                    <ul><li>unterbindet L&ouml;schen des WKs nach Bestellabschluss</li>'.
            '<li><a style="text-decoration: underline;" href="'.oxRegistry::getConfig()->getCurrentShopUrl(false).'index.php?cl=thankyou&d3dev=1&d3ordernr=1" target="_new">Thankyou ist ohne Bestellung aufrufbar</a></li>'.
            '<li>Mail-Templates k&ouml;nnen im Browser ausgegeben werden'.
                '<ul>'.
                '<li><a style="text-decoration: underline;" href="'.oxRegistry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showOrderMailContent&type=owner_html&d3ordernr=1" target="_new">Owner HTML</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.oxRegistry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showOrderMailContent&type=owner_plain&d3ordernr=1" target="_new">Owner Plain</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.oxRegistry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showOrderMailContent&type=user_html&d3ordernr=1" target="_new">User HTML</a></li>'.
                '<li><a style="text-decoration: underline;" href="'.oxRegistry::getConfig()->getCurrentShopUrl(false).'index.php?cl=d3dev&fnc=showOrderMailContent&type=user_plain&d3ordernr=1" target="_new">User Plain</a></li></ul>'.
            '</li></ul>Jede dieser Optionen muss aus Sicherheitsgr&uuml;nden unter "Einstell." aktiviert werden. Weiterhin darf der Shop nicht im Produktivmodus betrieben werden.',
        'en'    => ''),
    // 'thumbnail'    => 'picture.png',
    'version'      => '0.1',
    'author'       => 'D&sup3; Data Development (Inh.: Thomas Dartsch)',
    'email'        => 'support@shopmodule.com',
    'url'          => 'http://www.oxidmodule.com/',
    'extend'      => array(
        'thankyou'  => 'd3/d3dev/modules/controllers/d3_dev_thankyou',
        'oxorder'   => 'd3/d3dev/modules/models/d3_dev_oxorder',
        'oxorderarticle'   => 'd3/d3dev/modules/models/d3_dev_oxorderarticle',
        'oxemail'   => 'd3/d3dev/modules/models/d3_dev_oxemail',
        'oxbasket'  => 'd3/d3dev/modules/models/d3_dev_oxbasket',
        'oxbasketitem'  => 'd3/d3dev/modules/models/d3_dev_oxbasketitem',
    ),
    'files'       => array(
        'd3dev'     => 'd3/d3dev/controllers/d3dev.php',
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
    ),
);
