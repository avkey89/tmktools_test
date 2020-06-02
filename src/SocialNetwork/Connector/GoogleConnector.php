<?php

namespace App\SocialNetwork\Connector;


use App\SocialNetwork\Interfaces\SocialNetworkInterface;

class GoogleConnector implements SocialNetworkInterface
{
    private $api;
    private $oauth;
    private $responseAccessToken;
    private $responseUserId;
    private $responseUserEmail;

    public function __construct()
    {
        $this->api = new \Google_Client();
        $this->api->setClientId($_ENV["CLIENTIDGOOGLE"]);
        $this->api->setClientSecret($_ENV["SECRETKEYGOOGLE"]);
        $this->api->setRedirectUri($_ENV["REDICRECTURIGOOGLE"]);
        $this->api->addScope("email");
        $this->api->addScope("profile");
        $this->oauth = new \Google_Service_Oauth2($this->api);
    }

    public function getCode()
    {
        return $this->api->createAuthUrl();
    }

    public function getAccessToken($code)
    {
        $token = $this->api->fetchAccessTokenWithAuthCode($_GET['code']);
        $this->responseAccessToken = $this->api->setAccessToken($token['access_token']);
dump($this->responseAccessToken);
        return $this->responseAccessToken;
    }

    public function getUserInfo()
    {
        $google_account_info = $this->oauth->userinfo->get();
        dump($google_account_info);
    }
}