<?php

namespace D3\Devhelper\Modules\Application\Model;

use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Request;

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

class d3_dev_d3inquiry extends d3_dev_d3inquiry_parent
{
    protected $_oOrderBasket = null;

    /**
     * @return d3_dev_oxbasket
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function d3DevGetOrderBasket()
    {
        /** @var Basket $oBasket */
        $this->_getInquiryBasket();

        // unsetting bundles
        $oOrderArticles = $this->getInquiryArticles();
        foreach ($oOrderArticles as $sItemId => $oItem) {
            if ($oItem->isBundle()) {
                $oOrderArticles->offsetUnset($sItemId);
            }
        }

        // add this order articles to basket and recalculate basket
        $oBasket = $this->_addInquiryArticlesToBasket($this->getInquiryUser(), $oOrderArticles);
        // recalculating basket
        $oBasket->calculateBasket(true);
        $oBasket->d3ClearBasketItemArticles();

        return $oBasket;
    }

    /**
     * @return string
     * @throws DatabaseConnectionException
     */
    public function d3getLastInquiryId()
    {
        $inquiryNr = (int) Registry::get(Request::class)->getRequestEscapedParameter('d3inquirynr');
        $sWhere = 1;
        if ($inquiryNr) {
            $sWhere = ' oxinquirynr = ' .  $inquiryNr;
        }

        $sSelect = "SELECT oxid FROM ".getViewName('d3inquiry')." WHERE ".$sWhere." ORDER BY oxinquirydate DESC LIMIT 1";

        return DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getOne($sSelect);
    }

    /**
     * @throws DatabaseConnectionException
     */
    public function d3getLastInquiry()
    {
        $this->load($this->d3getLastInquiryId());
        //$this->_d3AddVouchers();
    }

    /**
     * @return Basket
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function getBasket()
    {
        $oBasket = parent::getBasket();

        if (false == $oBasket && Registry::getConfig()->getActiveView()->getClassKey() == 'd3dev') {
            $oBasket = $this->d3DevGetOrderBasket();
        }

        return $oBasket;
    }

    /**
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    protected function _d3AddVouchers()
    {
        $sSelect = "SELECT oxid FROM oxvouchers WHERE oxorderid = ". DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->quote($this->getId()).";";

        $aResult = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC)->getAll($sSelect);

        foreach ($aResult as $aFields) {
            $oVoucher = oxNew('oxvoucher');
            $oVoucher->load($aFields['oxid']);
            $this->_aVoucherList[$oVoucher->getId()] = $oVoucher;
        }
    }

    /**
     * Returns basket object filled up with discount, delivery, wrapping and all other info
     *
     * @param bool $blStockCheck perform stock check or not (default true)
     *
     * @return Basket
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    protected function _getInquiryBasket($blStockCheck = true)
    {
        /** @var Basket _oOrderBasket */
        $this->_oOrderBasket = oxNew(Basket::class);
        $this->_oOrderBasket->enableSaveToDataBase(false);

        //setting recalculation mode
        $this->_oOrderBasket->setCalculationModeNetto($this->isNettoMode());

        // setting stock check mode
        $this->_oOrderBasket->setStockCheckMode($blStockCheck);

        // setting virtual basket user
        $this->_oOrderBasket->setBasketUser($this->getInquiryUser());

        // transferring order id
        $this->_oOrderBasket->setInquiryId($this->getId());

        // setting basket currency order uses
        $aCurrencies = Registry::getConfig()->getCurrencyArray();
        foreach ($aCurrencies as $oCur) {
            if ($oCur->name == $this->getFieldData('oxcurrency')) {
                $oBasketCur = $oCur;
                break;
            }
        }

        // setting currency
        $this->_oOrderBasket->setBasketCurrency($oBasketCur);

        // set basket card id and message
        $this->_oOrderBasket->setCardId($this->getFieldData('oxcardid'));
        $this->_oOrderBasket->setCardMessage($this->getFieldData('oxcardtext'));

        if ($this->_blReloadDiscount) {
            $oDb = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
            // disabling availability check
            $this->_oOrderBasket->setSkipVouchersChecking(true);

            // add previously used vouchers
            $sQ = 'select oxid from oxvouchers where oxorderid = ' . $oDb->quote($this->getId());
            $aVouchers = $oDb->getAll($sQ);
            foreach ($aVouchers as $aVoucher) {
                $this->_oOrderBasket->addVoucher($aVoucher['oxid']);
            }
        } else {
            $this->_oOrderBasket->setDiscountCalcMode(false);
            $this->_oOrderBasket->setVoucherDiscount($this->getFieldData('oxvoucherdiscount'));
            $this->_oOrderBasket->setTotalDiscount($this->getFieldData('oxdiscount'));
        }

        return $this->_oOrderBasket;
    }
}
