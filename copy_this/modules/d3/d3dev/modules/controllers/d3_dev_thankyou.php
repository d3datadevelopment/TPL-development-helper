<?php

// .../?cl=thankyou[&d3orderid=23]

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
    public function init()
    {
        $sSessChallenge = oxRegistry::getSession()->getVariable('sess_challenge');

        parent::init();

        oxRegistry::getSession()->setVariable('sess_challenge', $sSessChallenge);

        if (oxRegistry::getConfig()->getRequestParameter('d3dev')
            && false == (bool) oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value
            && oxRegistry::getConfig()->getConfigParam('blD3DevShowThankyou')
        ) {
            $this->_d3authenticate();
            $oOrder = $this->d3GetLastOrder();
            $oBasket = $oOrder->d3DevGetOrderBasket();
            $this->_oBasket = $oBasket;
        }
    }

    protected function _d3authenticate ()
    {
        $oConfig = oxRegistry::getConfig();

        try {
            $sUser = $oConfig->getRequestParameter( 'usr' );
            $sPassword = $oConfig->getRequestParameter( 'pwd' );

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
            /** @var oxUser $oUser */
            $oUser = oxNew( 'oxuser' );
            if ( !$sUser || !$sPassword || !$oUser->login( $sUser, $sPassword ) ) {
                $oEx = oxNew( 'oxuserexception' );
                $oEx->setMessage( 'EXCEPTION_USER_NOVALIDLOGIN' );
                throw $oEx;
            }
        }
        catch ( Exception $oEx ) {
            $oShop = $oConfig->getActiveShop();
            header( 'WWW-Authenticate: Basic realm="' . $oShop->oxshops__oxname->value . '"' );
            header( 'HTTP/1.0 401 Unauthorized' );
            exit( 1 );
        }
    }

    /**
     * @return d3_dev_oxorder
     */
    public function d3GetLastOrder()
    {
        if (oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value) {
            return false;
        }

        /** @var d3_dev_oxorder $oOrder */
        $oOrder = oxNew('oxorder');
        $oOrder->d3getLastOrder();

        return $oOrder;
    }

    /**
     * @return d3_dev_d3inquiry
     */
    public function d3GetLastInquiry()
    {
        if (oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value) {
            return false;
        }

        /** @var d3_dev_d3inquiry $oInquiry */
        $oInquiry = oxNew('d3inquiry');
        $oInquiry->d3getLastInquiry();

        return $oInquiry;
    }
}
