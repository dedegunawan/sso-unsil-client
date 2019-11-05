<?php
/**
 * Created by PhpStorm.
 * User: tik_squad
 * Date: 05/11/19
 * Time: 19.17
 */

namespace DedeGunawan\SsoUnsilClient\Exceptions;


use Throwable;

class InvalidApiKeyException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        $code = 107;
        $message = "Invalid Api Key";
        parent::__construct($message, $code, $previous);
    }
}