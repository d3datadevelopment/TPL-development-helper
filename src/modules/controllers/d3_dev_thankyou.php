<?php

// .../?cl=thankyou[&d3orderid=23]
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

class d3_dev_thankyou extends d3_dev_thankyou_parent
{
    /**
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     * @throws object
     * @throws oxUserException
     */
    public function init()
    {
        $sSessChallenge = Registry::getSession()->getVariable('sess_challenge');

        parent::init();

        Registry::getSession()->setVariable('sess_challenge', $sSessChallenge);

        if (oxNew(\OxidEsales\Eshop\Core\Request::class)->getRequestParameter('d3dev')
            && false == (bool) Registry::getConfig()->getActiveShop()->oxshops__oxproductive->value
            && Registry::getConfig()->getConfigParam('blD3DevShowThankyou')
        ) {
            $this->_d3authenticate();
            $oOrder = $this->d3GetLastOrder();
            $oBasket = $oOrder->d3DevGetOrderBasket();
            $this->_oBasket = $oBasket;
        }
    }

    /**
     * @throws object
     * @throws oxUserException
     */
    protected function _d3authenticate ()
    {
        $request = oxNew(\OxidEsales\Eshop\Core\Request::class);

        try {
            $sUser = $request->getRequestParameter( 'usr' );
            $sPassword = $request->getRequestParameter( 'pwd' );

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
            /** @var \OxidEsales\Eshop\Application\Model\User $oUser */
            $oUser = oxNew( \OxidEsales\Eshop\Application\Model\User::class );
            if ( !$sUser || !$sPassword || !$oUser->login( $sUser, $sPassword ) ) {
                /** @var \OxidEsales\Eshop\Core\Exception\UserException $oEx */
                $oEx = oxNew( \OxidEsales\Eshop\Core\Exception\UserException::class, 'EXCEPTION_USER_NOVALIDLOGIN');
                throw $oEx;
            }
        }
        catch ( Exception $oEx ) {
            $oShop = Registry::getConfig()->getActiveShop();
            header( 'WWW-Authenticate: Basic realm="' . $oShop->oxshops__oxname->value . '"' );
            header( 'HTTP/1.0 401 Unauthorized' );
            exit( 1 );
        }
    }

    /**
     * @return bool|d3_dev_oxorder
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function d3GetLastOrder()
    {
        if (Registry::getConfig()->getActiveShop()->oxshops__oxproductive->value) {
            return false;
        }

        /** @var d3_dev_oxorder $oOrder */
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        $oOrder->d3getLastOrder();

        return $oOrder;
    }

    /**
     * @return bool|d3_dev_d3inquiry
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     */
    public function d3GetLastInquiry()
    {
        if (Registry::getConfig()->getActiveShop()->oxshops__oxproductive->value) {
            return false;
        }

        /** @var d3_dev_d3inquiry $oInquiry */
        $oInquiry = oxNew('d3inquiry');
        $oInquiry->d3getLastInquiry();

        return $oInquiry;
    }
}
