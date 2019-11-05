<?php
/**
 * Created by PhpStorm.
 * User: tik_squad
 * Date: 05/11/19
 * Time: 17.50
 */

namespace DedeGunawan\SsoUnsilClient;



use DedeGunawan\SsoUnsilClient\Exceptions\ApiKeyException;
use DedeGunawan\SsoUnsilClient\Exceptions\InvalidApiKeyException;
use DedeGunawan\SsoUnsilClient\Exceptions\InvalidSsoUrlException;
use DedeGunawan\SsoUnsilClient\Exceptions\SecretKeyException;
use DedeGunawan\SsoUnsilClient\Exceptions\SsoUrlException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

class Client
{
    protected static $api_key;
    protected static $secret_key;
    protected static $sso_url;
    protected $login_url;
    protected $logout_url;
    protected $profile_url;
    protected static $debug;
    protected $verify=false;
    protected $token;
    protected $session_file;
    protected $exceptionClass;

    public function __construct()
    {
        $this->setup();
    }


    public function login()
    {
        if (!$this->isLogin()) $this->redirect($this->getLoginUrl());
        return true;
    }

    public function logout()
    {
        $this->redirect($this->getLogoutUrl());
    }

    public function isLogin()
    {
        return array_key_exists("id", $this->profile());
    }

    protected function redirect($url)
    {
        $sent = headers_sent();
        if ($sent) {
            $url = urlencode($url);
            echo "Redirecting to <a href='$url'>$url</a>";
            echo "<script>window.location.href='$url';</script>";
            die();
        } else {
            header("Location:$url");
            echo "Redirecting to <a href='$url'>$url</a>";
            die();
        }
    }

    public function profile()
    {
        static $response_json;
        if ($response_json == null) {
            $datas = array();
            try {
                $profile_url = $this->getProfileUrl();
                $client = $this->getGuzzle();
                $response = $client->post($profile_url, [
                    RequestOptions::JSON => [
                        'api_key' => self::getApiKey(),
                        'secret_key' => self::getSecretKey(),
                        'token' => $this->getToken()
                    ],
                ]);
                $datas = $this->parseResponse($response);
            } catch (\Exception $exception) {
                $this->setOrCall(new InvalidApiKeyException());
            }

            if (
                @$datas['status'] == 1
                && is_array($datas['datas'])
                && is_array($datas['datas']['profile'])
            ) {
                $response_json = $datas['datas']['profile'];
            }
        }
        return $response_json;


    }

    /**
     * @throws \Exception
     */
    protected function setup()
    {
        if (!self::getApiKey()) $this->setOrCall(new ApiKeyException());
        if (!self::getSecretKey()) $this->setOrCall(new SecretKeyException());
        if (!self::getSsoUrl()) $this->setOrCall(new SsoUrlException());

        $this->setupClient();
        $this->setupToken();
    }


    protected function setupToken()
    {
        $this->setSessionFile(__DIR__."/abchref_v001.xyz/abchref_v001.xyz");
        $this->loadToken();
    }

    protected function loadToken()
    {
        if (!file_exists($this->getSessionFile())) $this->saveToken();
        $token = file_get_contents($this->getSessionFile());
        $this->setToken($token);
    }

    protected function saveToken()
    {
        return file_put_contents($this->getSessionFile(), $this->getToken());
    }

    protected function setupClient()
    {
        $client_url = self::getSsoUrl()."/sso/url";

        $guzzle = $this->getGuzzle();

        $response = $guzzle->get($client_url);
        $data = $this->parseResponse($response);
        if (!is_array($data) || !$data['login'] || !$data['logout'] || !$data['profile'])
            throw new InvalidSsoUrlException();

        $this->setLoginUrl($data['login']);
        $this->setLogoutUrl($data['logout']);
        $this->setProfileUrl($data['profile']);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getGuzzle()
    {
        $options['debug'] = (int) self::getDebug();
        $options['verify'] = $this->isVerify();

        $client = new \GuzzleHttp\Client($options);
        return $client;
    }

    /**
     * @param Response $response
     * @return mixed
     */
    protected function parseResponse($response)
    {
        return @json_decode($response->getBody()->getContents(), 1);
    }



    /**
     * @return mixed
     */
    public static function getApiKey()
    {
        return self::$api_key;
    }

    /**
     * @param mixed $api_key
     */
    public static function setApiKey($api_key)
    {
        self::$api_key = $api_key;
    }

    /**
     * @return mixed
     */
    public static function getSecretKey()
    {
        return self::$secret_key;
    }

    /**
     * @param mixed $secret_key
     */
    public static function setSecretKey($secret_key)
    {
        self::$secret_key = $secret_key;
    }

    /**
     * @return mixed
     */
    public static function getSsoUrl()
    {
        return self::$sso_url;
    }

    /**
     * @param mixed $sso_url
     */
    public static function setSsoUrl($sso_url)
    {
        self::$sso_url = $sso_url;
    }

    /**
     * @return mixed
     */
    public function getLoginUrl()
    {
        return $this->login_url;
    }

    /**
     * @param mixed $login_url
     */
    public function setLoginUrl($login_url)
    {
        $this->login_url = $login_url;
    }

    /**
     * @return mixed
     */
    public function getLogoutUrl()
    {
        return $this->logout_url;
    }

    /**
     * @param mixed $logout_url
     */
    public function setLogoutUrl($logout_url)
    {
        $this->logout_url = $logout_url;
    }

    /**
     * @return mixed
     */
    public function getProfileUrl()
    {
        return $this->profile_url;
    }

    /**
     * @param mixed $profile_url
     */
    public function setProfileUrl($profile_url)
    {
        $this->profile_url = $profile_url;
    }

    /**
     * @return mixed
     */
    public static function getDebug()
    {
        return self::$debug;
    }

    /**
     * @param mixed $debug
     */
    public static function setDebug($debug)
    {
        self::$debug = $debug;
    }

    /**
     * @return bool
     */
    public function isVerify()
    {
        return $this->verify;
    }

    /**
     * @param bool $verify
     */
    public function setVerify($verify)
    {
        $this->verify = $verify;
    }

    /**
     * @return mixed
     */
    protected function getSessionFile()
    {
        return $this->session_file;
    }

    /**
     * @param mixed $session_file
     */
    protected function setSessionFile($session_file)
    {
        $this->session_file = $session_file;
    }

    /**
     * @return mixed
     */
    protected function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return \Exception
     */
    protected function getExceptionClass()
    {
        return $this->exceptionClass;
    }

    /**
     * @param \Exception $exceptionClass
     */
    protected function setExceptionClass($exceptionClass)
    {
        $this->exceptionClass = $exceptionClass;
    }


    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    protected function setOrCall(\Exception $exception)
    {
        $this->setExceptionClass($exception);
        if (self::getDebug()) throw $exception;
    }



}