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

namespace D3\Devhelper\Modules\Application\Model;

use D3\Devhelper\Modules\Core\d3_dev_conf;
use oxArticleInputException;
use OxidEsales\Eshop\Application\Model\BasketItem;
use OxidEsales\Eshop\Application\Model\OrderArticle;
use OxidEsales\Eshop\Core\Registry;
use oxNoArticleException;

class d3_dev_oxbasket extends d3_dev_oxbasket_parent
{
    public function deleteBasket()
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()
             || ! Registry::getConfig()->getConfigParam(d3_dev_conf::OPTION_PREVENTDELBASKET)
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

    /**
     * @param OrderArticle $oOrderArticle
     *
     * @return d3_dev_oxbasketitem|null
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    public function d3addOrderArticleToBasket($oOrderArticle)
    {
        // adding only if amount > 0
        if ($oOrderArticle->getFieldData('oxamount') > 0) {
            $this->_isForOrderRecalculation = true;
            $sItemId = $oOrderArticle->getId();

            //inserting new
            /** @var d3_dev_oxbasketitem $oBasketItem */
            $oBasketItem = oxNew(BasketItem::class);
            $oBasketItem->initFromOrderArticle($oOrderArticle);
            $oBasketItem->d3ConvertToArticleObject();
            $oBasketItem->setWrapping($oOrderArticle->getFieldData('oxwrapid'));
            $oBasketItem->setBundle($oOrderArticle->isBundle());

            $this->_aBasketContents[$sItemId] = $oBasketItem;

            //calling update method
            $this->onUpdate();

            return $this->_aBasketContents[$sItemId];
        }

        return null;
    }
}
