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
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

class d3_dev_oxemail extends d3_dev_oxemail_parent
{
    /**
     * @param ModuleModel\d3_dev_oxorder $oOrder
     * @param                $sType
     * @return mixed|string
     */
    public function d3GetOrderMailContent($oOrder, $sType)
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

        $myConfig = Registry::getConfig();

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

        $templateExtension = ContainerFactory::getInstance()->getContainer()
            ->getParameter('oxid_esales.templating.engine_template_extension');
        $sTpl .= '.'.$templateExtension;

        return $renderer->renderTemplate($myConfig->getTemplatePath($sTpl, false), $this->getViewData());
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
     * @param $aRecInfo
     * @param array $aCc
     * @return array
     */
    public function d3ChangeRecipient($aRecInfo, array $aCc): array
    {
        if (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))
            && $sNewRecipient != $aRecInfo[0]
        ) {
            $aRecInfo[1] = $aRecInfo[1] . " (" . $aRecInfo[0] . ")";
            $aRecInfo[0] = $sNewRecipient;
            $aCc[] = $aRecInfo;
        } elseif (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))) {
            $aCc[] = $aRecInfo;
        }
        return $aCc;
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

        $this->d3clearRecipients();
        $this->d3clearReplies();
        $this->d3clearReplyTo();
        $this->d3clearCC();
        $this->d3clearBCC();

        if (count($this->getRecipient())) {
            return parent::sendMail();
        }

        return true;
    }

    public function d3clearRecipients()
    {
        $aRecipients = [];
        if (is_array($this->_aRecipients) && count($this->_aRecipients)) {
            foreach ($this->_aRecipients as $aRecInfo) {
                $aRecipients = $this->d3ChangeRecipient($aRecInfo, $aRecipients);
            }
        }
        $this->_aRecipients = $aRecipients;
    }

    public function d3clearReplies()
    {
        $aRecipients = [];
        if (is_array($this->_aReplies) && count($this->_aReplies)) {
            foreach ($this->_aReplies as $aRecInfo) {
                $aRecipients = $this->d3ChangeRecipient($aRecInfo, $aRecipients);
            }
        }
        $this->_aReplies = $aRecipients;
    }

    public function d3clearReplyTo()
    {
        $aRecipients = [];
        if (is_array($this->ReplyTo) && count($this->ReplyTo)) {
            foreach ($this->ReplyTo as $aRecInfo) {
                $aRecipients = $this->d3ChangeRecipient($aRecInfo, $aRecipients);
            }
        }
        $this->ReplyTo = $aRecipients;
    }

    public function d3clearCC()
    {
        $aCc = [];
        if (is_array($this->cc) && count($this->cc)) {
            foreach ($this->cc as $aRecInfo) {
                $aCc = $this->d3ChangeRecipient($aRecInfo, $aCc);
            }
        }

        $this->cc = $aCc;
    }

    public function d3clearBCC()
    {
        $aCc = [];
        if (is_array($this->bcc) && count($this->bcc)) {
            foreach ($this->bcc as $aRecInfo) {
                $aCc = $this->d3ChangeRecipient($aRecInfo, $aCc);
            }
        }

        $this->bcc = $aCc;
    }

    /**
     * @param $sMailAddress
     *
     * @return bool|string
     */
    public function getNewRecipient($sMailAddress)
    {
        if (Registry::getConfig()->getConfigParam(d3_dev_conf::OPTION_BLOCKMAIL)) {
            return false;
        } elseif (Registry::getConfig()->getConfigParam(d3_dev_conf::OPTION_REDIRECTMAIL)) {
            return trim(Registry::getConfig()->getConfigParam(d3_dev_conf::OPTION_REDIRECTMAIL));
        }

        return $sMailAddress;
    }
}
