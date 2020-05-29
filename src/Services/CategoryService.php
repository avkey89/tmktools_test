<?php

namespace App\Services;


use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryService
{
    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function add($fields)
    {
        if (empty($fields)) {
            return [
                "response" => [
                    "message" => "There is no information to add",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        if (empty($fields["title"])) {
            return [
                "response" => [
                    "message" => "Parameter 'title' not found.",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        if (!empty($fields["eId"])) {
            $category = $this->entityManager->getRepository('App\Entity\Category')->findOneBy(["eid" => $fields["eId"]]);
            if ($category) {
                return $this->update($fields["eId"], $fields);
            }
        }

        $category = new Category();
        $category->setTitle($fields["title"]);
        if (!empty($fields["eId"])) {
            $category->setEid($fields["eId"]);
        }

        $errorsProduct = $this->validator->validate($category);
        // TODO: нормальное сообщение об ошибке без указания сущности и code UUID
        if (count($errorsProduct) > 0) {
            return [
                "response" => [
                    "message" => (string)$errorsProduct,
                ],
                "status" => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return [
            "response" => [
                "message" => "Category added.",
                "id" => $category->getId()
            ],
            "status" => Response::HTTP_OK
        ];
    }

    public function update($id, $fields)
    {
        if (empty($id)) {
            return [
                "response" => [
                    "message" => "Not found parameter",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        if (empty($fields)) {
            return [
                "response" => [
                    "message" => "No update information",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        $category = $this->entityManager->getRepository('App\Entity\Category')->findOneBy(["eid" => $id]);
        if (!$category) {
            return [
                "response" => [
                    "message" => "Not found category",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        if (!empty($fields["title"])) {
            $category->setTitle($fields["title"]);
        }

        $errorsProduct = $this->validator->validate($category);
        // TODO: нормальное сообщение об ошибке без указания сущности и code UUID
        if (count($errorsProduct) > 0) {
            return [
                "response" => [
                    "message" => (string)$errorsProduct,
                ],
                "status" => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }

        $this->entityManager->flush();

        return [
            "response" => [
                "message" => "Category updated.",
                "id" => $category->getId()
            ],
            "status" => Response::HTTP_OK
        ];
    }

    public function read($id)
    {
        if (empty($id)) {
            return [
                "response" => [
                    "message" => "Not found parameter",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        $result = [];
        $category = $this->entityManager->getRepository('App\Entity\Category')
            ->find($id);
        if (!$category) {
            return [
                "response" => [
                    "message" => "Not found category",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }
        $result["title"] = $category->getTitle();
        foreach($category->getProducts() as $product) {
            $result["products"][] = $product->toArray();
        }

        return [
            "response" => [
                "message" => "Category found.",
                "result" => $result
            ],
            "status" => Response::HTTP_OK
        ];
    }

    public function delete($id)
    {
        if (empty($id)) {
            return [
                "response" => [
                    "message" => "Not found parameter",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        $category = $this->entityManager->getRepository('App\Entity\Category')->find($id);
        if (!$category) {
            return [
                "response" => [
                    "message" => "Not found category",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return [
            "response" => [
                "message" => "Category deleted."
            ],
            "status" => Response::HTTP_OK
        ];
    }
}