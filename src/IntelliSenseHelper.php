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

    use OxidEsales\Eshop\Application\Controller\ThankYouController;

    class d3_dev_thankyou_parent extends ThankYouController {}
}

namespace D3\Devhelper\Modules\Application\Model
{

    use OxidEsales\Eshop\Application\Controller\OrderController;
    use OxidEsales\Eshop\Application\Model\Basket;
    use OxidEsales\Eshop\Application\Model\BasketItem;
    use OxidEsales\Eshop\Application\Model\Order;
    use OxidEsales\Eshop\Application\Model\OrderArticle;

    class d3_dev_oxorder_parent extends Order {}

    class d3_dev_d3inquiry_parent extends d3inquiry {}

    class d3_dev_d3inquiryarticle_parent extends d3inquiryarticle {}

    class d3_dev_oxorderarticle_parent extends OrderArticle {}

    class d3_dev_oxbasket_parent extends Basket {}

    class d3_dev_oxbasketitem_parent extends BasketItem {}

    class d3_dev_order_parent extends OrderController {}
}

namespace D3\Devhelper\Modules\Core
{

    use OxidEsales\Eshop\Core\Email;

    class d3_dev_oxemail_parent extends Email {}
}