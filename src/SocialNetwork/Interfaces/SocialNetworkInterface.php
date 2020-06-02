<?php

namespace App\SocialNetwork\Interfaces;


interface SocialNetworkInterface
{
    public function getCode();

    public function getAccessToken($code);

    public function getUserInfo();
}
