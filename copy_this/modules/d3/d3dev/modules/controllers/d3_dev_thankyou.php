<?php

// .../?cl=thankyou[&d3orderid=23]

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

class d3_dev_thankyou extends d3_dev_thankyou_parent
{
    public function init()
    {
        $sSessChallenge = oxRegistry::getSession()->getVariable('sess_challenge');

        parent::init();

        oxRegistry::getSession()->setVariable('sess_challenge', $sSessChallenge);

        if (oxRegistry::getConfig()->getRequestParameter('d3dev')
            && false == (bool) oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value
            && oxRegistry::getConfig()->getConfigParam('blD3DevShowThankyou')
        ) {
            $oOrder = $this->d3GetLastOrder();
            $oBasket = $oOrder->d3DevGetOrderBasket();
            $this->_oBasket = $oBasket;
        }
    }

    /**
     * @return d3_dev_oxorder
     */
    public function d3GetLastOrder()
    {
        if (oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value) {
            return false;
        }

        /** @var d3_dev_oxorder $oOrder */
        $oOrder = oxNew('oxorder');
        $oOrder->d3getLastOrder();

        return $oOrder;
    }
}
