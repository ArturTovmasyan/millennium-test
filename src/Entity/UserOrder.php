<?php

namespace App\Entity;

use App\Model\TimeAwareTrait;
use App\Repository\UserOrderRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserOrderRepository::class)]
class UserOrder
{
    use TimeAwareTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[Assert\NotBlank(message: 'The user cannot be blank.')]
    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'orders')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private User $user;

    #[Assert\NotBlank(message: 'The product cannot be blank.')]
    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'orders')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private Product $product;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }
}
