<?php

namespace App\SocialNetwork\Services;


use App\Security\LoginFormAuthenticator;
use App\Services\UserService;
use App\SocialNetwork\Controller\VkController;
use App\SocialNetwork\Factory\SocialNetworkAuth;
use App\SocialNetwork\SocialNetwork;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserAuthentication
{
    private $entityManager;
    private $guardHandler;
    private $authenticator;
    private $request;
    private $userService;

    public function __construct(
        EntityManagerInterface $entityManager,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        RequestStack $requestStack,
        UserService $userService
    )
    {
        $this->entityManager = $entityManager;

        $this->guardHandler = $guardHandler;
        $this->authenticator = $authenticator;
        $this->request = $requestStack;
        $this->userService = $userService;
    }

    public function authorize(SocialNetwork $socialNetwork, SocialNetworkAuth $socialNetworkAuth)
    {
        $UserInfo = $socialNetwork->getUserInfo($socialNetworkAuth, $this->request->getCurrentRequest()->query->all());

        if (!$UserInfo) return false;

        $checkUser = $this->checkUser($UserInfo["email"]);
        if ($checkUser) {
            return $checkUser;
        }

        $newUser = $this->userService->register($UserInfo, $UserInfo["email"]);

        return $newUser;
    }

    private function checkUser($email)
    {
        $findUser = $this->entityManager->getRepository('App\Entity\User')
            ->findOneBy(['email' => $email]);

        return $findUser;
    }
}