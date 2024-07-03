<?php

namespace D3\Devhelper\Application\Model\Exception;

use Exception;
use OxidEsales\Eshop\Core\Exception\StandardException;

class NoOrderFoundException extends StandardException
{
    public function __construct( $sMessage = "no order found", $iCode = 0, Exception $previous = null )
    {
        parent::__construct( $sMessage, $iCode, $previous );
    }
}