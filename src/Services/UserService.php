<?php

namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $entityManager;
    private $passwordEncoder;
    private $role = 'ROLE_USER';

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function register($fields, $password)
    {
        $user = new User();

        $user->setName($fields["name"]);
        $user->setLastName($fields["lastName"]);
        $user->setEmail($fields["email"]);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $password
            )
        );

        if (!empty($fields['typeUser']) && $fields['typeUser'] == 2) {
            $this->role = 'ROLE_SALE';
        }
        $role = $this->entityManager->getRepository('App\Entity\Role')
            ->findOneBy(['shortName' => $this->role]);
        $user->addRole($role);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}