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

class d3_dev_d3inquiryarticle extends d3_dev_d3inquiryarticle_parent
{
    /**
     * @return array
     */
    public function getCustomerAlsoBoughtThisProducts()
    {
        $oArticle = $this->getArticle();

        return $oArticle->getCustomerAlsoBoughtThisProducts();
    }

    /**
     * Returns true if ordered product is bundle
     *
     * @return bool
     */
    public function isBundle()
    {
        return ( bool ) $this->d3inquiryarticles__oxisbundle->value;
    }
}
