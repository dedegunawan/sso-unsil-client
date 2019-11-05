<?php
/**
 * Created by PhpStorm.
 * User: tik_squad
 * Date: 05/11/19
 * Time: 17.55
 */

namespace DedeGunawan\SsoUnsilClient\Exceptions;


use Throwable;

class ApiKeyException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        $code = 102;
        $message = "Api key must be set";
        parent::__construct($message, $code, $previous);
    }
}