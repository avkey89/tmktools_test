<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @Route("", name="admin_user_list")
     */
    public function index(UserRepository $userRepository)
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @Route("/new", name="admin_role_new", methods={"GET","POST"})
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'invalid_message' => 'The password fields must match.',
            'first_options'  => ['label' => 'Password', 'attr' => ['class' => 'form-control']],
            'second_options' => ['label' => 'Repeat Password', 'attr' => ['class' => 'form-control']],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a password',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     * @Route("/{id}/edit", name="admin_user_edit", methods={"GET","POST"})
     * @return Response
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => false,
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'mapped' => false,
            'invalid_message' => 'The password fields must match.',
            'first_options'  => ['label' => 'Password', 'attr' => ['class' => 'form-control']],
            'second_options' => ['label' => 'Repeat Password', 'attr' => ['class' => 'form-control']],
            'constraints' => [
                new Length([
                    'min' => 6,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
            ],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param User $user
     * @Route("/{id}", name="admin_user_delete", methods={"DELETE", "GET", "POST"})
     * @return RedirectResponse
     */
    public function delete(User $user): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$role->getId(), $request->request->get('_token'))) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();
        //}

        return $this->redirectToRoute('admin_user_list');
    }
}
