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

namespace D3\Devhelper\Modules\Application\Model;

use oxArticleInputException;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\OrderArticle;
use OxidEsales\Eshop\Core\Exception\ArticleException;
use OxidEsales\Eshop\Core\Exception\ArticleInputException;
use OxidEsales\Eshop\Core\Exception\NoArticleException;
use oxNoArticleException;

class d3_dev_oxbasketitem extends d3_dev_oxbasketitem_parent
{
    public function d3ClearArticle()
    {
        $this->_oArticle = null;
    }

    /**
     * @return string
     * @throws ArticleException
     * @throws ArticleInputException
     * @throws NoArticleException
     */
    public function getTitle()
    {
        $oArticle = $this->getArticle();
        $this->_sTitle = $oArticle->getFieldData('oxtitle');

        if ($oArticle->getFieldData('oxvarselect')) {
            $this->_sTitle = $this->_sTitle . ', ' . $this->getVarSelect();
        }

        return $this->_sTitle;
    }

    /**
     * @throws oxArticleInputException
     * @throws oxNoArticleException
     */
    public function d3ConvertToArticleObject()
    {
        $oEmbeddedArticle = $this->getArticle();

        if ($oEmbeddedArticle instanceof OrderArticle) {
            $oArticle = oxNew(Article::class);
            $oArticle->load($oEmbeddedArticle->getFieldData('oxartid'));
            $this->_oArticle = $oArticle;
        }
    }
}
