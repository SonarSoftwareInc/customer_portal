<?php

namespace SonarSoftware\CustomerPortalFramework\Exceptions;

use Exception;

/**
 * Class AuthenticationException
 * @package SonarSoftware\CustomerPortalFramework\Exceptions
 */
class ApiException extends Exception {
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}