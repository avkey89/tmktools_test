<?php

namespace App\SocialNetwork\Controller;


use App\SocialNetwork\Connector\TwitterConnector;
use App\SocialNetwork\Factory\SocialNetworkAuth;
use App\SocialNetwork\Interfaces\SocialNetworkInterface;

class TwitterController extends SocialNetworkAuth
{
    public function getSocialNetwork(): SocialNetworkInterface
    {
        // TODO: Implement getUserInfo() method.
        return new TwitterConnector();
    }
}