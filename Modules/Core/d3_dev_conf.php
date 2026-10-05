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

namespace D3\Devhelper\Modules\Core;

class d3_dev_conf
{
    public const OPTION_PREVENTDELBASKET = 'blD3DevAvoidDelBasket';
    public const OPTION_SHOWTHANKYOU = 'blD3DevShowThankyou';

    public const OPTION_SHOWMAILSINBROWSER = 'blD3DevShowOrderMailsInBrowser';

    public const OPTION_MAILMODE = 'sD3DevMailMode';
    public const OPTION_REDIRECTMAIL = 'sD3DevRedirectMail';

    public const MAILMODE_BLOCK = 'block';
    public const MAILMODE_REDIRECT = 'redirect';
    public const MAILMODE_COPY = 'copy';
    public const MAILMODE_NORMAL = 'normal';
}
