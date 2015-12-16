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

class d3_dev_oxemail extends d3_dev_oxemail_parent
{
    /**
     * @param d3_dev_oxorder $oOrder
     *
     * @return mixed|string
     */
    public function d3GetOrderMailContent($oOrder, $sType)
    {
        if (oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value) {
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
        $this->setFrom($oShop->oxshops__oxowneremail->value);

        $oLang = oxRegistry::getLang();
        $iOrderLang = $oLang->getObjectTplLanguage();

        // if running shop language is different from admin lang. set in config
        // we have to load shop in config language
        if ($oShop->getLanguage() != $iOrderLang) {
            $oShop = $this->_getShop($iOrderLang);
        }

        $this->setSmtp($oShop);

        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setViewData("order", $oOrder);

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        return $oSmarty->fetch($myConfig->getTemplatePath($sTpl, false));
    }

    /**
     * @param d3_dev_d3inquiry $oInquiry
     *
     * @return mixed|string
     */
    public function d3GetInquiryMailContent($oInquiry, $sType)
    {
        if (oxRegistry::getConfig()->getActiveShop()->oxshops__oxproductive->value) {
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
        $this->setFrom($oShop->oxshops__oxowneremail->value);

        $oLang = oxRegistry::getLang();
        $iOrderLang = $oLang->getObjectTplLanguage();

        // if running shop language is different from admin lang. set in config
        // we have to load shop in config language
        if ($oShop->getLanguage() != $iOrderLang) {
            $oShop = $this->_getShop($iOrderLang);
        }

        $this->setSmtp($oShop);

        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setViewData("inquiry", $oInquiry);

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        return $oSmarty->fetch($myConfig->getTemplatePath($sTpl, false));
    }
}
