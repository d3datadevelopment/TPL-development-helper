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

 namespace D3\Devhelper\Modules\Application\Controller
 {
     use OxidEsales\Eshop\Application\Controller\ThankYouController;

     class d3_dev_thankyou_parent extends ThankYouController
     {
     }
 }

 namespace D3\Devhelper\Modules\Application\Model
 {
     use OxidEsales\Eshop\Application\Controller\OrderController;
     use OxidEsales\Eshop\Application\Model\Basket;
     use OxidEsales\Eshop\Application\Model\BasketItem;
     use OxidEsales\Eshop\Application\Model\Order;
     use OxidEsales\Eshop\Application\Model\OrderArticle;

     class d3_dev_oxorder_parent extends Order
     {
     }

     class d3_dev_oxorderarticle_parent extends OrderArticle
     {
     }

     class d3_dev_oxbasket_parent extends Basket
     {
     }

     class d3_dev_oxbasketitem_parent extends BasketItem
     {
     }

     class d3_dev_order_parent extends OrderController
     {
     }
 }

 namespace D3\Devhelper\Modules\Core
 {
     use OxidEsales\Eshop\Core\Email;

     class d3_dev_oxemail_parent extends Email
     {
     }
 }
