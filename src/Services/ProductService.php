<?php

namespace App\Services;


use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductService
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

        if (!empty($fields["eId"])) {
            $productExists = $this->entityManager->getRepository('App\Entity\Product')
                ->findOneBy(["eid" => $fields["eId"]]);
            if ($productExists) {
                return $this->update($fields["eId"], $fields);
                /*return [
                    "response" => [
                        "message" => "This product exists already.",
                    ],
                    "status" => Response::HTTP_BAD_REQUEST
                ];*/
            }
        }

        if (empty($fields["title"])) {
            return [
                "response" => [
                    "message" => "Parameter 'title' not found.",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        $product = new Product();
        $product->setTitle($fields["title"]);
        if (!empty($fields["price"])) {
            $price = is_float($fields["price"]) ? $fields["price"] : (float)$fields["price"];
            $product->setPrice($price);
        }
        if (!empty($fields["eId"])) {
            $product->setEid($fields["eId"]);
        }

        $errorCategory = [];
        if (!empty($fields["categoriesEId"])) {
            if (is_array($fields["categoriesEId"])) {
                foreach($fields["categoriesEId"] as $categoryEid) {
                    $category = $this->entityManager->getRepository('App\Entity\Category')
                        ->findOneBy(['eid' => $categoryEid]);
                    if($category != NULL) {
                        $product->addCategory($category);
                    } else {
                        $errorCategory[] = $categoryEid;
                    }
                }
            } else {
                $category = $this->entityManager->getRepository('App\Entity\Category')
                    ->findOneBy(['eid' => $fields["categoriesEId"]]);
                if($category != NULL) {
                    $product->addCategory($category);
                } else {
                    $errorCategory[] = $fields["categoriesEId"];
                }
            }

        }

        if (!empty($errorCategory)) {
            return [
                "response" => [
                    "message" => "This category ".implode(', ', $errorCategory)." isn't exists. Addition did not happen.",
                ],
                "status" => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }

        $errorsProduct = $this->validator->validate($product);
        // TODO: нормальное сообщение об ошибке без указания сущности и code UUID
        if (count($errorsProduct) > 0) {
            return [
                "response" => [
                    "message" => (string)$errorsProduct,
                ],
                "status" => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return [
            "response" => [
                "message" => "Product added.",
                "id" => $product->getId()
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

        $product = $this->entityManager->getRepository('App\Entity\Product')->findOneBy(["id" => $id]);
        if (!$product) {
            return [
                "response" => [
                    "message" => "Not found product",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }

        if (!empty($fields["title"])) {
            $product->setTitle($fields["title"]);
        }
        if (!empty($fields["price"])) {
            $price = is_float($fields["price"]) ? $fields["price"] : (float)$fields["price"];
            $product->setPrice($price);
        }

        $errorsProduct = $this->validator->validate($product);
        // TODO: нормальное сообщение об ошибке без указания сущности и code UUID
        if (count($errorsProduct) > 0) {
            return [
                "response" => [
                    "message" => (string)$errorsProduct,
                ],
                "status" => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }

        // TODO: обновление полное (не добавление ещё связи, а удаление старой и записи новой)
        $errorCategory = [];
        if (!empty($fields["categoriesEId"])) {
            if (is_array($fields["categoriesEId"])) {
                foreach($fields["categoriesEId"] as $categoryEid) {
                    $category = $this->entityManager->getRepository('App\Entity\Category')
                        ->findOneBy(['eid' => $categoryEid]);
                    if($category != NULL) {
                        $product->addCategory($category);
                    } else {
                        $errorCategory[] = $categoryEid;
                    }
                }
            } else {
                $category = $this->entityManager->getRepository('App\Entity\Category')
                    ->findOneBy(['eid' => $fields["categoriesEId"]]);
                if($category != NULL) {
                    $product->addCategory($category);
                } else {
                    $errorCategory[] = $fields["categoriesEId"];
                }
            }

        }

        if (!empty($errorCategory)) {
            return [
                "response" => [
                    "message" => "This category ".implode(', ', $errorCategory)." isn't exists. Addition did not happen.",
                ],
                "status" => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }

        $this->entityManager->flush();

        return [
            "response" => [
                "message" => "Product updated.",
                "id" => $product->getId()
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
        $product = $this->entityManager->getRepository('App\Entity\Product')
            ->find($id);
        if (!$product) {
            return [
                "response" => [
                    "message" => "Not found product",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }
        $result["title"] = $product->getTitle();
        $result["price"] = $product->getPrice();
        //var_dump((array)$product->getCategories()->toArray());
        foreach($product->getCategories() as $category) {
            $result["category"][] = $category->toArray();
        }

        return [
            "response" => [
                "message" => "Product found.",
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

        $product = $this->entityManager->getRepository('App\Entity\Product')->find($id);
        if (!$product) {
            return [
                "response" => [
                    "message" => "Not found product",
                ],
                "status" => Response::HTTP_BAD_REQUEST
            ];
        }
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return [
            "response" => [
                "message" => "Product deleted."
            ],
            "status" => Response::HTTP_OK
        ];
    }
}