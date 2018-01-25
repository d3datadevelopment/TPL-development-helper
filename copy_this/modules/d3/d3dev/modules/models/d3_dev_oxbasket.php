<?php

use OxidEsales\Eshop\Core\Registry;

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

class d3_dev_oxbasket extends d3_dev_oxbasket_parent
{
    public function deleteBasket()
    {
        if (Registry::getConfig()->getActiveShop()->oxshops__oxproductive->value
            || false == Registry::getConfig()->getConfigParam('blD3DevAvoidDelBasket')
        ) {
            parent::deleteBasket();
        }

        // else do nothing;
    }

    public function d3ClearBasketItemArticles()
    {
        /** @var d3_dev_oxbasketitem $oBasketItem */
        foreach ($this->_aBasketContents as $oBasketItem) {
            $oBasketItem->d3ClearArticle();
        }
    }

    /**
     * Calculates total basket discount value.
     */
    protected function _calcBasketTotalDiscount()
    {
        if ($this->_oTotalDiscount === null || (!$this->isAdmin())) {
            $this->_oTotalDiscount = $this->_getPriceObject();

            if (is_array($this->_aDiscounts)) {
                foreach ($this->_aDiscounts as $oDiscount) {
                    // skipping bundle discounts
                    if ($oDiscount->sType == 'itm') {
                        continue;
                    }

                    // add discount value to total basket discount
                    $this->_oTotalDiscount->add($oDiscount->dDiscount);
                }
            }
        }
    }
}
