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

use OxidEsales\Eshop\Application\Model\ArticleList;

class d3_dev_oxorderarticle extends d3_dev_oxorderarticle_parent
{
    /**
     * @return null|ArticleList
     */
    public function getCustomerAlsoBoughtThisProducts()
    {
        $oArticle = $this->getArticle();

        /** @var ArticleList $artList */
        $artList = $oArticle->getCustomerAlsoBoughtThisProducts();

        return $artList;
    }
}
