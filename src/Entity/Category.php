<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
     *     minMessage = "Category's title must be at least 3 and no more than 12 characters",
     *     maxMessage = "Category's title must be at least 3 and no more than 12 characters"
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $eid;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="categories")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getEid(): ?int
    {
        return $this->eid;
    }

    public function setEid(?int $eid): self
    {
        $this->eid = $eid;

        return $this;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function toArray() {
        return [
            "id" => $this->getId(),
            "title" => $this->getTitle(),
            "eid" => $this->getEid()
        ];
    }
}
