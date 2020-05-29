<?php

namespace App\Controller;

use App\Services\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product")
     */
    public function index()
    {
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
        ]);
    }

    /**
     * @param ProductService $productService
     * @param Request $request
     * @Route("/add", name="product_add")
     * @return JsonResponse
     */
    public function add(Request $request, ProductService $productService)
    {
        $result = $productService->add($request->query->all());

        return new JsonResponse($result["response"], $result["status"]);
    }

    /**
     * @param $id
     * @param ProductService $productService
     * @Route("/{id}", name="product_list")
     * @return JsonResponse
     */
    public function read($id, ProductService $productService)
    {
        $result = $productService->read($id);

        return new JsonResponse($result["response"], $result["status"]);
    }

    /**
     * @param $id
     * @param Request $request
     * @param ProductService $productService
     * @Route("/{id}/update", name="product_update")
     * @return JsonResponse
     */
    public function update($id, Request $request, ProductService $productService)
    {
        $result = $productService->update($id, $request->query->all());

        return new JsonResponse($result["response"], $result["status"]);
    }

    /**
     * @param $id
     * @param ProductService $productService
     * @Route("/{id}/delete", name="product_delete")
     * @return JsonResponse
     */
    public function delete($id, ProductService $productService)
    {
        $result = $productService->delete($id);

        return new JsonResponse($result["response"], $result["status"]);
    }
}
