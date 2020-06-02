<?php

namespace App\SocialNetwork\Controller;


use App\SocialNetwork\Connector\GoogleConnector;
use App\SocialNetwork\Factory\SocialNetworkAuth;
use App\SocialNetwork\Interfaces\SocialNetworkInterface;

class GoogleController extends SocialNetworkAuth
{
    public function getSocialNetwork(): SocialNetworkInterface
    {
        // TODO: Implement getUserInfo() method.
        return new GoogleConnector();
    }
}