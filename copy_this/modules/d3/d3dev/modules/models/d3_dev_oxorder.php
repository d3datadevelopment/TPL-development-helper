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

class d3_dev_oxorder extends d3_dev_oxorder_parent
{
    /**
     * @return d3_dev_oxbasket
     */
    public function d3DevGetOrderBasket()
    {
        /** @var d3_dev_oxbasket $oBasket */
        $oBasket = $this->_getOrderBasket();

        // unsetting bundles
        $oOrderArticles = $this->getOrderArticles();
        foreach ($oOrderArticles as $sItemId => $oItem) {
            if ($oItem->isBundle()) {
                $oOrderArticles->offsetUnset($sItemId);
            }
        }

        // add this order articles to basket and recalculate basket
        $this->_addOrderArticlesToBasket($oBasket, $oOrderArticles);
        // recalculating basket
        $oBasket->calculateBasket(true);
        $oBasket->d3ClearBasketItemArticles();

        $this->_oPayment = $this->_setPayment($oBasket->getPaymentId());

        return $oBasket;
    }

    /**
     * @return string
     */
    public function d3getLastOrderId()
    {
        if (oxRegistry::getConfig()->getRequestParameter('d3ordernr')) {
            $sWhere = ' oxordernr = ' . (int) oxRegistry::getConfig()->getRequestParameter('d3ordernr');
        } else {
            $sWhere = 1;
        }

        $sSelect = "SELECT oxid FROM ".getViewName('oxorder')." WHERE ".$sWhere." ORDER BY oxorderdate DESC LIMIT 1";

        return oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne($sSelect);
    }

    public function d3getLastOrder()
    {
        $this->load($this->d3getLastOrderId());
        $this->_d3AddVouchers();
    }

    /**
     * @return oxBasket
     */
    public function getBasket()
    {
        $oBasket = parent::getBasket();

        if (false == $oBasket && oxRegistry::getConfig()->getActiveView()->getClassName() == 'd3dev') {
            $oBasket = $this->d3DevGetOrderBasket();
        }

        return $oBasket;
    }
    
    protected function _d3AddVouchers()
    {
        $sSelect = "SELECT oxid FROM oxvouchers WHERE oxorderid = ".oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->quote($this->getId()).";";
        
        $aResult = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getArray($sSelect);

        foreach ($aResult as $aFields) {
            $oVoucher = oxNew('oxvoucher');
            $oVoucher->load($aFields['oxid']);
            $this->_aVoucherList[$oVoucher->getId()] = $oVoucher;
        }
    }
}
