<?php

namespace D3\Devhelper\Application\Controller;

use D3\Devhelper\Modules\Application\Controller as ModuleController;
use D3\Devhelper\Modules\Core as ModuleCore;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Controller\ThankYouController;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Email;
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

class d3dev extends FrontendController
{
    public function init()
    {
        $this->_authenticate();

        parent::init();
    }

    protected function _authenticate ()
    {
        try {
            $sUser = Registry::get(Request::class)->getRequestEscapedParameter('usr');
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
            $oUser = oxNew( User::class );
            if ( !$sUser || !$sPassword || !$oUser->login( $sUser, $sPassword ) ) {
                /** @var UserException $oEx */
                $oEx = oxNew( UserException::class, 'EXCEPTION_USER_NOVALIDLOGIN' );

                throw $oEx;
            }
        }
        catch ( \Exception $oEx ) {
            $oShop = Registry::getConfig()->getActiveShop();
            header( 'WWW-Authenticate: Basic realm="' . $oShop->getFieldData('oxname') . '"' );
            header( 'HTTP/1.0 401 Unauthorized' );
            exit( 1 );
        }
    }

    /**
     * @throws DatabaseConnectionException
     * @throws DatabaseErrorException
     */
    public function showOrderMailContent()
    {
        header('Content-type: text/html; charset='.Registry::getLang()->translateString('charset'));

        if (Registry::getConfig()->getActiveShop()->isProductiveMode()
            || false == Registry::getConfig()->getConfigParam('blD3DevShowOrderMailsInBrowser')
        ) {
            Registry::getUtils()->redirect(Registry::getConfig()->getShopUrl().'index.php?cl=start');
        }

        $sTpl = Registry::get(Request::class)->getRequestEscapedParameter('type');

        /** @var ModuleController\d3_dev_thankyou $oThankyou */
        $oThankyou = oxNew(ThankYouController::class);
        $oOrder = $oThankyou->d3GetLastOrder();

        /** @var ModuleCore\d3_dev_oxemail $oEmail */
        $oEmail = oxNew(Email::class);
        echo $oEmail->d3GetOrderMailContent($oOrder, $sTpl);
        die();
    }

    /**
     * @throws DatabaseConnectionException
     */
    public function showInquiryMailContent()
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()
            || false == Registry::getConfig()->getConfigParam('blD3DevShowOrderMailsInBrowser')
        ) {
            Registry::getUtils()->redirect(Registry::getConfig()->getShopUrl().'index.php?cl=start');
        }

        $sTpl = Registry::get(Request::class)->getRequestEscapedParameter('type');

        /** @var ModuleController\d3_dev_thankyou $oThankyou */
        $oThankyou = oxNew(ThankYouController::class);
        $oOrder = $oThankyou->d3GetLastInquiry();

        /** @var ModuleCore\d3_dev_oxemail $oEmail */
        $oEmail = oxNew(Email::class);
        echo $oEmail->d3GetInquiryMailContent($oOrder, $sTpl);
        die();
    }
}
