<?php

namespace App\SocialNetwork;


use App\SocialNetwork\Interfaces\SocialNetworkInterface;
use App\SocialNetwork\Factory\SocialNetworkAuth;

class SocialNetwork
{

    public function getResponseUrl(SocialNetworkInterface $socialNetwork)
    {
        return $socialNetwork->getCode();
    }

    public function getUserInfo(SocialNetworkAuth $socialNetworkAuth, $code)
    {
        return $socialNetworkAuth->handle($code);
    }
}