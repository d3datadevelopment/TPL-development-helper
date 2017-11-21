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

class d3dev extends oxUBase
{
    public function init()
    {
        $this->_authenticate();

        parent::init();
    }

    protected function _authenticate ()
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

    public function showOrderMailContent()
    {
        header('Content-type: text/html; charset='.oxRegistry::getLang()->translateString('charset'));
        
        if (oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value
            || false == oxRegistry::getConfig()->getConfigParam('blD3DevShowOrderMailsInBrowser')
        ) {
            oxRegistry::getUtils()->redirect(oxRegistry::getConfig()->getShopUrl().'index.php?cl=start');
        }

        $sTpl = oxRegistry::getConfig()->getRequestParameter('type');

        /** @var d3_dev_thankyou $oThankyou */
        $oThankyou = oxNew('thankyou');
        $oOrder = $oThankyou->d3GetLastOrder();

        /** @var d3_dev_oxemail $oEmail */
        $oEmail = oxNew('oxemail');
        echo $oEmail->d3GetOrderMailContent($oOrder, $sTpl);
        die();
    }

    public function showInquiryMailContent()
    {
        if (oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value
            || false == oxRegistry::getConfig()->getConfigParam('blD3DevShowOrderMailsInBrowser')
        ) {
            oxRegistry::getUtils()->redirect(oxRegistry::getConfig()->getShopUrl().'index.php?cl=start');
        }

        $sTpl = oxRegistry::getConfig()->getRequestParameter('type');

        /** @var d3_dev_thankyou $oThankyou */
        $oThankyou = oxNew('thankyou');
        $oOrder = $oThankyou->d3GetLastInquiry();

        /** @var d3_dev_oxemail $oEmail */
        $oEmail = oxNew('oxemail');
        echo $oEmail->d3GetInquiryMailContent($oOrder, $sTpl);
        die();
    }
}
