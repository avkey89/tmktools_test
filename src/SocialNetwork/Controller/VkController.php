<?php

namespace App\SocialNetwork\Controller;


use App\SocialNetwork\Connector\VkConnector;
use App\SocialNetwork\Interfaces\SocialNetworkInterface;
use App\SocialNetwork\Factory\SocialNetworkAuth;

class VkController extends SocialNetworkAuth
{

    public function getSocialNetwork(): SocialNetworkInterface
    {
        // TODO: Implement getUserInfo() method.
        return new VkConnector();
    }
}