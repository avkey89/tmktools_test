<?php

namespace App\SocialNetwork\Connector;


use App\SocialNetwork\Interfaces\SocialNetworkInterface;

class TwitterConnector implements SocialNetworkInterface
{
    private $clientId;
    private $secretCode;
    private $redirectUri;
    private $responseAccessToken;
    private $responseAccessTokenSecret;
    private $responseScreenName;
    private $responseUserId;
    private $responseUserEmail;
    private $oauth_nonce;
    private $oauth_timestamp;
    private $oauth_token;
    private $oauth_token_secret;
    private $oauth_verifier;

    private $requestTokenUrl = 'https://api.twitter.com/oauth/request_token';
    private $authorizeUrl = 'https://api.twitter.com/oauth/authorize';
    private $accessTokenUrl = 'https://api.twitter.com/oauth/access_token';
    private $accountDataUrl = 'https://api.twitter.com/1.1/users/show.json';

    public function __construct()
    {
        $this->clientId = $_ENV["CLIENTIDTWITTER"];
        $this->redirectUri = $_ENV["REDICRECTURITWITTER"];
        $this->secretCode = $_ENV["SECRETKEYTWITTER"];
        $this->oauth_nonce = md5(uniqid(rand(), true));
        $this->oauth_timestamp = time();
    }

    public function getCode()
    {
        // TODO: Implement getCode() method.
        $signature = $this->getSignature();
        $this->getToken($signature);

        return $this->authorizeUrl . '?oauth_token=' . $this->oauth_token;
    }

    public function getAccessToken($code)
    {
        // TODO: Implement getAccessToken() method.
        $signature = $this->getSignature();
        $this->getToken($signature);

        $this->oauth_token = $code["oauth_token"];
        $this->oauth_verifier = $code["oauth_verifier"];

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode($this->accessTokenUrl)."&";

        $params = array(
            'oauth_nonce=' . $this->oauth_nonce,
            'oauth_signature_method=HMAC-SHA1',
            'oauth_timestamp=' . $this->oauth_timestamp,
            'oauth_consumer_key=' . $this->clientId,
            'oauth_token=' . urlencode($this->oauth_token),
            'oauth_verifier=' . urlencode($this->oauth_verifier),
            'oauth_signature=' . urlencode($signature),
            'oauth_version=1.0'
        );

        $url = $this->accessTokenUrl . '?' . implode('&', $params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        // Парсим ответ
        parse_str($response, $response);

        if (empty($response["oauth_token"])) {
            return false;
        }

        $this->responseAccessToken = $response["oauth_token"];
        $this->responseAccessTokenSecret = $response["oauth_token_secret"];
        $this->responseScreenName = $response["screen_name"];

        return true;
    }

    public function getUserInfo()
    {
        // TODO: Implement getUserInfo() method.
        $this->oauth_nonce = md5(uniqid(rand(), true));
        $this->oauth_timestamp = time();

        $params = array(
            'oauth_consumer_key=' . $this->clientId . '&',
            'oauth_nonce=' . $this->oauth_nonce . '&',
            'oauth_signature_method=HMAC-SHA1' . '&',
            'oauth_timestamp=' . $this->oauth_timestamp . '&',
            'oauth_token=' . $this->responseAccessToken . '&',
            'oauth_version=1.0' . '&',
            'screen_name=' . $this->responseScreenName
        );
        $oauth_base_text = 'GET' . '&' . urlencode($this->accountDataUrl) . '&' . implode('', array_map('urlencode', $params));

        $key = $this->secretCode . '&' . $this->responseAccessTokenSecret;
        $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $params = array(
            'oauth_consumer_key=' . $this->clientId,
            'oauth_nonce=' . $this->oauth_nonce,
            'oauth_signature=' . urlencode($signature),
            'oauth_signature_method=HMAC-SHA1',
            'oauth_timestamp=' . $this->oauth_timestamp,
            'oauth_token=' . urlencode($this->responseAccessToken),
            'oauth_version=1.0',
            'screen_name=' . $this->responseScreenName
        );

        $url = $this->accountDataUrl . '?' . implode('&', $params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        // Парсим ответ
        $user_data = json_decode($response, true);

        return ["name" => $user_data["name"], "lastName" => $user_data["screen_name"], "email" => $user_data["screen_name"]."@twitter.com"];
    }

    private function getSignature()
    {
        // формируем набор параметров
        $params = array(
            'oauth_callback=' . urlencode($this->redirectUri) . '&',
            'oauth_consumer_key=' . $this->clientId . '&',
            'oauth_nonce=' . $this->oauth_nonce . '&',
            'oauth_signature_method=HMAC-SHA1' . '&',
            'oauth_timestamp=' . $this->oauth_timestamp . '&',
            'oauth_version=1.0'
        );

        // склеиваем все параметры, применяя к каждому из них функцию urlencode
        $oauth_base_text = implode('', array_map('urlencode', $params));

        // специальный ключ
        $key = $this->secretCode . '&';

        // формируем общий текст строки
        $oauth_base_text = 'GET' . '&' . urlencode($this->requestTokenUrl) . '&' . $oauth_base_text;

        // хэшируем с помощью алгоритма sha1
        $oauth_signature = base64_encode(hash_hmac('sha1', $oauth_base_text, $key, true));

        return $oauth_signature;
    }

    private function getToken($signature)
    {
        // готовим массив параметров
        $params = array(
            '&' . 'oauth_consumer_key=' . $this->clientId,
            'oauth_nonce=' . $this->oauth_nonce,
            'oauth_signature=' . urlencode($signature),
            'oauth_signature_method=HMAC-SHA1',
            'oauth_timestamp=' . $this->oauth_timestamp,
            'oauth_version=1.0'
        );

        // склеиваем параметры для формирования url
        $url = $this->requestTokenUrl . '?oauth_callback=' . urlencode($this->redirectUri) . implode('&', $params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        // Парсим ответ
        parse_str($response, $response);

        // записываем ответ в переменные
        $this->oauth_token = $response['oauth_token'];
        $this->oauth_token_secret = $response['oauth_token_secret'];

        return true;
    }
}