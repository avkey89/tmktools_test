<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/role")
 * @IsGranted("ROLE_SUPER")
 */
class RoleController extends AbstractController
{
    /**
     * @param RoleRepository $roleRepository
     * @Route("", name="admin_role_index", methods={"GET"})
     * @return Response
     */
    public function index(RoleRepository $roleRepository): Response
    {
        return $this->render('admin/role/index.html.twig', [
            'roles' => $roleRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @Route("/new", name="admin_role_new", methods={"GET","POST"})
     * @return Response
     */
    public function new(Request $request): Response
    {
        $role = new Role();
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($role);
            $entityManager->flush();

            return $this->redirectToRoute('admin_role_index');
        }

        return $this->render('admin/role/new.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Role $role
     * @Route("/{id}/edit", name="admin_role_edit", methods={"GET","POST"})
     * @return Response
     */
    public function edit(Request $request, Role $role): Response
    {
        $form = $this->createForm(RoleType::class, $role);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_role_index');
        }

        return $this->render('admin/role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Role $role
     * @Route("/{id}", name="admin_role_delete", methods={"DELETE", "GET", "POST"})
     * @return RedirectResponse
     */
    public function delete(Request $request, Role $role): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$role->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($role);
            $entityManager->flush();
        //}

        return $this->redirectToRoute('admin_role_index');
    }
}
