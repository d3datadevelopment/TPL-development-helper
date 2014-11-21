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
 * @author    D³ Data Development - Daniel Seifert <ds@shopmodule.com>
 * @link      http://www.oxidmodule.com
 */

class d3dev extends oxUBase
{
    public function showOrderMailContent()
    {
        if (oxRegistry::getConfig()->getActiveShop()->isProductiveMode()
            || false == oxRegistry::getConfig()->getConfigParam('blD3DevShowOrderMailsInBrowser')
        ) {
            oxRegistry::getUtils()->redirect(oxRegistry::getConfig()->getShopUrl().'index.php?cl=start');
        }

        $sTpl = oxRegistry::getConfig()->getRequestParameter('type');

        /** @var d3_dev_thankyou $oThankyou */
        $oThankyou = oxNew('thankyou');
        $oOrder = $oThankyou->d3GetLastOrder();

        /** @var d3_dev_oxemail $oEmail */
        $oEmail = oxNew('oxemail');
        echo $oEmail->d3GetOrderMailContent($oOrder, $sTpl);
        die();
    }
}
