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
