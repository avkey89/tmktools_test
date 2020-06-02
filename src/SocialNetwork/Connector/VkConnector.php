<?php

namespace App\SocialNetwork\Connector;


use App\SocialNetwork\Interfaces\SocialNetworkInterface;
use VK\Client\VKApiClient;
use VK\OAuth\Scopes\VKOAuthUserScope;
use VK\OAuth\VKOAuth;
use VK\OAuth\VKOAuthDisplay;
use VK\OAuth\VKOAuthResponseType;

class VkConnector implements SocialNetworkInterface
{
    private $api;
    private $oauth;
    private $clientId;
    private $redirectUri = 'http://opencode.local/register';
    private $responseAccessToken;
    private $responseUserId;
    private $responseUserEmail;
    private $secretCode;

    public function __construct()
    {
        $this->api = new VKApiClient();
        $this->oauth = new VKOAuth();
        $this->clientId = $_ENV["CLIENTIDVK"];
        $this->redirectUri = $_ENV["REDICRECTURIVK"];
        $this->secretCode = $_ENV["SECTERKEYVK"];
    }

    public function getCode()
    {
        // TODO: Implement getCode() method.
        return $this->oauth->getAuthorizeUrl(VKOAuthResponseType::CODE, $this->clientId, $this->redirectUri, VKOAuthDisplay::PAGE, [VKOAuthUserScope::EMAIL], '');
    }

    public function getAccessToken($code): bool
    {
        // TODO: Implement getAccessToken() method.
        $response = $this->oauth->getAccessToken($this->clientId, $this->secretCode, $this->redirectUri, $code);

        if (!$response["access_token"]) {
            return false;
        }

        $this->responseAccessToken = $response["access_token"];
        $this->responseUserId = $response["user_id"];
        $this->responseUserEmail = $response["email"];

        return true;
    }

    public function getUserInfo()
    {
        $responseData = $this->api->users()->get($this->responseAccessToken, ['user_ids' => [$this->responseUserId], 'fields' => ['first_name', 'last_name']]);

        return ["name" => $responseData[0]["first_name"], "lastName" => $responseData[0]["last_name"], "email" => $this->responseUserEmail];
    }
}