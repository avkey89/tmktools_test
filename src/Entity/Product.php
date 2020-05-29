<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=12)
     * @Assert\Length(
     *     min = 3,
     *     max = 12,
     *     minMessage = "Product's title must be at least 3 and no more than 12 characters",
     *     maxMessage = "Product's title must be at least 3 and no more than 12 characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $eid;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="products")
     * @ORM\JoinTable(name="product_category")
     */
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getEid(): ?int
    {
        return $this->eid;
    }

    public function setEid(?int $eid): self
    {
        $this->eid = $eid;

        return $this;
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category)
    {
        if ($this->categories->contains($category)) {
            return;
        }

        $this->categories[] = $category;
    }

    public function toArray() {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "price" => $this->getPrice(),
            "eid" => $this->getEid()
        ];
    }
}
