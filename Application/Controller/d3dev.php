<?php

namespace D3\Devhelper\Application\Controller;

use D3\Devhelper\Application\Model\Exception\UnauthorisedException;
use D3\Devhelper\Modules\Application\Controller as ModuleController;
use D3\Devhelper\Modules\Core as ModuleCore;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Exception;
use GuzzleHttp\Psr7\ServerRequest;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Controller\ThankYouController;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Email;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingService;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

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

    protected function _authenticate (): void
    {
        try {
            $sUser = Registry::getRequest()->getRequestEscapedParameter('usr');
            $sPassword = Registry::getRequest()->getRequestEscapedParameter('pwd');

            if ( !$sUser || !$sPassword ) {
                $request = ServerRequest::fromGlobals();
                $sUser      = $request->getServerParams()['PHP_AUTH_USER'];
                $sPassword  = $request->getServerParams()['PHP_AUTH_PW'];
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
                throw oxNew( UserException::class, 'EXCEPTION_USER_NOVALIDLOGIN' );
            }
        } catch ( Exception ) {
            $oShop = Registry::getConfig()->getActiveShop();
            header( 'WWW-Authenticate: Basic realm="' . $oShop->getFieldData('oxname') . '"' );
            http_response_code(401);
            exit( 1 );
        }
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALDriverException
     */
    public function showOrderMailContent(): void
    {
        try {
            header( 'Content-type: text/html; charset=' . Registry::getLang()->translateString( 'charset' ) );
            /** @var ModuleSettingService $moduleSettingService */
            $moduleSettingService = ContainerFactory::getInstance()->getContainer()->get( ModuleSettingServiceInterface::class );

            if ( Registry::getConfig()->getActiveShop()->isProductiveMode() ||
                 ! $moduleSettingService->getBoolean( ModuleCore\d3_dev_conf::OPTION_SHOWMAILSINBROWSER, 'd3dev' )
            ) {
                throw oxNew(UnauthorisedException::class);
            }

            $sTpl = Registry::getRequest()->getRequestEscapedParameter( 'type' );

            /** @var ModuleController\d3_dev_thankyou $oThankyou */
            $oThankyou = oxNew( ThankYouController::class );
            $oOrder    = $oThankyou->d3GetLastOrder();

            /** @var ModuleCore\d3_dev_oxemail $oEmail */
            $oEmail = oxNew( Email::class );
            echo $oEmail->d3GetOrderMailContent( $oOrder, $sTpl );
            http_response_code( 200 );
        } catch (UnauthorisedException $exception) {
            echo $exception->getMessage();
            http_response_code(401);
        } catch (Exception $exception) {
            echo $exception->getMessage();
            http_response_code(500);
        } finally {
            Registry::getConfig()->pageClose();
            die();
        }
    }
}
