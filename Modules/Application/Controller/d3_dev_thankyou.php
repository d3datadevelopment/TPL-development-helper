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
use GuzzleHttp\Psr7\ServerRequest;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
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

        $container = ContainerFactory::getInstance()->getContainer();

        /** @var ModuleSettingServiceInterface $moduleSettings */
        $moduleSettings = $container->get(ModuleSettingServiceInterface::class);

        if (Registry::getRequest()->getRequestEscapedParameter("d3dev")
            && !Registry::getConfig()->getActiveShop()->isProductiveMode()
            && $moduleSettings->getBoolean(d3_dev_conf::OPTION_PREVENTDELBASKET, 'd3dev')
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
        $container = ContainerFactory::getInstance()->getContainer();

        /** @var ModuleSettingServiceInterface $moduleSettings */
        $moduleSettings = $container->get(ModuleSettingServiceInterface::class);

        return Registry::getRequest()->getRequestEscapedParameter("d3dev") &&
               !Registry::getConfig()->getActiveShop()->isProductiveMode() &&
               $moduleSettings->getBoolean(d3_dev_conf::OPTION_SHOWTHANKYOU, 'd3dev');
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
            $request = ServerRequest::fromGlobals();
            $serverParams = $request->getServerParams();
            $sUser = $serverParams['PHP_AUTH_USER'] ?? null;
            $sPassword = $serverParams['PHP_AUTH_PW'] ?? null;

            if (!$sUser || !$sPassword) {
                $authorization = $request->getHeaderLine('Authorization')
                    ?: ($serverParams['HTTP_AUTHORIZATION'] ?? $serverParams['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
                if (preg_match('/^Basic\s+([^\s]+)$/i', trim($authorization), $matches)) {
                    $credentials = base64_decode($matches[1], true);
                    if ($credentials !== false && str_contains($credentials, ':')) {
                        [$sUser, $sPassword] = explode(':', $credentials, 2);
                    }
                }
            }
            /** @var User $oUser */
            $oUser = oxNew(User::class);
            if (!$sUser || !$sPassword || !$oUser->login($sUser, $sPassword) || !$oUser->isMallAdmin()) {
                /** @var UserException $oEx */
                $oEx = oxNew(UserException::class, 'EXCEPTION_USER_NOVALIDLOGIN');
                throw $oEx;
            }
        } catch (Exception $oEx) {
            $realm = (string) Registry::getConfig()->getActiveShop()->getFieldData('oxname');
            $realm = addcslashes(preg_replace('/[\x00-\x1F\x7F]/', '', $realm), "\\\"");
            header('WWW-Authenticate: Basic realm="' . $realm . '"');
            http_response_code(401);
            exit;
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

        if ((!$oOrder || !$oOrder->getFieldData('oxordernr'))
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
