<?php
/**
 * Created by PhpStorm.
 * User: tik_squad
 * Date: 05/11/19
 * Time: 17.57
 */

namespace DedeGunawan\SsoUnsilClient\Exceptions;


class SecretKeyException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        $code = 103;
        $message = "Secret key must be set";
        parent::__construct($message, $code, $previous);
    }
}