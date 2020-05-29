<?php

namespace App\Controller;

use App\Services\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index()
    {
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
        ]);
    }

    /**
     * @param CategoryService $categoryService
     * @param Request $request
     * @Route("/category/add", name="category_add")
     * @return JsonResponse
     */
    public function add(Request $request, CategoryService $categoryService)
    {
        $result = $categoryService->add($request->query->all());

        return new JsonResponse($result["response"], $result["status"]);
    }

    /**
     * @param $id
     * @param CategoryService $categoryService
     * @Route("/category/{id}", name="category_list")
     * @return JsonResponse
     */
    public function read($id, CategoryService $categoryService)
    {
        $result = $categoryService->read($id);

        return new JsonResponse($result["response"], $result["status"]);
    }

    /**
     * @param $id
     * @param Request $request
     * @param CategoryService $categoryService
     * @Route("/category/{id}/update", name="category_update")
     * @return JsonResponse
     */
    public function update($id, Request $request, CategoryService $categoryService)
    {
        $result = $categoryService->update($id, $request->query->all());

        return new JsonResponse($result["response"], $result["status"]);
    }

    /**
     * @param $id
     * @param CategoryService $categoryService
     * @Route("/category/{id}/delete", name="category_delete")
     * @return JsonResponse
     */
    public function delete($id, CategoryService $categoryService)
    {
        $result = $categoryService->delete($id);

        return new JsonResponse($result["response"], $result["status"]);
    }
}
