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

namespace D3\Devhelper\Modules\Application\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use oxarticleinputexception;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Voucher;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use oxnoarticleexception;

class d3_dev_oxorder extends d3_dev_oxorder_parent
{
    /**
     * @return d3_dev_oxbasket
     */
    public function d3DevGetOrderBasket()
    {
        /** @var d3_dev_oxbasket $oBasket */
        $oBasket = $this->getOrderBasket();

        // add this order articles to basket and recalculate basket
        $this->_d3AddOrderArticlesToBasket($oBasket, $this->getOrderArticles());

        // recalculating basket
        $oBasket->calculateBasket(true);
        $oBasket->d3ClearBasketItemArticles();

        $this->_oPayment = $this->setPayment($oBasket->getPaymentId());

        return $oBasket;
    }

    /**
     * @return string
     * @throws DatabaseConnectionException
     */
    public function d3getLastOrderId()
    {
        /** @var QueryBuilder $qb */
        $qb = ContainerFactory::getInstance()->getContainer()->get(QueryBuilderFactoryInterface::class)->create();
        $qb->select('oxid')
            ->from((oxNew(Order::class))->getViewName())
            ->where(
                $qb->expr()->and(
                    $qb->expr()->eq(
                        'oxuserid',
                        $qb->createNamedParameter('')
                    ),
                    $qb->expr()->eq(
                        'oxshopid',
                        $qb->createNamedParameter(Registry::getConfig()->getShopId())
                    )
                )
            )
            ->orderBy('oxorderdate', 'DESC')
            ->setMaxResults(1);

        $orderNr = (int) Registry::getRequest()->getRequestEscapedParameter('d3ordernr');
        if ($orderNr) {
            $qb->andWhere(
                $qb->expr()->eq(
                    'oxordernr',
                    $orderNr
                )
            );
        }

        return $qb->execute()->fetchOne();
    }

    /**
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function d3getLastOrder()
    {
        $this->load($this->d3getLastOrderId());
        $this->_d3AddVouchers();
    }

    /**
     * @return d3_dev_oxbasket|Basket
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
        /** @var QueryBuilder $qb */
        $qb = ContainerFactory::getInstance()->getContainer()->get(QueryBuilderFactoryInterface::class)->create();
        $qb->select('oxid')
            ->from((oxNew(Voucher::class))->getViewName())
            ->where(
                 $qb->expr()->eq(
                     'oxorderid',
                     $qb->createNamedParameter($this->getId())
                 )
            );
        $aResult = $qb->execute()->fetchAllAssociative();

        foreach ($aResult as $aFields) {
            $oVoucher = oxNew(Voucher::class);
            $oVoucher->load($aFields['oxid']);
            $this->_aVoucherList[$oVoucher->getId()] = $oVoucher;
        }
    }

    /**
     * Adds order articles back to virtual basket. Needed for recalculating order.
     *
     * @param d3_dev_oxbasket $oBasket        basket object
     * @param ListModel                                      $aOrderArticles order articles
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    protected function _d3AddOrderArticlesToBasket($oBasket, $aOrderArticles)
    {
        // if no order articles, return empty basket
        if (count($aOrderArticles) > 0) {
            //adding order articles to basket
            foreach ($aOrderArticles as $oOrderArticle) {
                $oBasket->d3addOrderArticleToBasket($oOrderArticle);
            }
        }
    }
}
