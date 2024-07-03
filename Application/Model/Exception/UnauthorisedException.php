<?php

namespace D3\Devhelper\Application\Model\Exception;

use OxidEsales\Eshop\Core\Exception\StandardException;

class UnauthorisedException extends StandardException
{
    public function __construct( $sMessage = "unauthorised, disable productive and activate option", $iCode = 0, Exception $previous = null )
    {
        parent::__construct( $sMessage, $iCode, $previous );
    }
}