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

namespace D3\Devhelper\Modules\Application\Controller;

// .../?cl=thankyou[&d3orderid=23]
use D3\Devhelper\Application\Model\Exception\NoOrderFoundException;
use D3\Devhelper\Application\Model\Exception\UnauthorisedException;
use D3\Devhelper\Modules\Application\Model\d3_dev_oxorder;
use D3\Devhelper\Modules\Core\d3_dev_conf;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Exception;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Registry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class d3_dev_thankyou extends d3_dev_thankyou_parent
{
    /**
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function init()
    {
        $sSessChallenge = Registry::getSession()->getVariable('sess_challenge');

        parent::init();

        if (Registry::getRequest()->getRequestEscapedParameter("d3dev")
            && !Registry::getConfig()->getActiveShop()->isProductiveMode()
            && Registry::getConfig()->getConfigParam(d3_dev_conf::OPTION_PREVENTDELBASKET)
        ) {
            Registry::getSession()->setVariable('sess_challenge', $sSessChallenge);
        }

        if ($this->d3DevCanShowThankyou()) {
            $this->_d3authenticate();
            $oOrder = $this->d3GetLastOrder();
            $this->_oBasket = $oOrder->d3DevGetOrderBasket();
        }
    }

    /**
     * @return bool
     */
    public function d3DevCanShowThankyou()
    {
        return Registry::getRequest()->getRequestEscapedParameter("d3dev") &&
               !Registry::getConfig()->getActiveShop()->isProductiveMode() &&
               Registry::getConfig()->getConfigParam(d3_dev_conf::OPTION_SHOWTHANKYOU);
    }

    /**
     * @return string
     */
    public function render()
    {
        $currentClass = '';
        if ($this->d3DevCanShowThankyou()) {
            $currentClass = $this->getViewConfig()->getViewConfigParam('cl');
        }

        $ret = parent::render();

        if ($this->d3DevCanShowThankyou()) {
            $this->getViewConfig()->setViewConfigParam('cl', $currentClass);
        }

        return $ret;
    }

    protected function _d3authenticate()
    {
        try {
            $sUser = Registry::getRequest()->getRequestEscapedParameter('usr');
            $sPassword = Registry::getRequest()->getRequestEscapedParameter('pwd');

            if (!$sUser || !$sPassword) {
                $sUser = $_SERVER[ 'PHP_AUTH_USER' ];
                $sPassword = $_SERVER[ 'PHP_AUTH_PW' ];
            }

            if (!$sUser || !$sPassword) {
                $sHttpAuthorization = $_REQUEST[ 'HTTP_AUTHORIZATION' ];
                if ($sHttpAuthorization) {
                    $sUser = null;
                    $sPassword = null;
                    $aHttpAuthorization = explode(' ', $sHttpAuthorization);
                    if (is_array($aHttpAuthorization) && count($aHttpAuthorization) >= 2 && strtolower($aHttpAuthorization[ 0 ]) == 'basic') {
                        $sBasicAuthorization = base64_decode($aHttpAuthorization[ 1 ]);
                        $aBasicAuthorization = explode(':', $sBasicAuthorization);
                        if (is_array($aBasicAuthorization) && count($aBasicAuthorization) >= 2) {
                            $sUser = $aBasicAuthorization[ 0 ];
                            $sPassword = $aBasicAuthorization[ 1 ];
                        }
                    }
                }
            }
            /** @var User $oUser */
            $oUser = oxNew(User::class);
            if (!$sUser || !$sPassword || !$oUser->login($sUser, $sPassword)) {
                /** @var UserException $oEx */
                $oEx = oxNew(UserException::class, 'EXCEPTION_USER_NOVALIDLOGIN');
                throw $oEx;
            }
        } catch (Exception $oEx) {
            $oShop = Registry::getConfig()->getActiveShop();
            header('WWW-Authenticate: Basic realm="{' . $oShop->getFieldData('oxname') . '"');
            header('HTTP/1.0 401 Unauthorized');
            exit(1);
        }
    }

    /**
     * @return bool|d3_dev_oxorder|Order
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function getOrder()
    {
        $oOrder = parent::getOrder();

        if ((false == $oOrder || !$oOrder->getFieldData('oxordernr'))
            && $this->d3DevCanShowThankyou()
        ) {
            try {
                $this->_oOrder = $this->d3GetLastOrder();
                $oOrder = $this->_oOrder;

                if (!$oOrder || !$oOrder->getFieldData('oxordernr')) {
                    throw oxNew(\RuntimeException::class, 'unknown order');
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        return $oOrder;
    }

    /**
     * @return d3_dev_oxorder
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     * @throws NoOrderFoundException
     * @throws DBALDriverException
     * @throws DBALException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function d3GetLastOrder(): d3_dev_oxorder
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()) {
            throw oxNew(UnauthorisedException::class);
        }

        /** @var d3_dev_oxorder $oOrder */
        $oOrder = oxNew(Order::class);
        $oOrder->d3getLastOrder();

        return $oOrder;
    }
}
