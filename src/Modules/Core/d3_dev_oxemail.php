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
 * @author    D³ Data Development - Daniel Seifert <info@shopmodule.com>
 * @link      http://www.oxidmodule.com
 */

namespace D3\Devhelper\Modules\Core;

use D3\Devhelper\Modules\Application\Model as ModuleModel;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;

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
            return '';
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

        $myConfig = $this->getConfig();

        $oShop = $this->_getShop();

        // cleanup
        $this->_clearMailer();

        // add user defined stuff if there is any
        $oOrder = $this->_addUserInfoOrderEMail($oOrder);

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
            $oShop = $this->_getShop($iOrderLang);
        }

        $this->setSmtp($oShop);

        // create messages
        /** @var \Smarty $oSmarty */
        $oSmarty = $this->_getSmarty();
        $this->setViewData("order", $oOrder);

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        return $oSmarty->fetch($myConfig->getTemplatePath($sTpl, false));
    }

    /**
     * @param ModuleModel\d3_dev_d3inquiry $oInquiry
     *
     * @param                  $sType
     *
     * @return mixed|string
     */
    public function d3GetInquiryMailContent($oInquiry, $sType )
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()) {
            return '';
        }

        switch (strtolower($sType)) {
            case 'owner_html':
                $sTpl = $this->_sInquiryOwnerTemplate;
                break;
            case 'owner_plain':
                $sTpl = $this->_sInquiryOwnerPlainTemplate;
                break;
            case 'user_plain':
                $sTpl = $this->_sInquiryUserPlainTemplate;
                break;
            case 'user_html':
            default:
                $sTpl = $this->_sInquiryUserTemplate;
        }

        $myConfig = $this->getConfig();

        $oShop = $this->_getShop();

        // cleanup
        $this->_clearMailer();

        // add user defined stuff if there is any
        $oInquiry = $this->_addUserInfoOrderEMail($oInquiry);

        $oUser = $oInquiry->getInquiryUser();
        $this->setUser($oUser);

        // send confirmation to shop owner
        // send not pretending from order user, as different email domain rise spam filters
        $this->setFrom($oShop->getFieldData('oxowneremail'));

        $oLang = Registry::getLang();
        $iOrderLang = $oLang->getObjectTplLanguage();

        // if running shop language is different from admin lang. set in config
        // we have to load shop in config language
        if ($oShop->getLanguage() != $iOrderLang) {
            $oShop = $this->_getShop($iOrderLang);
        }

        $this->setSmtp($oShop);

        // create messages
        /** @var \Smarty $oSmarty */
        $oSmarty = $this->_getSmarty();
        $this->setViewData("inquiry", $oInquiry);

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        return $oSmarty->fetch($myConfig->getTemplatePath($sTpl, false));
    }

    /**
     * @return bool
     * @throws StandardException
     */
    protected function _sendMail()
    {
        if (Registry::getConfig()->getActiveShop()->isProductiveMode()) {
            return parent::_sendMail();
        }

        $this->d3clearRecipients();
        $this->d3clearReplies();
        $this->d3clearReplyTo();
        $this->d3clearCC();
        $this->d3clearBCC();

        if (count($this->getRecipient())) {
            return parent::_sendMail();
        }

        return true;
    }

    public function d3clearRecipients()
    {
        $aRecipients = array();
        if (is_array($this->_aRecipients) && count($this->_aRecipients)) {
            foreach ($this->_aRecipients as $aRecInfo) {
                if (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))
                    && $sNewRecipient != $aRecInfo[0]
                ) {
                    $aRecInfo[1] = $aRecInfo[1]." (".$aRecInfo[0].")";
                    $aRecInfo[0] = $sNewRecipient;
                    $aRecipients[] = $aRecInfo;
                } elseif (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))) {
                    $aRecipients[] = $aRecInfo;
                }
            }
        }
        $this->_aRecipients = $aRecipients;
    }

    public function d3clearReplies()
    {
        $aRecipients = array();
        if (is_array($this->_aReplies) && count($this->_aReplies)) {
            foreach ($this->_aReplies as $aRecInfo) {
                if (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))
                    && $sNewRecipient != $aRecInfo[0]
                ) {
                    $aRecInfo[1] = $aRecInfo[1]." (".$aRecInfo[0].")";
                    $aRecInfo[0] = $sNewRecipient;
                    $aRecipients[] = $aRecInfo;
                } elseif (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))) {
                    $aRecipients[] = $aRecInfo;
                }
            }
        }
        $this->_aReplies = $aRecipients;
    }

    public function d3clearReplyTo()
    {
        $aRecipients = array();
        if (is_array($this->ReplyTo) && count($this->ReplyTo)) {
            foreach ($this->ReplyTo as $aRecInfo) {
                if (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))
                    && $sNewRecipient != $aRecInfo[0]
                ) {
                    $aRecInfo[1] = $aRecInfo[1]." (".$aRecInfo[0].")";
                    $aRecInfo[0] = $sNewRecipient;
                    $aRecipients[] = $aRecInfo;
                } elseif (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))) {
                    $aRecipients[] = $aRecInfo;
                }
            }
        }
        $this->ReplyTo = $aRecipients;
    }

    public function d3clearCC()
    {
        $aCc = array();
        if (is_array($this->cc) && count($this->cc)) {
            foreach ($this->cc as $aRecInfo) {
                if (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))
                    && $sNewRecipient != $aRecInfo[0]
                ) {
                    $aRecInfo[1] = $aRecInfo[1]." (".$aRecInfo[0].")";
                    $aRecInfo[0] = $sNewRecipient;
                    $aCc[] = $aRecInfo;
                } elseif (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))) {
                    $aCc[] = $aRecInfo;
                }
            }
        }

        $this->cc = $aCc;
    }

    public function d3clearBCC()
    {
        $aCc = array();
        if (is_array($this->bcc) && count($this->bcc)) {
            foreach ($this->bcc as $aRecInfo) {
                if (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))
                    && $sNewRecipient != $aRecInfo[0]
                ) {
                    $aRecInfo[1] = $aRecInfo[1]." (".$aRecInfo[0].")";
                    $aRecInfo[0] = $sNewRecipient;
                    $aCc[] = $aRecInfo;
                } elseif (($sNewRecipient = $this->getNewRecipient($aRecInfo[0]))) {
                    $aCc[] = $aRecInfo;
                }
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
        if (Registry::getConfig()->getConfigParam('blD3DevBlockMails')) {
            return false;
        } elseif (Registry::getConfig()->getConfigParam('sD3DevRedirectMail')) {
            return trim(Registry::getConfig()->getConfigParam('sD3DevRedirectMail'));
        }

        return $sMailAddress;
    }
}
