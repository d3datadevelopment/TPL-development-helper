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

class d3_dev_oxbasket extends d3_dev_oxbasket_parent
{
    public function deleteBasket()
    {
        if (oxRegistry::getConfig()->getActiveShop()->isProductiveMode()
            || false == oxRegistry::getConfig()->getConfigParam('blD3DevAvoidDeleteBasketInThankyou')
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
}
