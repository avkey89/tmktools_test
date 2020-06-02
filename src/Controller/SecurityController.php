<?php

namespace App\Controller;


use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use App\SocialNetwork\Services\UserAuthentication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\SocialNetwork\Connector\VkConnector;
use App\SocialNetwork\Controller\VkController;
use App\SocialNetwork\SocialNetwork;

class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param UserPasswordEncoderInterface $encoder
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param GuardAuthenticatorHandler $guardHandler
     * @param LoginFormAuthenticator $authenticator
     * @param UserAuthentication $userAuthentication
     * @Route("/login", name="app_login")
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, UserPasswordEncoderInterface $encoder, Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, UserAuthentication $userAuthentication): Response
    {
        // TODO: add redirect
        /*if ($this->getUser()) {
            return $this->redirectToRoute('target_path');
        }*/

        $social = new SocialNetwork();

        if($request->query->get('code')) {
            try {

                $socialAuthenticationUser = $userAuthentication->authorize($social);
                if ($socialAuthenticationUser) {
                    return $guardHandler->authenticateUserAndHandleSuccess(
                        $socialAuthenticationUser,
                        $request,
                        $authenticator,
                        'main' // firewall name in security.yaml
                    );
                }

            } catch (\Exception $e) {
                dump($e->getMessage());
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $SocialLinks = [];
        $SocialLinks["vk"] = $social->getResponseUrl(new VkConnector());

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'socialLinks' => $SocialLinks]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
