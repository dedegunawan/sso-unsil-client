<?php
/**
 * Created by PhpStorm.
 * User: tik_squad
 * Date: 05/11/19
 * Time: 17.52
 */

require_once '../../vendor/autoload.php';

\DedeGunawan\SsoUnsilClient\Client::setApiKey('uji_coba');
\DedeGunawan\SsoUnsilClient\Client::setSecretKey('uji_coba');
\DedeGunawan\SsoUnsilClient\Client::setSsoUrl('http://127.0.0.1:8000/');

$client = new \DedeGunawan\SsoUnsilClient\Client();

//$client->profile();
//$client->login();
//$client->isLogin();
//$client->logout();