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

namespace D3\Devhelper\Modules\Application\Model;

class d3_dev_oxbasketitem extends d3_dev_oxbasketitem_parent
{
    public function d3ClearArticle()
    {
        $this->_oArticle = null;
    }

    /**
     * @return string
     * @throws \OxidEsales\EshopCommunity\Application\Model\oxArticleException
     * @throws \OxidEsales\Eshop\Core\Exception\StandardException
     * @throws oxNoArticleException
     */
    public function getTitle()
    {
        $oArticle = $this->getArticle();
        $this->_sTitle = $oArticle->oxarticles__oxtitle->value;

        if ($oArticle->oxarticles__oxvarselect->value) {
            $this->_sTitle = $this->_sTitle . ', ' . $this->getVarSelect();
        }

        return $this->_sTitle;
    }
}
