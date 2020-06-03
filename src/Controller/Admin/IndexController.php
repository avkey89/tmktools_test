<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("", name="admin_main")
     */
    public function index()
    {
        return $this->render('admin/index/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }
}
