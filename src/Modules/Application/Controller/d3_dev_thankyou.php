<?php

namespace D3\Devhelper\Modules\Application\Controller;

// .../?cl=thankyou[&d3orderid=23]
use D3\Devhelper\Modules\Application\Model\d3_dev_d3inquiry;
use D3\Devhelper\Modules\Application\Model\d3_dev_oxorder;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseErrorException;
use OxidEsales\Eshop\Core\Exception\UserException;
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

        if (Registry::get(Request::class)->getRequestEscapedParameter("d3dev")
            && false == (bool) Registry::getConfig()->getActiveShop()->isProductiveMode()
            && Registry::getConfig()->getConfigParam('blD3DevAvoidDelBasket')
        ) {
            Registry::getSession()->setVariable( 'sess_challenge', $sSessChallenge );
        }

        if (Registry::get(Request::class)->getRequestEscapedParameter("d3dev")
            && false == (bool) Registry::getConfig()->getActiveShop()->isProductiveMode()
            && Registry::getConfig()->getConfigParam('blD3DevShowThankyou')
        ) {
            $this->_d3authenticate();
            $oOrder = $this->d3GetLastOrder();
            $oBasket = $oOrder->d3DevGetOrderBasket();
            $this->_oBasket = $oBasket;
        }
    }

    protected function _d3authenticate ()
    {
        try {
            $sUser = Registry::get(Request::class)->getRequestEscapedParameter( 'usr');
            $sPassword = Registry::get(Request::class)->getRequestEscapedParameter('pwd');

            if ( !$sUser || !$sPassword ) {
                $sUser = $_SERVER[ 'PHP_AUTH_USER' ];
                $sPassword = $_SERVER[ 'PHP_AUTH_PW' ];
            }

            if ( !$sUser || !$sPassword ) {
                $sHttpAuthorization = $_REQUEST[ 'HTTP_AUTHORIZATION' ];
                if ( $sHttpAuthorization ) {
                    $sUser = null;
                    $sPassword = null;
                    $aHttpAuthorization = explode( ' ', $sHttpAuthorization );
                    if ( is_array( $aHttpAuthorization ) && count( $aHttpAuthorization ) >= 2 && strtolower( $aHttpAuthorization[ 0 ] ) == 'basic' ) {
                        $sBasicAuthorization = base64_decode( $aHttpAuthorization[ 1 ] );
                        $aBasicAuthorization = explode( ':', $sBasicAuthorization );
                        if ( is_array( $aBasicAuthorization ) && count( $aBasicAuthorization ) >= 2 ) {
                            $sUser = $aBasicAuthorization[ 0 ];
                            $sPassword = $aBasicAuthorization[ 1 ];
                        }
                    }
                }
            }
            /** @var User $oUser */
            $oUser = oxNew(User::class);
            if ( !$sUser || !$sPassword || !$oUser->login( $sUser, $sPassword ) ) {
                /** @var UserException $oEx */
                $oEx = oxNew(UserException::class, 'EXCEPTION_USER_NOVALIDLOGIN');
                throw $oEx;
            }
        }
        catch ( \Exception $oEx ) {
            $oShop = Registry::getConfig()->getActiveShop();
            header( 'WWW-Authenticate: Basic realm="{' . $oShop->getFieldData('oxname') . '"' );
            header( 'HTTP/1.0 401 Unauthorized' );
            exit( 1 );
        }
    }

    /**
     * @return bool|d3_dev_oxorder|\oxOrder
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function getOrder()
    {
        $oOrder = parent::getOrder();

        if ((false == $oOrder || !$oOrder->getFieldData('oxordernr'))
            && Registry::get(Request::class)->getRequestEscapedParameter("d3dev")
            && false == (bool) Registry::getConfig()->getActiveShop()->isProductiveMode()
            && Registry::getConfig()->getConfigParam('blD3DevShowThankyou')
        ) {
            $this->_oOrder = $this->d3GetLastOrder();
            $oOrder = $this->_oOrder;
        }

        return $oOrder;
    }

    /**
     * @return bool|d3_dev_oxorder
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function d3GetLastOrder()
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()) {
            return false;
        }

        /** @var d3_dev_oxorder $oOrder */
        $oOrder = oxNew(Order::class);
        $oOrder->d3getLastOrder();

        return $oOrder;
    }

    /**
     * @return bool|d3_dev_d3inquiry
     * @throws DatabaseConnectionException
     */
    public function d3GetLastInquiry()
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()) {
            return false;
        }

        /** @var d3_dev_d3inquiry $oInquiry */
        $oInquiry = oxNew('d3inquiry');
        $oInquiry->d3getLastInquiry();

        return $oInquiry;
    }
}
