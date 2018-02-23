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

namespace D3\Devhelper\Modules\Application\Controller
{
    class d3_dev_thankyou_parent extends \OxidEsales\Eshop\Application\Controller\ThankYouController {}
}

namespace D3\Devhelper\Modules\Application\Model
{
    class d3_dev_oxorder_parent extends \OxidEsales\Eshop\Application\Model\Order {}

    class d3_dev_d3inquiry_parent extends d3inquiry {}

    class d3_dev_d3inquiryarticle_parent extends d3inquiryarticle {}

    class d3_dev_oxorderarticle_parent extends \OxidEsales\Eshop\Application\Model\OrderArticle {}

    class d3_dev_oxbasket_parent extends \OxidEsales\Eshop\Application\Model\Basket {}

    class d3_dev_oxbasketitem_parent extends \OxidEsales\Eshop\Application\Model\BasketItem {}

    class d3_dev_order_parent extends \OxidEsales\Eshop\Application\Controller\OrderController {}
}

namespace D3\Devhelper\Modules\Core
{
    class d3_dev_oxemail_parent extends \OxidEsales\Eshop\Core\Email {}
}