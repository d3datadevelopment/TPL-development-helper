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
