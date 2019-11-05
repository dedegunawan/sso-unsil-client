<?php
/**
 * Created by PhpStorm.
 * User: tik_squad
 * Date: 05/11/19
 * Time: 18.51
 */

namespace DedeGunawan\SsoUnsilClient\Exceptions;


use Throwable;

class InvalidSsoUrlException extends \Exception
{
    public function __construct(Throwable $previous = null)
    {
        $code = 105;
        $message = "Sso url not valid";
        parent::__construct($message, $code, $previous);
    }
}