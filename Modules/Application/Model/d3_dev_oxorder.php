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

use D3\Devhelper\Application\Model\Exception\NoOrderFoundException;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use oxarticleinputexception;
use OxidEsales\Eshop\Application\Model\Basket;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\Voucher;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Model\ListModel;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use oxnoarticleexception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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
     * @return false|string
     * @throws ContainerExceptionInterface
     * @throws DBALException
     * @throws DBALDriverException
     * @throws NotFoundExceptionInterface
     */
    public function d3getLastOrderId(): false|string
    {
        /** @var QueryBuilder $qb */
        $qb = ContainerFactory::getInstance()->getContainer()->get(QueryBuilderFactoryInterface::class)->create();
        $qb->select('oxid')
            ->from((oxNew(Order::class))->getViewName())
            ->where(
                $qb->expr()->and(
                    $qb->expr()->neq(
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
     * @throws ContainerExceptionInterface
     * @throws DBALDriverException
     * @throws DBALException
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws NoOrderFoundException
     * @throws NotFoundExceptionInterface
     */
    public function d3getLastOrder(): void
    {
        if ($orderId = $this->d3getLastOrderId()) {
            $this->load($orderId);
            $this->_d3AddVouchers();
        } else {
            throw new NoOrderFoundException();
        }
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
