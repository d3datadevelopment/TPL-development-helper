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

class d3dev extends FrontendController
{
    public function init()
    {
        $this->_authenticate();

        parent::init();
    }

    protected function _authenticate(): void
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

            $oUser = oxNew(User::class);
            if (!$sUser || !$sPassword || !$oUser->login($sUser, $sPassword) || !$oUser->isMallAdmin()) {
                throw oxNew(UserException::class, 'EXCEPTION_USER_NOVALIDLOGIN');
            }
        } catch (UserException) {
            $realm = (string) Registry::getConfig()->getActiveShop()->getFieldData('oxname');
            $realm = addcslashes(preg_replace('/[\x00-\x1F\x7F]/', '', $realm), "\\\"");
            header('WWW-Authenticate: Basic realm="' . $realm . '"');
            http_response_code(401);
            exit;
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
            header('Cache-Control: no-store, private');
            header('Content-Security-Policy: sandbox allow-same-origin');
            header('Content-type: text/html; charset=' . Registry::getLang()->translateString('charset'));
            /** @var ModuleSettingService $moduleSettingService */
            $moduleSettingService = ContainerFactory::getInstance()->getContainer()->get(ModuleSettingServiceInterface::class);

            if (Registry::getConfig()->getActiveShop()->isProductiveMode() ||
                 ! $moduleSettingService->getBoolean(ModuleCore\d3_dev_conf::OPTION_SHOWMAILSINBROWSER, 'd3dev')
            ) {
                throw oxNew(UnauthorisedException::class);
            }

            $sTpl = Registry::getRequest()->getRequestEscapedParameter('type');

            /** @var ModuleController\d3_dev_thankyou $oThankyou */
            $oThankyou = oxNew(ThankYouController::class);
            $oOrder    = $oThankyou->d3GetLastOrder();

            /** @var ModuleCore\d3_dev_oxemail $oEmail */
            $oEmail = oxNew(Email::class);
            echo $this->wrapPlainContent(
                $sTpl,
                $oEmail->d3GetOrderMailContent($oOrder, $sTpl)
            );
            http_response_code(200);
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

    protected function wrapPlainContent($type, $mailContent)
    {
        if (stristr($type, 'plain')){
            return sprintf(
                <<<TEXTAREA
<textarea style="width: 100%%; height: 100%%">%s</textarea>
TEXTAREA,
                htmlspecialchars($mailContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
            );
        }

        return $mailContent;
    }
}
