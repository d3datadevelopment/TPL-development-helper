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

namespace D3\Devhelper\Modules\Core;

use D3\Devhelper\Application\Model\Exception\UnauthorisedException;
use D3\Devhelper\Modules\Application\Model as ModuleModel;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ModuleSettingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

class d3_dev_oxemail extends d3_dev_oxemail_parent
{
    /**
     * @param ModuleModel\d3_dev_oxorder $oOrder
     * @param                $sType
     * @return string
     */
    public function d3GetOrderMailContent($oOrder, $sType): string
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()) {
            throw oxNew(UnauthorisedException::class);
        }

        switch (strtolower($sType)) {
            case 'owner_html':
                $sTpl = $this->_sOrderOwnerTemplate;
                break;
            case 'owner_plain':
                $sTpl = $this->_sOrderOwnerPlainTemplate;
                break;
            case 'user_plain':
                $sTpl = $this->_sOrderUserPlainTemplate;
                break;
            case 'user_html':
            default:
                $sTpl = $this->_sOrderUserTemplate;
        }

        $oShop = $this->getShop();

        // cleanup
        $this->clearMailer();

        // add user defined stuff if there is any
        $oOrder = $this->addUserInfoOrderEMail($oOrder);

        $oUser = $oOrder->getOrderUser();
        $this->setUser($oUser);

        // send confirmation to shop owner
        // send not pretending from order user, as different email domain rise spam filters
        $this->setFrom($oShop->getFieldData('oxowneremail'));

        $oLang = Registry::getLang();
        $iOrderLang = $oLang->getObjectTplLanguage();

        // if running shop language is different from admin lang. set in config
        // we have to load shop in config language
        if ($oShop->getLanguage() != $iOrderLang) {
            $oShop = $this->getShop($iOrderLang);
        }

        $this->setSmtp($oShop);

        // create messages
        $this->setViewData("order", $oOrder);

        // Process view data array through oxoutput processor
        $this->processViewArray();

        $renderer = $this->getRenderer();
        return $renderer->renderTemplate($sTpl, $this->getViewData());
    }

    /**
     * required because private in Email class
     * Templating instance getter
     *
     * @return TemplateRendererInterface
     */
    protected function getRenderer()
    {
        $bridge = ContainerFactory::getInstance()->getContainer()
            ->get(TemplateRendererBridgeInterface::class);
//        $bridge->setEngine($this->getSmarty());

        return $bridge->getTemplateRenderer();
    }

    /**
     * @return bool
     * @throws StandardException
     */
    protected function sendMail()
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()) {
            return parent::sendMail();
        }

        $moduleSettingService = ContainerFactory::getInstance()->getContainer()
            ->get(ModuleSettingServiceInterface::class);
        $mailMode = $moduleSettingService->getString(d3_dev_conf::OPTION_MAILMODE, 'd3dev')->toString();

        if ($mailMode === d3_dev_conf::MAILMODE_BLOCK) {
            return true;
        }

        if ($mailMode === d3_dev_conf::MAILMODE_NORMAL) {
            return parent::sendMail();
        }

        if (!in_array($mailMode, [d3_dev_conf::MAILMODE_REDIRECT, d3_dev_conf::MAILMODE_COPY], true)) {
            return true;
        }

        $redirectAddress = trim($moduleSettingService->getString(d3_dev_conf::OPTION_REDIRECTMAIL, 'd3dev')->toString());
        if (!filter_var($redirectAddress, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        if ($mailMode === d3_dev_conf::MAILMODE_REDIRECT) {
            $this->clearAllRecipients();
            $this->clearReplyTos();
            $this->setRecipient($redirectAddress);
            $this->setReplyTo($redirectAddress);

            return !count( $this->getRecipient() ) || parent::sendMail();
        }

        foreach ([$this->getRecipient(), $this->getCc(), $this->getBcc()] as $recipients) {
            foreach ($recipients as $recipient) {
                if (strcasecmp($recipient[0], $redirectAddress) === 0) {
                    return parent::sendMail();
                }
            }
        }

        return !$this->addBCC($redirectAddress) || parent::sendMail();
    }
}
