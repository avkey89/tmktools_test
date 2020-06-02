<?php

namespace App\SocialNetwork\Factory;

use App\SocialNetwork\Interfaces\SocialNetworkInterface;

abstract class SocialNetworkAuth
{
    abstract public function getSocialNetwork(): SocialNetworkInterface;

    public function handle($code)
    {

        $socialNetwork = $this->getSocialNetwork();
        $response = $socialNetwork->getAccessToken($code);

        if (!$response) {
            return false;
        }

        $userInfo = $socialNetwork->getUserInfo();

        return $userInfo;
    }
}